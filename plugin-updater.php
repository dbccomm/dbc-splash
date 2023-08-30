<?php
/**
 * DBC_Plugin_Updater
 *
 * Version: 1.1.1
 * Author: Bruno Laferrière
 * Author Email: blaferriere@dbc.ca
 *
 * A class for managing plugin updates from a GitHub repository. This class is designed to
 * work with WordPress and handles the entire update process, including checking for updates,
 * displaying update notifications, and managing the update process.
 *
 * Usage:
 *
 * 1. Include this class in your plugin's main PHP file.
 * 2. Create a new instance of the DBC_Plugin_Updater class, passing the required parameters
 *    (GitHub repository owner's username, repository name, access token (can be null for public repositories),
 *    plugin slug, and plugin basename) to the constructor.
 * 3. The class will automatically handle the update process, including checking for updates
 *    and managing the update process via WordPress hooks and filters.
 *
 * Example:
 *
 * require_once 'path/to/DBC_Plugin_Updater.php';
 *
 * $updater = new DBC_Plugin_Updater(
 *     'github-username/repository-name',
 *     'your-github-access-token' // Pass null if it's a public repository
 * );
 *
 * Note: Replace 'path/to/DBC_Plugin_Updater.php' with the actual path to the file containing
 * the DBC_Plugin_Updater class. Replace the other parameters with the appropriate values for
 * your plugin and GitHub repository. If it's a public repository, the access token can be set to null.
 */


// Plugin namespace
namespace dbccomm\dbcSplash;

// Block direct calls
if ( ! function_exists( 'add_action' ) ) {
        die;
}

class DBC_Plugin_Updater {

    // Properties
    private $owner;             // GitHub username of the plugin repository owner
    private $repo;              // GitHub repository name ( owner/plugin-folder )
    private $token;             // Access token for the GitHub API
    private $plugin_slug;       // The slug of the plugin
    private $plugin_basename;   // The plugin file basename
    private $tested;            // Tested version
    private $requires;          // Minimum wordpress version
    private $requires_php;      // Minimium requires PHP version

    /**
     * Constructor function for the DBC_Plugin_Updater class
     *
     * @param string $repo          GitHub username/repository name
     * @param string $token         Access token for the GitHub API
     */
     public function __construct($repo, $args = [], $token = null) {
         list($this->owner, $this->repo) = explode('/', $repo);
         $this->token = $token;
         $this->plugin_slug = $this->repo;
         $this->plugin_basename = $this->repo . '/' .$this->repo . '.php';

         // Set tested, requires, and requires_php values from args or default values
         $this->tested = isset($args['tested']) ? $args['tested'] : '6.3';
         $this->requires = isset($args['requires']) ? $args['requires'] : '6.3';
         $this->requires_php = isset($args['requires_php']) ? $args['requires_php'] : '8.2';

         // Add filters
         add_filter('pre_set_site_transient_update_plugins', [$this, 'check_for_updates']);
         add_filter('plugins_api', [$this, 'plugin_info'], 20, 3);
         add_filter('http_request_args', [$this, 'add_download_header'], 10, 2);
         add_filter('upgrader_source_selection', [$this, 'rename_github_zip'], 10, 3);
     }


    /**
     * Check for plugin updates and set update information
     *
     * @param object $transient     The current plugin update transient object
     * @return object               The modified plugin update transient object
     */
     public function check_for_updates($transient) {
         if (empty($transient->checked)) {
             return $transient;
         }

         // Get latest release info from GitHub
         $info = $this->get_repo_info();

         if (!empty($info)) {
             // Get plugin data
             $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $this->plugin_basename, false, false);

             // Check if update is available
             if (version_compare($plugin_data['Version'], $info->tag_name, '<')) {
                 $transient->response[$this->plugin_basename] = (object) [
                     'slug' => $this->plugin_slug,
                     'new_version' => $info->tag_name,
                     'url' => $info->html_url,
                     'package' => $info->zipball_url . ($this->token ? (strpos($info->zipball_url, '?') === false ? '?' : '&') . 'access_token=' . $this->token : ''),
                 ];
             }
         }

         return $transient;
     }

    /**
     * Add download header for authenticating with GitHub
     *
     * @param array $args       HTTP request arguments
     * @param string $url       URL for the HTTP request
     * @return array            Modified HTTP request arguments
     */
     public function add_download_header($args, $url) {
         if (strpos($url, 'api.github.com/repos/' . $this->owner . '/' . $this->repo . '/zipball/') !== false && $this->token) {
             $args['headers']['Authorization'] = 'token ' . $this->token;
         }
         return $args;
     }

    /**
    * Provide plugin information for the WordPress plugin details modal
    *
    * @param object $res Response object
    * @param string $action The type of information requested
    * @param object $args Arguments for the request
    * @return object Modified response object
    */
    public function plugin_info($res, $action, $args) {
        if ($action !== 'plugin_information' || $args->slug !== $this->plugin_slug) {
            return $res;
        }
        // Get latest release info from GitHub
        $info = $this->get_repo_info();

        if (!empty($info)) {
            // Generate plugin info
            $res = (object) [
                'name' => $info->name,
                'version' => $info->tag_name,
                'slug' => $this->plugin_slug,
                'download_link' => $info->zipball_url,
                'tested' => $this->tested,
                'requires' => $this->requires,
                'requires_php' => $this->requires_php,
                'author' => $info->author->login,
                'homepage' => $info->html_url,
                'sections' => [
                    'description' => $info->body,
                ],
            ];
        }

        return $res;
    }

    /**
     * Get repository information from GitHub
     *
     * @return object|null     The latest release info as an object, or null if the request fails
     */
     private function get_repo_info() {

         $transient_name = 'dbc_plugin_update_' . $this->repo;

         $info = get_transient($transient_name);

         // If transient data exists, return it
         if ($info) {
             return $info;
         }

         $url = 'https://api.github.com/repos/' . $this->owner . '/' . $this->repo . '/releases/latest';

         // Define Accept header
         $headers = ['Accept' => 'application/vnd.github+json'];

         // Include Authorization header if a token is provided
         if ($this->token) {
             $headers['Authorization'] = 'token ' . $this->token;
         }

         // Make HTTP request to GitHub API
         $response = wp_remote_get($url, ['headers' => $headers]);

         if (is_wp_error($response)) {
             return null;
         }

         // Extract response body
         $body = wp_remote_retrieve_body($response);
         if (empty($body)) {
             return null;
         }

         // Decode response body as JSON and return the result as an object
         $info = json_decode($body);
         set_transient($transient_name, $info, 12 * HOUR_IN_SECONDS); // Caching for 12 hours

         return $info;
     }


    /**
     * Rename the GitHub zip folder to match the plugin directory
     *
     * @param string $source            The current source location of the plugin zip file
     * @param string $remote_source     The remote source location of the plugin zip file
     * @param object $upgrader          The plugin upgrader object
     * @return string                   The modified source location of the plugin zip file
     */
    public function rename_github_zip($source, $remote_source, $upgrader) {
        global $wp_filesystem;

        // Get plugin directory
        $plugin_path = plugin_basename(__FILE__);
        $plugin_directory = dirname($plugin_path);

        // Check if source location contains plugin directory
        if (strpos($source, $plugin_directory) === false) {
            return $source;
        }

        // Rename zip folder to match plugin directory
        $corrected_source = trailingslashit($remote_source) . $plugin_directory . '/';
        if ($source !== $corrected_source) {
            $wp_filesystem->move($source, $corrected_source);
            $source = $corrected_source;
        }

        return $source;
    }

}
