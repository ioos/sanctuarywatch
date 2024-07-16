<?php

/**
 * Utility functions used across the plugin
 *
 * @link       https://www.noaa.gov
 * @since      1.0.0
 *
 * @package    Webcr
 * @subpackage Webcr/admin
 */

/**
 * Utility functions used across the plugin
 *
 * @package    Webcr
 * @subpackage Webcr/admin
 * @author     Jai Ranganathan <jai.ranganathan@noaa.gov>
 */
class Webcr_Utility {

    /**
	 * Shorten string without cutting words midword.
	 *
     * @param string $string The string to be shortened.
     * @param int $your_desired_width The number of characters in the shortened string.
     * @return The shortened string
	 * @since    1.0.0
	 */
    public function stringTruncate($string, $your_desired_width) {
        $parts = preg_split('/([\s\n\r]+)/', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
        $parts_count = count($parts);
        
        $length = 0;
        //$last_part = 0;
        for ($last_part = 0; $last_part < $parts_count; ++$last_part) {
            $length += strlen($parts[$last_part]);
            if ($length > $your_desired_width) { break; }
        }
        
        return implode(array_slice($parts, 0, $last_part));
    }

    /**
	 * Get list of locations, which is returned as an array
	 *
     * @return  An array containing the list of locations of the instances
	 * @since    1.0.0
	 */
    public function returnInstances() {
        // get list of locations, which is saved as a taxonomy
        $locations_array = get_terms(array('taxonomy' => 'location', 'hide_empty' => false));
        $locations=[];
        foreach ( $locations_array as $locations_row ){
            $locations[$locations_row -> name] = $locations_row -> name;
        }
        return $locations;
    }

    public function returnSceneTitles($scene_id, $modal_id){
        $scene_titles =  array("" => "Scene");
        if (array_key_exists("post", $_GET)) {
            $scene_location = get_post_meta($modal_id, "modal_location", true);
            $scene_name = get_post_meta($scene_id, "post_title", true);
            $scenes[$scene_id] = $scene_name;

            $args = array(
                'post_type' => 'scene',  // Your custom post type
                'meta_query' => array(
                    array(
                        'key' => 'scene_location',      // The custom field key
                        'value' => $scene_location, // The value you are searching for
                        'compare' => '='         // Comparison operator
                    )
                ),
                'fields' => 'ids'            // Only return post IDs
            );
            
            // Execute the query
            $query = new WP_Query($args);
            
            // Get the array of post IDs
            $scene_post_ids = $query->posts;

            $scene_titles =[];
            foreach ($scene_post_ids as $target_id){
                $target_title = get_post_meta($target_id, "post_title", true);
                $scene_titles[$target_id] = $target_title;
            }
            asort($scene_titles);
        }
        return $scene_titles;
    }

    public function returnIcons($scene_id){
        $modal_icons = array("" => "Icons");
        $scene_infographic = get_post_meta($scene_id, "scene_infographic", true);
        if ($scene_infographic == true){
            $relative_path =  ltrim(parse_url($scene_infographic)['path'], "/");

            $full_path = get_home_path() . $relative_path;

            $svg_content = file_get_contents($full_path);

            if ($svg_content === false) {
                die("Failed to load SVG file.");
            }
            
            // Create a new DOMDocument instance and load the SVG content 
            $dom = new DOMDocument();
            libxml_use_internal_errors(true); // Suppress errors related to invalid XML
            $dom->loadXML($svg_content);
            libxml_clear_errors();
            
            // Create a new DOMXPath instance
            $xpath = new DOMXPath($dom);
            
            // Find the element with the ID "icons"
            $icons_element = $xpath->query('//*[@id="icons"]')->item(0);
            
            if ($icons_element === null) {
                die('Element with ID "icons" not found.');
            }
            
            // Get all child elements of the "icons" element
            $child_elements = $icons_element->childNodes;
            
            // Initialize an array to hold the IDs
            $child_ids = array();
            
            // Loop through the child elements and extract their IDs
            foreach ($child_elements as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE && $child->hasAttribute('id')) {
                    $child_ids[] = $child->getAttribute('id');
                }
            }
            asort($child_ids);
            foreach ($child_ids as $single_icon){
                $modal_icons[$single_icon] = $single_icon;
            }
        }
        return $modal_icons;
    }

    // return an array of scenes, other than the current scene, for a given location
    public function returnScenesExceptCurrent($scene_id){
        $potential_scenes = [];// array(" " => "Scenes");
        $scene_location = get_post_meta($scene_id, "scene_location", true);
        if ($scene_location == true){
            $args = array(
                'post_type' => 'scene',  // Your custom post type
                'meta_query' => array(
                    array(
                        'key' => 'scene_location',      // The custom field key
                        'value' => $scene_location, // The value you are searching for
                        'compare' => '='         // Comparison operator
                    )
                ),
                'fields' => 'ids'            // Only return post IDs
            );
            
            // Execute the query
            $query = new WP_Query($args);
            
            // Get the array of post IDs
            $scene_post_ids = $query->posts;
            foreach ($scene_post_ids as $target_id){
                if ($target_id != $scene_id) {
                    $target_title = get_post_meta($target_id, "post_title", true);
                    $potential_scenes[$target_id] = $target_title;
                }
            }
            asort($potential_scenes);
        }
        return $potential_scenes;
    }

    // Potential section headers for icons
    public function returnModalSections($scene_id){
        $modal_sections = [];
        for ($i = 1; $i < 7; $i++){
            $field_target = 'scene_section' . $i;
            $target_section = get_post_meta($scene_id, $field_target, true);
            if ($target_section != null && $target_section != ""){
                $modal_sections[$target_section] = $target_section;
            }
        }
        asort($modal_sections);
        $modal_sections = array_merge(array("None" => "None"),$modal_sections );
        return $modal_sections;
    }

    // Dropdown options for Scene in figure content type
    public function returnScenesFigure($location){
        $potential_scenes = [];
        if ($location != ""){
            $args = array(
                'post_type' => 'scene',  // Your custom post type
                'meta_query' => array(
                    array(
                        'key' => 'scene_location',      // The custom field key
                        'value' => $location, // The value you are searching for
                        'compare' => '='         // Comparison operator
                    )
                ),
                'fields' => 'ids'            // Only return post IDs
            );
            // Execute the query
            $query = new WP_Query($args);

            // Get the array of post IDs
            $scene_post_ids = $query->posts;
            foreach ($scene_post_ids as $target_id){
                $target_title = get_post_meta($target_id, "post_title", true);
                $potential_scenes[$target_id] = $target_title;
            }
            asort($potential_scenes);
        }
        return $potential_scenes;
        
    }

    public function returnModalTabs($modal_id){
        $potential_tabs = [];
        if ($modal_id != "") {
            for ($i = 1; $i < 7; $i++){
                $target_field = "modal_tab_title" . $i;
                $target_title = get_post_meta($modal_id, $target_field, true);
                if ($target_title != "" && $target_title != null ){
                    $potential_tabs[$target_title] = $target_title;
                }
            }
            asort($potential_tabs);
        }
        return $potential_tabs;
    }

    //dropdown options for Icon in figure content type
    public function returnFigureIcons($scene_id){
        $potential_icons = [];
        if ($scene_id != ""){

            $args = array(
                'post_type' => 'modal',  // Your custom post type
                'fields' => 'ids',           // Only return post IDs
                'meta_query' => array(
                    array(
                        'key' => 'modal_scene',      // The custom field key
                        'value' => $scene_id, // The value you are searching for
                        'compare' => '='         // Comparison operator
                    ),
                    array(
                        'key' => 'icon_function',
                        'value' => 'Modal',
                        'compare' => '='
                    ),
                ),
            );
            
            // Execute the query
            $query = new WP_Query($args);
            
            // Get the array of post IDs
            $modal_post_ids = $query->posts;

            $modal_titles =[];
            foreach ($modal_post_ids as $target_id){
                $target_title = get_post_meta($target_id, "post_title", true);
                $potential_icons[$target_id] = $target_title;
            }
            asort($potential_icons);
        }
        return $potential_icons;
    }

}

