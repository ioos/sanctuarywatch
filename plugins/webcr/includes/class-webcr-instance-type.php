<?php
/**
 * Register class that defines the Instance Type functions  
 * 
 */

 
include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-webcr-utility.php';
class Webcr_Instance_Type {

    // code version for instance
    function instance_settings_init() {
        // Register a new settings group
        register_setting('theme_settings_group', 'instance_settings');

        // Add a new section
        add_settings_section(
            'instance_settings_section',
            'Instance Settings',
            [$this, 'webcr_settings_section_callback'],
            'instance_settings'
        );
    }

    // Add menu item to WordPress admin
    function webcr_add_admin_menu() {
        add_menu_page(
            'Site Settings', // Page title
            'Site Settings', // Menu title
            'manage_options', // Capability required
            'theme_settings', // Menu slug
            [$this, 'webcr_settings_page'] // Function to display the page
        );
    }

    // Register settings
    function webcr_settings_init() {
        // Register a new settings group
        register_setting('theme_settings_group', 'webcr_settings');

        // Add a new section
        add_settings_section(
            'webcr_settings_section',
            'Theme Display',
            [$this, 'webcr_settings_section_callback'],
            'theme_settings'
        );

        // Add fields to the section
        add_settings_field(
            'intro_text',
            'Front Page Introduction',
            [$this, 'intro_text_field_callback'],
            'theme_settings',
            'webcr_settings_section'
        );

        add_settings_field(
            'footer_background',
            'Footer Background Color',
            [$this, 'footer_background_field_callback'],
            'theme_settings',
            'webcr_settings_section'
        );

        // Add a new section
        add_settings_section(
            'webcr_google_settings_section',
            'Google Analytics/Tags',
            [$this, 'webcr_settings_google_section_callback'],
            'theme_settings'
        );

        add_settings_field(
            'google_analytics_measurement_id',
            'Google Analytics Measurement ID',
            [$this, 'google_analytics_measurement_id_field_callback'],
            'theme_settings',
            'webcr_google_settings_section'
        );

        add_settings_field(
            'google_tags_container_id',
            'Google Tags Container ID',
            [$this, 'google_tags_container_id_field_callback'],
            'theme_settings',
            'webcr_google_settings_section'
        );
    }

    // Section callback
    function webcr_settings_section_callback() {
    //   echo '<p>Customize your theme\'s appearance and functionality.</p>';
    }
   
    // Field callbacks
    function intro_text_field_callback() {
        $options = get_option('webcr_settings');
        $value = isset($options['intro_text']) ? $options['intro_text'] : '';
        ?>
        <textarea name="webcr_settings[intro_text]" rows="5" cols="100"><?php echo esc_textarea($value); ?></textarea>
        <p class="description">This text will appear on your site's front page.</p>
        <?php
    }
   
    function multiple_instances_field_callback() {
        $options = get_option('webcr_settings');
        $value = isset($options['multiple_instances']) ? $options['multiple_instances'] : '0';
        ?>
        <input type="checkbox" name="webcr_settings[multiple_instances]" value="1" <?php checked('1', $value); ?>>
        <p class="description">Check this if your site has multiple instance types.</p>
        <?php
    }

    function footer_background_field_callback() {
        $options = get_option('webcr_settings');
        $value = isset($options['footer_background']) ? $options['footer_background'] : '#ffffff';
        ?>
        <input type="color" name="webcr_settings[footer_background]" value="<?php echo esc_attr($value); ?>">
        <p class="description">Choose the background color for your footer.</p>
        <?php
    }

    // Section callback
    function webcr_settings_google_section_callback() {
        //   echo '<p>Customize your theme\'s appearance and functionality.</p>';
        }


    function google_analytics_measurement_id_field_callback() {
        $options = get_option('webcr_settings');
        $value = isset($options['google_analytics_measurement_id']) ? $options['google_analytics_measurement_id'] : '';
        ?>
        <input type="text" name="webcr_settings[google_analytics_measurement_id]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="G-XXXXXXXXXXXX">
        <p class="description">
            Enter the Google Analytics Measurement ID for your site.
            <br>
            <a href="https://support.google.com/analytics/answer/9539598" target="_blank" rel="noopener noreferrer">Learn how to find your Measurement ID</a>.
        </p>
        <?php
    }

    function google_tags_container_id_field_callback() {
        $options = get_option('webcr_settings');
        $value = isset($options['google_tags_container_id']) ? $options['google_tags_container_id'] : '';
        $value_GTMContainer = isset($options['google_analytics_measurement_id']) ? $options['google_analytics_measurement_id'] : '';
        $example_container_json = 'example_google_container_tags.json';
        $example_folder = get_site_url() . '/wp-content/plugins/webcr/example_files/';
        $filedownload =  esc_url($example_folder . $example_container_json)

        ?>
        <input type="text" name="webcr_settings[google_tags_container_id]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="GTM-XXXXXXXX">
        <p class="description">
            Enter the Google Tags Container ID for your site.
            <br>
            <a href="https://support.google.com/tagmanager/answer/14847097?hl=en" target="_blank" rel="noopener noreferrer">Learn how to find your Container ID (2. Install a web container > Step 4)</a>.
            <br>
            <br>
            Enter both IDs above, then click below to download, then import this container into your Google Tag Manager instance.
            <br>
            <a href="#" id="downloadLink" target="" rel="noopener noreferrer">Download Container File</a>
            <br>
            <a href="https://support.google.com/tagmanager/answer/6106997" target="_blank" rel="noopener noreferrer">Learn how to import a container into Google Tag Manager</a>
            <br>
            <br>
            Be sure to click the "Save Changes" below.
            <br>
        </p>


        <script>
            document.getElementById('downloadLink').addEventListener('click', function (event) {
                event.preventDefault();  // Prevent the default link behavior

                // GA4 Measurement ID passed from PHP
                var gaMeasurementId = "<?php echo esc_js($value); ?>"; 

                // GA4 Measurement ID passed from PHP
                var gtmContainerId = "<?php echo esc_js($value_GTMContainer); ?>";


                // Fetch the GTM container JSON from the local server
                const rootURL = window.location.origin;
                const figureRestCall = `${rootURL}/wp-content/plugins/webcr/example_files/example_google_container_tags.json`;
                fetch(figureRestCall)  // Update with the correct path
                    .then(response => response.json())  // Parse JSON
                    .then(jsonData => {
                        // Loop through the tags and replace "G-EXAMPLE" with the dynamic GA Measurement ID
                        jsonData.containerVersion.tag.forEach(tag => {
                            tag.parameter.forEach(param => {
                                if (param.key === "tagId" && param.value === "G-EXAMPLE") {
                                    param.value = gaMeasurementId;  // Replace with the actual GA Measurement ID
                                }
                                if (param.key === "publicId" && param.value === "GTM-EXAMPLE") {
                                    param.value = gtmContainerId;  // Replace with the actual GA Measurement ID
                                }
                            });
                        });

                        // Loop through the tagIds array and replace "GTM-EXAMPLE" with gtmContainerId
                        jsonData.containerVersion.container.tagIds.forEach((tagId, index) => {
                            if (tagId === "GTM-EXAMPLE") {
                                jsonData.containerVersion.container.tagIds[index] = gtmContainerId;  // Replace with the GTM Container ID
                            }
                        });

                        // Create a Blob from the modified JSON data
                        const jsonString = JSON.stringify(jsonData, null, 2);  // Format JSON with indentation
                        const blob = new Blob([jsonString], { type: 'application/json' });

                        console.log(jsonString);
                        console.log(blob);

                        // Create a download link for the modified file
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'GTM-EXAMPLE-CONTAINER.json';  // Set the filename for the download

                        // Programmatically click the download link to trigger the download
                        a.click();

                        // Clean up the object URL after the download
                        URL.revokeObjectURL(url);
                    })
                    .catch(error => {
                        console.error("Error fetching the JSON file:", error);
                    });
            });
        </script>
        <?php
    }

    
   
   // Create the settings page
   function webcr_settings_page() {
       // Check user capabilities
       if (!current_user_can('manage_options')) {
           return;
       }
       ?>
       <div class="wrap">
           <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
           <form action="options.php" method="post">
               <?php
               settings_fields('theme_settings_group');
               do_settings_sections('theme_settings');
               submit_button();
               ?>
           </form>
       </div>
       <?php
   }
   
   // Optional: Add settings link on plugin page
   function add_settings_link($links) {
       $settings_link = '<a href="admin.php?page=theme_settings">' . __('Settings') . '</a>';
       array_push($links, $settings_link);
       return $links;
   }

    // Register the instance_type taxonomy if it doesn't exist
    function register_instance_type_taxonomy() {
        if (!taxonomy_exists('instance_type')) {
            register_taxonomy('instance_type', 'post', [
                'hierarchical' => false,
                'labels' => [
                    'name' => 'Instance Types',
                    'singular_name' => 'Instance Type',
                    'menu_name' => 'Instance Types',
                    'all_items' => 'All Instance Types',
                    'edit_item' => 'Edit Instance Type',
                    'view_item' => 'View Instance Type',
                    'update_item' => 'Update Instance Type',
                    'add_new_item' => 'Add New Instance Type',
                    'new_item_name' => 'New Instance Type Name',
                    'search_items' => 'Search Instance Types',
                ],
                'show_ui' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => ['slug' => 'instance-type'],
            ]);
        }
    }

    // Register the instance order meta field for the taxonomy
    function register_instance_type_order_meta() {
        register_meta('term', 'instance_order', [
            'type' => 'integer',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'absint',
        ]);
    }

    // Register the instance navbar name meta field for the taxonomy
    function register_instance_type_navbar_name_meta() {
        register_meta('term', 'navbar_name', [
            'type' => 'integer',
            'single' => true,
            'show_in_rest' => true,
        ]);
    }

    // Add the admin menu item
    function add_instance_type_admin_menu() {
        add_menu_page(
            'Manage Instance Types',
            'Instance Types',
            'manage_categories',
            'manage-instance-types',
            [$this, 'render_instance_type_admin_page'],
            'dashicons-category',
            20
        );
    }

    // Render the admin page
    function render_instance_type_admin_page() {
        // Check if taxonomy exists before proceeding
        if (!taxonomy_exists('instance_type')) {
            echo '<div class="error"><p>Error: The instance_type taxonomy is not properly registered.</p></div>';
            return;
        }

        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'add':
                        if (isset($_POST['term_name']) && isset($_POST['instance_order']) && isset($_POST['instance_navbar_name'])) {
                            $term_name = sanitize_text_field($_POST['term_name']);
                            $term_slug = sanitize_title($_POST['term_slug']);
                            $term_description = sanitize_textarea_field($_POST['term_description']);
                            $instance_order = absint($_POST['instance_order']);
                            $instance_navbar_name = sanitize_text_field($_POST['instance_navbar_name']);
                            
                            $args = array(
                                'slug' => $term_slug,
                                'description' => $term_description
                            );
                            
                            $term = wp_insert_term($term_name, 'instance_type', $args);
                            if (!is_wp_error($term)) {
                                update_term_meta($term['term_id'], 'instance_order', $instance_order);
                                update_term_meta($term['term_id'], 'instance_navbar_name', $instance_navbar_name);
                            }
                        }
                        break;
                        
                    case 'edit':
                        if (isset($_POST['term_id']) && isset($_POST['term_name']) && isset($_POST['instance_order']) && isset($_POST['instance_navbar_name'])) {
                            $term_id = absint($_POST['term_id']);
                            $term_name = sanitize_text_field($_POST['term_name']);
                            $term_slug = sanitize_title($_POST['term_slug']);
                            $term_description = sanitize_textarea_field($_POST['term_description']);
                            $instance_order = absint($_POST['instance_order']);
                            $instance_navbar_name = sanitize_text_field($_POST['instance_navbar_name']);

                            wp_update_term($term_id, 'instance_type', [
                                'name' => $term_name,
                                'slug' => $term_slug,
                                'description' => $term_description
                            ]);
                            update_term_meta($term_id, 'instance_order', $instance_order);
                            update_term_meta($term_id, 'instance_navbar_name', $instance_navbar_name);
                        }
                        break;
                        
                    case 'delete':
                        if (isset($_POST['term_id'])) {
                            $term_id = absint($_POST['term_id']);
                            wp_delete_term($term_id, 'instance_type');
                        }
                        break;
                }
            }
        }
        
        // Get all instance_type terms
        $terms = get_terms([
            'taxonomy' => 'instance_type',
            'hide_empty' => false,
        ]);

        // Check if we got an error
        if (is_wp_error($terms)) {
            echo '<div class="error"><p>Error retrieving terms: ' . esc_html($terms->get_error_message()) . '</p></div>';
            return;
        }

        // Convert terms to array if it's not already (for older WordPress versions)
        $terms = is_array($terms) ? $terms : array();
        ?>
        <div class="wrap">
            <h1>Manage Instance Types</h1>
            
            <!-- Add new term form -->
            <h2>Add New Instance Type</h2>
            <form method="post" action="">
                <input type="hidden" name="action" value="add">
                <table class="form-table">
                    <tr>
                        <th><label for="term_name">Name</label></th>
                        <td><input type="text" name="term_name" id="term_name" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="term_slug">Slug</label></th>
                        <td><input type="text" name="term_slug" id="term_slug" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="term_description">Description</label></th>
                        <td><textarea name="term_description" id="term_description" class="large-text" rows="5"></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="instance_order">Order</label></th>
                        <td><input type="number" name="instance_order" id="instance_order" class="small-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="instance_navbar_name">Navbar Name</label></th>
                        <td><input type="text" name="instance_navbar_name" id="instance_navbar_name" class="regular-text" required></td>
                    </tr>
                </table>
                <?php submit_button('Add New Instance Type'); ?>
            </form>
            
            <!-- List existing terms -->
            <h2>Existing Instance Types</h2>
            <?php if (empty($terms)): ?>
                <p>No instance types found.</p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Order</th>
                            <th>Navbar Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($terms as $term): 
                            // Ensure $term is a WP_Term object
                            if (!is_object($term) || !isset($term->term_id)) {
                                continue;
                            }
                            $instance_order = get_term_meta($term->term_id, 'instance_order', true); 
                            $instance_navbar_name = get_term_meta($term->term_id, 'instance_navbar_name', true); 
                        ?>
                            <tr>
                                <td><?php echo esc_html($term->name); ?></td>
                                <td><?php echo esc_html($term->slug); ?></td>
                                <td><?php echo esc_html($term->description); ?></td>
                                <td><?php echo esc_html($instance_order); ?></td>
                                <td><?php echo esc_html($instance_navbar_name); ?></td>
                                <td>
                                    <button type="button" class="button" 
                                        onclick="showEditForm(
                                            <?php echo esc_js($term->term_id); ?>,
                                            '<?php echo esc_js($term->name); ?>',
                                            '<?php echo esc_js($term->slug); ?>',
                                            '<?php echo esc_js($term->description); ?>',
                                            <?php echo esc_js($instance_order); ?>,
                                            '<?php echo esc_js($instance_navbar_name); ?>'
                                        )">
                                        Edit
                                    </button>
                                    <form method="post" action="" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="term_id" value="<?php echo esc_attr($term->term_id); ?>">
                                        <button type="submit" class="button" onclick="return confirm('Are you sure you want to delete this term?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            
            <!-- Edit form (hidden by default) -->
            <div id="edit-form" style="display: none;">
                <h2>Edit Instance Type</h2>
                <form method="post" action="">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="term_id" id="edit_term_id">
                    <table class="form-table">
                        <tr>
                            <th><label for="edit_term_name">Name</label></th>
                            <td><input type="text" name="term_name" id="edit_term_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="edit_term_slug">Slug</label></th>
                            <td><input type="text" name="term_slug" id="edit_term_slug" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label for="edit_term_description">Description</label></th>
                            <td><textarea name="term_description" id="edit_term_description" class="large-text" rows="5"></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="edit_instance_order">Order</label></th>
                            <td><input type="number" name="instance_order" id="edit_instance_order" class="small-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="edit_instance_navbar_name">Navbar Name</label></th>
                            <td><input type="text" name="instance_navbar_name" id="edit_instance_navbar_name" class="regular-text" required></td>
                        </tr>
                    </table>
                    <?php submit_button('Update Instance Type'); ?>
                </form>
            </div>
            
            <script>
                function showEditForm(termId, termName, termSlug, termDescription, termOrder, termNavbarName) {
                    document.getElementById('edit-form').style.display = 'block';
                    document.getElementById('edit_term_id').value = termId;
                    document.getElementById('edit_term_name').value = termName;
                    document.getElementById('edit_term_slug').value = termSlug;
                    document.getElementById('edit_term_description').value = termDescription;
                    document.getElementById('edit_instance_order').value = termOrder;
                    document.getElementById('edit_instance_navbar_name').value = termNavbarName;
                }
            </script>

        </div>
        <?php
    }

}

