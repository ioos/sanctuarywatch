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
        $scene_titles = array("" => "Scene");
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
}

