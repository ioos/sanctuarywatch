<?php
/**
 * GitHub Updater
 * 
 * Enables WordPress plugins and themes to update from GitHub.
 */
class GitHub_Updater {
    private $slug;
    private $plugin_data;
    private $username;
    private $repository;
    private $github_response;
    private $plugin_file;
    private $is_theme = false;
    private $subdir_path;
    
    public function __construct($file, $github_username, $github_repo, $is_theme = false, $subdir_path = '') {
        $this->is_theme = $is_theme;
        $this->subdir_path = $subdir_path; // Path within repository (e.g., 'plugins/webcr' or 'themes/Sanctuary_Watch')
        
        if ($is_theme) {
            $this->slug = basename(dirname($file));
        } else {
            $this->plugin_file = $file;
            $this->slug = plugin_basename($file);
            
            add_filter('plugins_api', array($this, 'plugin_info'), 20, 3);
            add_filter('site_transient_update_plugins', array($this, 'update_state'));
            add_filter('upgrader_post_install', array($this, 'after_install'), 10, 3);
        }
        
        $this->username = $github_username;
        $this->repository = $github_repo;
        
        if ($is_theme) {
            add_filter('site_transient_update_themes', array($this, 'theme_update_state'));
            add_filter('upgrader_post_install', array($this, 'after_theme_install'), 10, 3);
        }
        
        add_action('admin_init', array($this, 'set_plugin_properties'));
    }
    
    public function set_plugin_properties() {
        if (!$this->is_theme) {
            if (!function_exists('get_plugin_data')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            
            $this->plugin_data = get_plugin_data($this->plugin_file);
        } else {
            $theme = wp_get_theme($this->slug);
            $this->plugin_data = array(
                'Name' => $theme->get('Name'),
                'Version' => $theme->get('Version'),
                'ThemeURI' => $theme->get('ThemeURI'),
                'Description' => $theme->get('Description'),
                'Author' => $theme->get('Author'),
                'AuthorURI' => $theme->get('AuthorURI'),
            );
        }
    }
    
    private function get_repository_info() {
        if (is_null($this->github_response)) {
            // First try to get the latest release
            $request_uri = sprintf('https://api.github.com/repos/%s/%s/releases/latest', $this->username, $this->repository);
            $response = wp_remote_get($request_uri, array(
                'headers' => array(
                    'Accept' => 'application/vnd.github.v3+json',
                    'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url')
                )
            ));
            
            // Check if the request was successful
            if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
                // If no release is found, try getting the main branch info instead
                $request_uri = sprintf('https://api.github.com/repos/%s/%s/commits/main', $this->username, $this->repository);
                $response = wp_remote_get($request_uri, array(
                    'headers' => array(
                        'Accept' => 'application/vnd.github.v3+json',
                        'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url')
                    )
                ));
                
                if (!is_wp_error($response) && 200 === wp_remote_retrieve_response_code($response)) {
                    $commit_data = json_decode(wp_remote_retrieve_body($response), true);
                    
                    // If we have commit data, create a simulated release response
                    if (is_array($commit_data) && isset($commit_data['sha'])) {
                        // Get the current version from the plugin/theme
                        $current_version = $this->is_theme ? 
                            wp_get_theme($this->slug)->get('Version') : 
                            $this->plugin_data['Version'];
                        
                        // Increment version number for the update (this is a fallback when no release exists)
                        $version_parts = explode('.', $current_version);
                        $version_parts[count($version_parts) - 1]++;
                        $new_version = implode('.', $version_parts);
                        
                        // Create a simulated release structure
                        $this->github_response = array(
                            'tag_name' => $new_version,
                            'published_at' => date('Y-m-d H:i:s', strtotime($commit_data['commit']['author']['date'])),
                            'body' => $commit_data['commit']['message'],
                            'html_url' => sprintf('https://github.com/%s/%s/commit/%s', 
                                       $this->username, $this->repository, $commit_data['sha']),
                            'zipball_url' => sprintf('https://github.com/%s/%s/archive/refs/heads/main.zip',
                                       $this->username, $this->repository)
                        );
                    }
                }
            } else {
                // We got a release, use that
                $release_data = json_decode(wp_remote_retrieve_body($response), true);
                if (is_array($release_data)) {
                    $this->github_response = $release_data;
                }
            }
            
            // Log error for debugging
            if (is_null($this->github_response)) {
                error_log('GitHub Updater: Failed to get repository info for ' . $this->username . '/' . $this->repository);
            }
        }
    }
    
    public function update_state($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        $this->get_repository_info();
        
        if (!is_array($this->github_response)) {
            return $transient;
        }
        
        $current_version = $this->is_theme ? 
            $transient->checked[$this->slug] : 
            $this->plugin_data['Version'];
        
        // Check if a new version is available
        if (version_compare($this->github_response['tag_name'], $current_version, '>')) {
            $package = $this->get_download_url();
            
            if (!empty($package)) {
                if ($this->is_theme) {
                    $transient->response[$this->slug] = array(
                        'new_version' => $this->github_response['tag_name'],
                        'package' => $package,
                        'url' => $this->github_response['html_url']
                    );
                } else {
                    $obj = new stdClass();
                    $obj->slug = $this->slug;
                    $obj->new_version = $this->github_response['tag_name'];
                    $obj->url = isset($this->plugin_data['PluginURI']) ? $this->plugin_data['PluginURI'] : 
                          sprintf('https://github.com/%s/%s', $this->username, $this->repository);
                    $obj->package = $package;
                    $transient->response[$this->slug] = $obj;
                }
            }
        }
        
        return $transient;
    }
    
    // Alias for theme updates
    public function theme_update_state($transient) {
        return $this->update_state($transient);
    }
    
    public function plugin_info($false, $action, $response) {
        if (!isset($response->slug) || $response->slug != $this->slug) {
            return $false;
        }
        
        $this->get_repository_info();
        
        if (!is_array($this->github_response)) {
            return $false;
        }
        
        $response->slug = $this->slug;
        $response->plugin_name = $this->plugin_data['Name'];
        $response->version = $this->github_response['tag_name'];
        $response->author = isset($this->plugin_data['Author']) ? $this->plugin_data['Author'] : '';
        $response->homepage = isset($this->plugin_data['PluginURI']) ? $this->plugin_data['PluginURI'] : 
                       sprintf('https://github.com/%s/%s', $this->username, $this->repository);
        $response->requires = isset($this->plugin_data['RequiresWP']) ? $this->plugin_data['RequiresWP'] : '';
        $response->requires_php = isset($this->plugin_data['RequiresPHP']) ? $this->plugin_data['RequiresPHP'] : '';
        
        $response->downloaded = 0;
        $response->last_updated = $this->github_response['published_at'];
        $response->sections = array(
            'description' => isset($this->plugin_data['Description']) ? $this->plugin_data['Description'] : '',
            'changelog' => $this->github_response['body']
        );
        $response->download_link = $this->get_download_url();
        
        return $response;
    }
    
    private function get_download_url() {
        // First check if there's a dedicated release asset for our component
        $asset_name = $this->is_theme ? 'Sanctuary_Watch.zip' : 'webcr.zip';
            
        if (isset($this->github_response['assets']) && is_array($this->github_response['assets'])) {
            foreach ($this->github_response['assets'] as $asset) {
                if (strpos($asset['name'], $asset_name) !== false) {
                    return $asset['browser_download_url'];
                }
            }
        }
        
        // If no specific asset found, create a custom URL to download only the subdirectory
        // Note: For this to work, we need a server-side handler or GitHub action
        
        // Fallback to the full repository zipball
        return $this->github_response['zipball_url'];
    }
    
    // For plugins
    public function after_install($response, $hook_extra, $result) {
        if (!$this->is_theme && isset($hook_extra['plugin'])) {
            global $wp_filesystem;
            
            // Check if this is our plugin
            if ($hook_extra['plugin'] === $this->slug) {
                $proper_destination = plugin_dir_path($this->plugin_file);
                $wp_filesystem->move($result['destination'], $proper_destination);
                $result['destination'] = $proper_destination;
                
                // If we need to move from a subdirectory (e.g., if the zip contains the repo structure)
                $subdir = trailingslashit($proper_destination) . basename($this->repository) . '-*/' . $this->subdir_path;
                $subdir_files = glob($subdir);
                
                if (!empty($subdir_files) && is_dir($subdir_files[0])) {
                    // Move from subdirectory to the proper plugin location
                    $this->recursive_copy($subdir_files[0], $proper_destination);
                    $this->recursive_remove_directory(dirname($subdir_files[0]));
                }
            }
        }
        
        return $result;
    }
    
    // For themes
    public function after_theme_install($response, $hook_extra, $result) {
        if ($this->is_theme && isset($hook_extra['theme'])) {
            global $wp_filesystem;
            
            // Check if this is our theme
            if ($hook_extra['theme'] === $this->slug) {
                $proper_destination = get_theme_root() . '/' . $this->slug;
                
                // If we need to move from a subdirectory (e.g., if the zip contains the repo structure)
                $subdir = trailingslashit($result['destination']) . basename($this->repository) . '-*/' . $this->subdir_path;
                $subdir_files = glob($subdir);
                
                if (!empty($subdir_files) && is_dir($subdir_files[0])) {
                    // Move from subdirectory to the proper theme location
                    $this->recursive_copy($subdir_files[0], $proper_destination);
                    
                    // Clean up
                    $wp_filesystem->delete($result['destination'], true);
                    $result['destination'] = $proper_destination;
                }
            }
        }
        
        return $result;
    }
    
    // Helper function to recursively copy directories
    private function recursive_copy($src, $dst) {
        global $wp_filesystem;
        
        if (!is_dir($src)) return false;
        
        $dir = opendir($src);
        if (!$dir) return false;
        
        if (!is_dir($dst)) {
            wp_mkdir_p($dst);
        }
        
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($src . '/' . $file)) {
                    $this->recursive_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    $wp_filesystem->copy($src . '/' . $file, $dst . '/' . $file, true);
                }
            }
        }
        
        closedir($dir);
        return true;
    }
    
    // Helper function to recursively remove a directory
    private function recursive_remove_directory($directory) {
        global $wp_filesystem;
        
        // Extra safety check to prevent accidentally deleting important directories
        if (empty($directory) || $directory == '/' || $directory == ABSPATH) {
            return false;
        }
        
        return $wp_filesystem->delete($directory, true);
    }
}