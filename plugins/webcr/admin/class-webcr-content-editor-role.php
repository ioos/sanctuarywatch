<?php
/**
 * Content Editor and Manager Role Implementation
 * 
 * This file creates custom user roles and limits available roles to:
 * Content Editor, Content Manager, and Administrator (in that order)
 */

// Don't allow direct access
if (!defined('ABSPATH')) {
    exit;
}

class WEBCR_Custom_Roles {
    
    /**
     * Initialize the class
     */
    public function __construct() {
        // Create custom roles on plugin activation
        add_action('init', array($this, 'create_custom_roles'));
        
        // Add meta boxes to the user edit screen
        add_action('show_user_profile', array($this, 'add_instance_selection_fields'));
        add_action('edit_user_profile', array($this, 'add_instance_selection_fields'));
        
        // Save the selected instances when the user is updated
        add_action('personal_options_update', array($this, 'save_instance_selections'));
        add_action('edit_user_profile_update', array($this, 'save_instance_selections'));
        
        // Filter the available roles in the dropdown
        add_filter('editable_roles', array($this, 'filter_user_roles'));
        
        // Direct manipulation of the role dropdown output
        add_action('admin_footer-user-new.php', array($this, 'reorder_roles_js'));
        add_action('admin_footer-user-edit.php', array($this, 'reorder_roles_js'));
        add_action('admin_footer-profile.php', array($this, 'reorder_roles_js'));

        // Filter admin list queries for scenes
        add_action('pre_get_posts', array($this, 'webcr_restrict_scene_listing'));
    }
    
    // Filter admin list queries for scenes
    public function webcr_restrict_scene_listing($query) {
        global $pagenow;
        
        // Only filter in admin, when viewing scene list, and for content editors
        if (is_admin() && $pagenow == 'edit.php' && 
            isset($_GET['post_type']) && $_GET['post_type'] == 'scene' && 
            current_user_can('content_editor') && !current_user_can('administrator')) {
            
            // Get the current user
            $current_user = wp_get_current_user();
            
            // Get instances associated with this content editor
            $user_instances = get_user_meta($current_user->ID, 'associated_instances', true);
            
            // If we have associated instances
            if (!empty($user_instances) && is_array($user_instances)) {
                // Set up meta query to only show scenes from these instances
                $query->set('meta_query', array(
                    array(
                        'key' => 'scene_instance',
                        'value' => $user_instances,
                        'compare' => 'IN',
                    )
                ));
            } else {
                // If no instances are associated, show no scenes (safer than showing all)
                $query->set('post__in', array(0)); // This ensures no posts will be found
            }
        }
    }


    /**
     * Create the custom roles if they don't exist
     */
    public function create_custom_roles() {
        // Get the capabilities of the editor role
        $editor_role = get_role('editor');
        $editor_capabilities = $editor_role ? $editor_role->capabilities : array();
        
        // Create the Content Editor role
        if (!get_role('content_editor')) {
            add_role(
                'content_editor',
                __('Content Editor', 'webcr'),
                $editor_capabilities
            );
        }
        
        // Create the Content Manager role (with slightly higher capabilities)
        if (!get_role('content_manager')) {
            // Get admin capabilities but remove some sensitive ones
//            $admin_role = get_role('administrator');
            $manager_capabilities = $editor_role->capabilities;
            
            // Remove capabilities that should be reserved for administrators
            $restricted_caps = array(
                'install_plugins', 'activate_plugins', 'delete_plugins', 'edit_plugins',
                'install_themes', 'switch_themes', 'edit_themes', 'delete_themes',
                'update_core', 'update_plugins', 'update_themes',
                'manage_options', 'manage_sites'
            );
            
            foreach ($restricted_caps as $cap) {
                if (isset($manager_capabilities[$cap])) {
                    unset($manager_capabilities[$cap]);
                }
            }
            
            add_role(
                'content_manager',
                __('Content Manager', 'webcr'),
                $manager_capabilities
            );
        }
        
        // Remove default WordPress roles
        $roles_to_remove = array('subscriber', 'contributor', 'author', 'editor');
        foreach ($roles_to_remove as $role) {
            if (get_role($role)) {
                remove_role($role);
            }
        }
    }
    
    /**
     * Filter the available user roles
     * 
     * @param array $roles The array of role objects
     * @return array The filtered roles
     */
    public function filter_user_roles($roles) {
        // Only keep our custom roles and administrator
        $allowed_roles = array('content_editor', 'content_manager', 'administrator');
        
        foreach ($roles as $role => $details) {
            if (!in_array($role, $allowed_roles)) {
                unset($roles[$role]);
            }
        }
        
        return $roles;
    }
    
    /**
     * Add JavaScript to reorder the role dropdown directly in the DOM
     */
    public function reorder_roles_js() {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Function to reorder the role options
            function reorderRoleOptions() {
                // Get the role select element
                var $roleSelect = $('select#role');
                if (!$roleSelect.length) {
                    $roleSelect = $('select[name="role"]');
                }
                
                if ($roleSelect.length) {
                    // Define the desired order
                    var desiredOrder = ['content_editor', 'content_manager', 'administrator'];
                    
                    // Get all options
                    var $options = $roleSelect.find('option').get();
                    
                    // Sort options based on our desired order
                    $options.sort(function(a, b) {
                        var aValue = $(a).val();
                        var bValue = $(b).val();
                        
                        var aIndex = desiredOrder.indexOf(aValue);
                        var bIndex = desiredOrder.indexOf(bValue);
                        
                        // If both values are in our order array, sort by their index
                        if (aIndex !== -1 && bIndex !== -1) {
                            return aIndex - bIndex;
                        }
                        
                        // If only aValue is in our order array, it comes first
                        if (aIndex !== -1) {
                            return -1;
                        }
                        
                        // If only bValue is in our order array, it comes first
                        if (bIndex !== -1) {
                            return 1;
                        }
                        
                        // Otherwise, maintain original order
                        return 0;
                    });
                    
                    // Replace existing options with sorted ones
                    $roleSelect.empty().append($options);
                }
            }
            
            // Run on page load
            reorderRoleOptions();
            
            // Also run after any AJAX completes (in case the form is loaded dynamically)
            $(document).ajaxComplete(function() {
                reorderRoleOptions();
            });
        });
        </script>
        <?php
    }
    
    /**
     * Add instance selection fields to the user edit screen
     *
     * @param WP_User $user The user object being edited
     */
    public function add_instance_selection_fields($user) {
        // Only show these fields if the user is a content editor
        if (!in_array('content_editor', $user->roles)) {
            return;
        }
        
        // Get all instance posts
        $instances = get_posts(array(
            'post_type' => 'instance',
            'numberposts' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        
        // Get the currently selected instances for this user
        $selected_instances = get_user_meta($user->ID, 'webcr_assigned_instances', true);
        if (!is_array($selected_instances)) {
            $selected_instances = array();
        }
        
        // Display the fields
        ?>
        <h3><?php _e('Instance Assignments', 'webcr'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label><?php _e('Assigned Instances', 'webcr'); ?></label></th>
                <td>
                    <?php if (!empty($instances)) : ?>
                        <fieldset>
                            <legend class="screen-reader-text"><?php _e('Assigned Instances', 'webcr'); ?></legend>
                            <?php foreach ($instances as $instance) : ?>
                                <label>
                                    <input type="checkbox" 
                                           name="webcr_assigned_instances[]" 
                                           value="<?php echo esc_attr($instance->ID); ?>" 
                                           <?php checked(in_array($instance->ID, $selected_instances)); ?>>
                                    <?php echo esc_html($instance->post_title); ?>
                                </label><br>
                            <?php endforeach; ?>
                        </fieldset>
                        <p class="description"><?php _e('Select the instances this content editor can manage.', 'webcr'); ?></p>
                    <?php else : ?>
                        <p><?php _e('No instances found.', 'webcr'); ?></p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Save the selected instances when the user is updated
     *
     * @param int $user_id The ID of the user being saved
     * @return bool|void
     */
    public function save_instance_selections($user_id) {
        // Check for permissions
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }
        
        // Get the current user object
        $user = get_userdata($user_id);
        
        // Only save instance selections if the user is a content editor
        if (in_array('content_editor', $user->roles)) {
            // Get the selected instances
            $selected_instances = isset($_POST['webcr_assigned_instances']) ? $_POST['webcr_assigned_instances'] : array();
            
            // Sanitize each ID
            $selected_instances = array_map('absint', $selected_instances);
            
            // Save the selected instances
            update_user_meta($user_id, 'webcr_assigned_instances', $selected_instances);
        }
    }
}

// Initialize the class
$webcr_custom_roles = new WEBCR_Custom_Roles();

/**
 * Check if a user has access to a specific instance
 *
 * @param int $user_id The user ID to check
 * @param int $instance_id The instance ID to check access for
 * @return bool Whether the user has access to the instance
 */
function webcr_user_has_instance_access($user_id, $instance_id) {
    // Get the user
    $user = get_userdata($user_id);
    
    // Administrators and Content Managers have access to everything
    if (in_array('administrator', $user->roles) || in_array('content_manager', $user->roles)) {
        return true;
    }
    
    // Check if the user is a content editor
    if (in_array('content_editor', $user->roles)) {
        // Get the user's assigned instances
        $assigned_instances = get_user_meta($user_id, 'webcr_assigned_instances', true);
        
        // Check if the instance is in the assigned instances
        if (is_array($assigned_instances) && in_array($instance_id, $assigned_instances)) {
            return true;
        }
    }
    
    return false;
}