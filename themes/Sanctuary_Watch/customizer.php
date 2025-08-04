<?php
/**
 * Register class that defines the Customizer settings for the theme
 * 
 */

class Customizer_Settings {
    /**
     * Adds theme customizer options for the site.
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     */
    function sanctuary_watch_customize_register( $wp_customize ) {

        // Add Header Row Section
        $wp_customize->add_section('header_row_section', array(
            'title'       => __('Header Row', 'textdomain'),
            'description' => __('Configure the header row settings.', 'textdomain'),
            'priority'    => 30,
        ));
        
        // Add setting for header row enable/disable
        $wp_customize->add_setting('header_row_enable', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));
        
        // Add control for header row enable/disable
        $wp_customize->add_control('header_row_enable', array(
            'label'       => __('Enable Header Row', 'textdomain'),
            'description' => __('Check to display a header row above the main header.', 'textdomain'),
            'section'     => 'header_row_section',
            'type'        => 'checkbox',
            'priority'    => 10,
        ));
        
        // Add setting for header row background color
        $wp_customize->add_setting('header_row_bg_color', array(
            'default'           => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ));
        
        // Add control for header row background color
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'header_row_bg_color', array(
            'label'           => __('Header Row Background Color', 'textdomain'),
            'description'     => __('Choose the background color for the header row.', 'textdomain'),
            'section'         => 'header_row_section',
            'priority'        => 20,
            'active_callback' => [$this, 'is_header_row_enabled'],
        )));
        
        // Add setting for header image
        $wp_customize->add_setting('header_row_image', array(
            'default'           => $this->get_header_row_default_image_id(),
            'sanitize_callback' => [$this, 'header_row_sanitize_image'],
            'transport'         => 'refresh',
        ));
        
        // Add control for header image
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'header_row_image', array(
            'label'           => __('Header Image', 'textdomain'),
            'description'     => __('Upload an image that is exactly 433px wide and 50px tall.', 'textdomain'),
            'section'         => 'header_row_section',
            'mime_type'       => 'image',
            'priority'        => 30,
            'active_callback' => [$this, 'is_header_row_enabled'],
        )));

        // Add setting for header image alt text
        $wp_customize->add_setting('header_row_image_alt', array(
            'default'           => 'IOOS',
            'sanitize_callback' => [$this, 'header_row_sanitize_alt_text'],
            'transport'         => 'refresh',
        ));
        
        // Add control for header image alt text
        $wp_customize->add_control('header_row_image_alt', array(
            'label'           => __('Header Image Alt Text', 'textdomain'),
            'description'     => __('Alternative text for the header image (required for accessibility).', 'textdomain'),
            'section'         => 'header_row_section',
            'type'            => 'text',
            'priority'        => 40,
            'active_callback' => [$this, 'is_header_row_enabled'],
        ));
        
        // Add setting for header image link
        $wp_customize->add_setting('header_row_image_link', array(
            'default'           => 'https://ioos.us/',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ));
        
        // Add control for header image link
        $wp_customize->add_control('header_row_image_link', array(
            'label'           => __('Header Image Link', 'textdomain'),
            'description'     => __('URL that the header image should link to.', 'textdomain'),
            'section'         => 'header_row_section',
            'type'            => 'url',
            'priority'        => 50,
            'active_callback' => [$this, 'is_header_row_enabled'],
        ));
        
        // Add setting for header name within breadcrumb row
        $wp_customize->add_setting('header_row_breadcrumb_name', array(
            'default'           => 'IOOS',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));
        
        // Add control for header name within breadcrumb row
        $wp_customize->add_control('header_row_breadcrumb_name', array(
            'label'           => __('Header Name Within Breadcrumb Row', 'textdomain'),
            'description'     => __('Text to display in the breadcrumb navigation for this header.', 'textdomain'),
            'section'         => 'header_row_section',
            'type'            => 'text',
            'priority'        => 60,
            'active_callback' => [$this, 'is_header_row_enabled'],
        ));

        // Add a new section for Breadcrumb settings
        $wp_customize->add_section( 'breadcrumb_settings', array(
            'title'    => __( 'Breadcrumb Colors', 'sanctuary-watch' ),
            'priority' => 30,
        ) );

        // Add setting for breadcrumb background color
        $wp_customize->add_setting( 'breadcrumb_background_color', array(
            'default'   => '#008da8',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );

        // Add control for breadcrumb background color
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'breadcrumb_background_color_control', array(
            'label'    => __( 'Background Color', 'sanctuary-watch' ),
            'section'  => 'breadcrumb_settings',
            'settings' => 'breadcrumb_background_color',
        ) ) );

        // Add setting for breadcrumb text color
        $wp_customize->add_setting( 'breadcrumb_text_color', array(
            'default'   => '#ffffff',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );

        // Add control for breadcrumb text color
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'breadcrumb_text_color_control', array(
            'label'    => __( 'Text Color', 'sanctuary-watch' ),
            'section'  => 'breadcrumb_settings',
            'settings' => 'breadcrumb_text_color',
        ) ) );

        // Add a new section for Navigation Bar settings
        $wp_customize->add_section( 'navbar_settings', array(
            'title'    => __( 'Navigation Bar Colors', 'sanctuary-watch' ),
            'priority' => 35,
        ) );

        // Add setting for Navigation Bar background color
        $wp_customize->add_setting( 'navbar_background_color', array(
            'default'   => '#03386c',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );

        // Add control for Navigation Bar background color
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'navbar_background_color_control', array(
            'label'    => __( 'Background Color', 'sanctuary-watch' ),
            'section'  => 'navbar_settings',
            'settings' => 'navbar_background_color',
        ) ) );

        // Add setting for Navigation Bar text color
        $wp_customize->add_setting( 'navbar_text_color', array(
            'default'   => '#ffffff',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );

        // Add control for Navigation Bar text color
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'navbar_text_color_control', array(
            'label'    => __( 'Text Color', 'sanctuary-watch' ),
            'section'  => 'navbar_settings',
            'settings' => 'navbar_text_color',
        ) ) );

        // Add a new section for Footer settings
        $wp_customize->add_section( 'footer_settings', array(
            'title'    => __( 'Footer Colors', 'sanctuary-watch' ),
            'priority' => 40,
        ) );

        // Add setting for Footer background color
        $wp_customize->add_setting( 'footer_background_color', array(
            'default'   => '#03386c',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );

        // Add control for Footer background color
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_background_color_control', array(
            'label'    => __( 'Background Color', 'sanctuary-watch' ),
            'section'  => 'footer_settings',
            'settings' => 'footer_background_color',
        ) ) );

        // Add setting for Footer text color
        $wp_customize->add_setting( 'footer_text_color', array(
            'default'   => '#ffffff',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );

        // Add control for Footer text color
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_text_color_control', array(
            'label'    => __( 'Text Color', 'sanctuary-watch' ),
            'section'  => 'footer_settings',
            'settings' => 'footer_text_color',
        ) ) );

    }

    /**
     * Get the default header image attachment ID
     * This function checks if the default image exists in the media library
     * and returns its attachment ID, or 0 if not found
     */
    function get_header_row_default_image_id() {
        static $default_image_id = null;
        
        if ($default_image_id === null) {
            $default_image_path = get_template_directory() . '/assets/images/IOOS_Emblem_Tertiary_B_RGB.png';
            $default_image_url = get_template_directory_uri() . '/assets/images/IOOS_Emblem_Tertiary_B_RGB.png';
            
            // Check if image exists in filesystem
            if (file_exists($default_image_path)) {
                // Try to find this image in the media library
                $attachment = get_posts(array(
                    'post_type' => 'attachment',
                    'meta_query' => array(
                        array(
                            'key' => '_wp_attached_file',
                            'value' => basename($default_image_path),
                            'compare' => 'LIKE'
                        )
                    ),
                    'posts_per_page' => 1
                ));
                
                if (!empty($attachment)) {
                    $default_image_id = $attachment[0]->ID;
                } else {
                    // If not in media library, try to add it
                    $default_image_id = $this->add_default_header_image_to_media_library();
                }
            } else {
                $default_image_id = 0;
            }
        }
        
        return $default_image_id;
    }

    /**
     * Add the default header image to the media library
     */
    function add_default_header_image_to_media_library() {
        $default_image_path = get_template_directory() . '/assets/images/IOOS_Emblem_Tertiary_B_RGB.png';
        $default_image_url = get_template_directory_uri() . '/assets/images/IOOS_Emblem_Tertiary_B_RGB.png';
        
        if (!file_exists($default_image_path)) {
            return 0;
        }
        
        // Check if already exists
        $existing = get_posts(array(
            'post_type' => 'attachment',
            'meta_query' => array(
                array(
                    'key' => '_wp_attached_file',
                    'value' => basename($default_image_path),
                    'compare' => 'LIKE'
                )
            ),
            'posts_per_page' => 1
        ));
        
        if (!empty($existing)) {
            return $existing[0]->ID;
        }
        
        // Include WordPress file handling functions
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        if (!function_exists('wp_generate_attachment_metadata')) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
        }
        if (!function_exists('media_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/media.php');
        }
        
        // Copy file to uploads directory
        $upload_dir = wp_upload_dir();
        $filename = basename($default_image_path);
        $new_file_path = $upload_dir['path'] . '/' . $filename;
        
        // Only copy if it doesn't already exist in uploads
        if (!file_exists($new_file_path)) {
            if (!copy($default_image_path, $new_file_path)) {
                return 0;
            }
        }
        
        // Create attachment
        $attachment = array(
            'guid' => $upload_dir['url'] . '/' . $filename,
            'post_mime_type' => 'image/png',
            'post_title' => 'IOOS Header Image',
            'post_content' => '',
            'post_status' => 'inherit'
        );
        
        $attachment_id = wp_insert_attachment($attachment, $new_file_path);
        
        if (!is_wp_error($attachment_id)) {
            // Generate metadata
            $attachment_data = wp_generate_attachment_metadata($attachment_id, $new_file_path);
            wp_update_attachment_metadata($attachment_id, $attachment_data);
            
            return $attachment_id;
        }
        
        return 0;
    }

    /**
     * Active callback to show/hide header row controls when header row is enabled
     */
    function is_header_row_enabled($control) {
        $value = $control->manager->get_setting('header_row_enable')->value();
        if ($value == 1){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add inline JavaScript to control the visibility of header row settings
     */
    function header_row_customizer_inline_script() {
        ?>
        <script type="text/javascript">
        (function() {
            wp.customize.bind('ready', function() {
                // List of controls that should be hidden/shown based on header row enable
                var dependentControls = [
                    'header_row_bg_color',
                    'header_row_image', 
                    'header_row_image_alt',
                    'header_row_image_link',
                    'header_row_breadcrumb_name'
                ];
                
                // Function to slide down (show) an element
                function slideDown(element, duration) {
                    duration = duration || 300;
                    element.style.display = 'block';
                    element.style.height = '0px';
                    element.style.overflow = 'hidden';
                    element.style.transition = 'height ' + duration + 'ms ease-out';
                    
                    var height = element.scrollHeight + 'px';
                    element.style.height = height;
                    
                    setTimeout(function() {
                        element.style.height = '';
                        element.style.overflow = '';
                        element.style.transition = '';
                    }, duration);
                }
                
                // Function to slide up (hide) an element
                function slideUp(element, duration) {
                    duration = duration || 300;
                    element.style.height = element.offsetHeight + 'px';
                    element.style.overflow = 'hidden';
                    element.style.transition = 'height ' + duration + 'ms ease-out';
                    
                    setTimeout(function() {
                        element.style.height = '0px';
                    }, 10);
                    
                    setTimeout(function() {
                        element.style.display = 'none';
                        element.style.height = '';
                        element.style.overflow = '';
                        element.style.transition = '';
                    }, duration);
                }
                
                // Function to toggle control visibility
                function toggleHeaderRowControls(enabled) {
                    dependentControls.forEach(function(controlId) {
                        var control = wp.customize.control(controlId);
                        if (control) {
                            var container = control.container[0]; // Get DOM element from jQuery object
                            if (enabled) {
                                slideDown(container);
                            } else {
                                slideUp(container);
                            }
                        }
                    });
                }
                
                // Get the header row enable control
                var headerRowEnable = wp.customize.control('header_row_enable');
                
                if (headerRowEnable) {
                    // Set initial state
                    var initialValue = wp.customize('header_row_enable').get();
                    toggleHeaderRowControls(!!initialValue);
                    
                    // Listen for changes to the checkbox
                    wp.customize('header_row_enable', function(setting) {
                        setting.bind(function(value) {
                            toggleHeaderRowControls(!!value);
                        });
                    });
                }
            });
        })();
        </script>
        <?php
    }

    /**
     * Outputs custom CSS from the Theme Customizer for the breadcrumb.
     */
    function sanctuary_watch_customizer_css() {
        ?>
        <style type="text/css">
            #ioos-breadcrumb {
                background-color: <?php echo esc_attr( get_theme_mod( 'breadcrumb_background_color', '#008da8' ) ); ?>;
                color: <?php echo esc_attr( get_theme_mod( 'breadcrumb_text_color', '#ffffff' ) ); ?>;
            }
            #ioos-breadcrumb a, #ioos-breadcrumb p {
                color: <?php echo esc_attr( get_theme_mod( 'breadcrumb_text_color', '#ffffff' ) ); ?>;
            }
            #navbar-inner {
                background-color: <?php echo esc_attr( get_theme_mod( 'navbar_background_color', '#03386c' ) ); ?>;
            }

            .navbar-brand, .nav-link {
                color: <?php echo esc_attr( get_theme_mod( 'navbar_text_color', '#ffffff' ) ); ?>;
            }

            .site-footer {
                background-color: <?php echo esc_attr( get_theme_mod( 'footer_background_color', '#03386c' ) ); ?>;
            }

            .footer-column-title, .footer_component {
                color: <?php echo esc_attr( get_theme_mod( 'footer_text_color', '#ffffff' ) ); ?>;

            }

        </style>
        <?php
    }

    /**
     * Helper function to check if header row is enabled 
     */
    function is_header_row_active() {
        return get_theme_mod('header_row_enable', false);
    }

    /**
     * Helper function to get header row image URL 
     */
    function get_header_row_image() {
        return get_theme_mod('header_row_image', get_template_directory_uri() . '/assets/images/IOOS_Emblem_Tertiary_B_RGB.png');
    }

    /**
     * Helper function to get header row image alt text 
     */
    function get_header_row_image_alt() {
        return get_theme_mod('header_row_image_alt', 'IOOS');
    }

    /**
     * Helper function to get header row image link 
     */
    function get_header_row_image_link() {
        return get_theme_mod('header_row_image_link', 'https://ioos.us/');
    }

    /**
     * Helper function to get header row breadcrumb name 
     */
    function get_header_row_breadcrumb_name() {
        return get_theme_mod('header_row_breadcrumb_name', 'IOOS');
    }

    /**
     * Add validation notice in customizer if fields are empty
     */
    function header_row_validation_notice($wp_customize) {
        if (!$this->validate_header_row_settings() && $this->is_header_row_active()) {
            $wp_customize->add_setting('header_row_validation_notice', array(
                'sanitize_callback' => 'wp_kses_post',
            ));
            
            $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'header_row_validation_notice', array(
                'label'           => '',
                'description'     => '<div style="background: #dc3232; color: white; padding: 10px; border-radius: 3px; margin: 10px 0;"><strong>Warning:</strong> All header row fields must be filled when header row is enabled.</div>',
                'section'         => 'header_row_section',
                'type'            => 'hidden',
                'priority'        => 5,
                'active_callback' => 'is_header_row_enabled',
            )));
        }
    }

    /**
     * Validate header row settings to ensure no fields are blank when enabled
     */
    function validate_header_row_settings() {
        if ($this->is_header_row_active()) {
            $image = $this->get_header_row_image();
            $alt_text = $this->get_header_row_image_alt();
            $link = $this->get_header_row_image_link();
            $breadcrumb_name = $this->get_header_row_breadcrumb_name();
            
            // Check if any required field is empty
            if (empty($image) || empty($alt_text) || empty($link) || empty($breadcrumb_name)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Remove specific sections and panels from the WordPress Customizer.
     *
     * This function removes the Menus panel, Additional CSS section, and Homepage 
     * Settings section from the WordPress Customizer interface to streamline the 
     * customization options available to users.
     *
     * @since 1.0.0
     *
     * @param WP_Customize_Manager $wp_customize The WordPress Customizer Manager object.
     *                                          Contains methods for adding and removing
     *                                          customizer panels, sections, and controls.
     *
     * @return void
     */
    function remove_customizer_sections( $wp_customize ) {
        // Remove Menus panel
        $wp_customize->remove_panel( 'nav_menus' );
        
        // Remove Additional CSS section
        $wp_customize->remove_section( 'custom_css' );
        
        // Remove Homepage Settings section
        $wp_customize->remove_section( 'static_front_page' );
    }

}