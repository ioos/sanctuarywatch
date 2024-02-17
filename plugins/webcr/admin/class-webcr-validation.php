<?php

/**
 * The file that defines the validation methods for the fields of the Scene custom content type
 */

class webcr_validation {

    //public $save_fields;

    public function master_validate($validate_content_type){
        if ($validate_content_type == "scene") {
            return $this->validate_scene();
        } else {
            return false;
        }
    }

    function check_scene_link_fields($field_prefix){

    }

    public function validate_scene (){
        $save_scene_fields = true;
        $user_id = get_current_user_id();

        // Set the error list cookie expiration time to a past date in order to delete it, if it is there
        setcookie("scene_errors", 0, time() - 3000, "/");

        $scene_errors = [];

        $scene_info_link_value = $_POST['scene_info_link'];
        if (!$scene_info_link_value == "") { 
            if ( $this -> url_check($scene_info_link_value) == FALSE ) {
                $save_scene_fields = FALSE;
                array_push($scene_errors, "The URL for Scene Info Link is not valid");
            } 
        }

        $scene_info_photo_link_value = $_POST['scene_info_photo_link'];
        if (!$scene_info_photo_link_value == "") { 
            if ( $this -> url_check($scene_info_photo_link_value) == FALSE ) {
                $save_scene_fields = FALSE;
                array_push($scene_errors, "The URL for Scene Info Photo Link is not valid");
            } 
        }

        if ($save_scene_fields == FALSE) {
            $error_list_cookie_value = json_encode($scene_errors);
            setcookie("scene_errors", $error_list_cookie_value, time() + 10, "/");           
            setcookie("scene_post_status", "post_error", time() + 10, "/");
            $this->scene_fields_to_cookie();
        } else {
            setcookie("scene_post_status", "post_good", time() + 10, "/");
        }
        return $save_scene_fields;
    }

    public function scene_fields_to_cookie () {
        $scene_fields = [];
        $scene_fields['scene_location'] = $_POST['scene_location'];
        $scene_fields['scene_infographic'] = $_POST['scene_infographic'];
        $scene_fields['scene_tagline'] = $_POST['scene_tagline'];
        $scene_fields['scene_info_link'] = $_POST['scene_info_link'];
        $scene_fields['scene_info_photo_link'] = $_POST['scene_info_photo_link'];
        $scene_fields_cookie_value = json_encode($scene_fields);
        $user_id = get_current_user_id();
      //  $scene_fields_cookie =  "scene_error_all_fields";
        setcookie("scene_error_all_fields", $scene_fields_cookie_value, time() + 10, "/"); 
    }

    // This function checks whether an input url has valid syntax
    public function url_check ($input_url) {
        if ( filter_var($input_url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) == FALSE ) {
            return FALSE;
        } else {
            return TRUE;
        } 
    }

    // JAI - DELETE FUNCTION
    public function render_to_pdf() {
        // ... whatever you need to do
          my_trigger_notice( 1 ); // 1 here would be a key that refers to a particular message, defined elsewhere (and not shown here)
    }

     // JAI - DELETE FUNCTION   
    function my_trigger_notice( $key = '' ) {
        add_filter(
            'redirect_post_location',
            function ( $location ) use ( $key ) {
                $key = sanitize_text_field( $key );
    
                return add_query_arg( array( 'notice_key' => rawurlencode( sanitize_key( $key ) ) ), $location );
            }
        );
    }

}
