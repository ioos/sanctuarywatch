<?php
/**
 * Register custom taxonomy for the location of a site (example: Olympic Coast National Marine Sanctuary)
 * 
 */
class Webcr_Taxonomy {

    /**
     * Register custom taxonomy for the location of a site (example: Olympic Coast National Marine Sanctuary)
     * 
     */
    function custom_location_taxonomy() {
            
        $labels = array(
            'name'                       => _x( 'Locations', 'Taxonomy General Name', 'text_domain' ),
            'singular_name'              => _x( 'Location', 'Taxonomy Singular Name', 'text_domain' ),
            'menu_name'                  => __( 'Location', 'text_domain' ),
            'all_items'                  => __( 'All Locations', 'text_domain' ),
            'parent_item'                => __( 'Parent Location', 'text_domain' ),
            'parent_item_colon'          => __( 'Parent Location:', 'text_domain' ),
            'new_item_name'              => __( 'New Location Name', 'text_domain' ),
            'add_new_item'               => __( 'Add New Location', 'text_domain' ),
            'edit_item'                  => __( 'Edit Location', 'text_domain' ),
            'update_item'                => __( 'Update Location', 'text_domain' ),
            'view_item'                  => __( 'View Location', 'text_domain' ),
            'separate_items_with_commas' => __( 'Separate locations with commas', 'text_domain' ),
            'add_or_remove_items'        => __( 'Add or Remove Locations', 'text_domain' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
            'popular_items'              => __( 'Popular Items', 'text_domain' ),
            'search_items'               => __( 'Search Locations', 'text_domain' ),
            'not_found'                  => __( 'Not Found', 'text_domain' ),
            'no_terms'                   => __( 'No items', 'text_domain' ),
            'items_list'                 => __( 'Locations list', 'text_domain' ),
            'items_list_navigation'      => __( 'Locations list navigation', 'text_domain' ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        );
        register_taxonomy( 'location', array( 'post' ), $args );

        wp_insert_term('Channel Islands NMS','location', array('slug' => 'ChannelIslands'));
        wp_insert_term('Florida Keys NMS','location', array('slug' => 'FloridaKeys'));
        wp_insert_term('Monterey Bay NMS','location', array('slug' => 'MontereyBay'));
        wp_insert_term('Olympic Coast NMS','location', array('slug' => 'OlympicCoast'));
    }

}