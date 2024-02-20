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

    public function validate_scene (){
        $save_scene_fields = true;

        // Set the error list cookie expiration time to a past date in order to delete it, if it is there
        setcookie("scene_errors", 0, time() - 3000, "/");

        $scene_errors = [];
        $scene_warnings = [];
        $field_types = array("info", "photo");

        foreach ($field_types as $field_type){
            for ($i = 1; $i < 7; $i++){
                $form_fieldset = 'scene_' . $field_type .  $i;
                $field_couplet = $_POST[$form_fieldset];
                $field_text = "scene_" . $field_type . "_text" . $i;
                $field_url = "scene_" . $field_type . "_url" . $i;
                if (!$field_couplet[$field_url] == "" || !$field_couplet[$field_text] == "" ){
                    if ($field_couplet[$field_url] == "" || $field_couplet[$field_text] == "" ){
                        $save_scene_fields = FALSE;
                        array_push($scene_errors,  "The URL or Text is blank for Scene " . ucfirst($field_type) . " Link " . $i);
                    }
                    if (!$field_couplet[$field_url] == "" ) {
                        if ( $this -> url_check($field_couplet[$field_url]) == FALSE ) {
                            $save_scene_fields = FALSE;
                            array_push($scene_errors, "The URL for Scene " . ucfirst($field_type) . " Link " . $i . " is not valid");
                        } else {
                            $url_check = get_headers($field_couplet[$field_url])[0];


                            //https://stackoverflow.com/questions/39113450/php-get-headers-returns-400-bad-request-and-403-forbidden-for-valid-urls
                            if ($url_check != "HTTP/1.1 200 OK"){
                                array_push($scene_warnings, "The URL for Scene " . ucfirst($field_type) . " Link " . $i . " cannot be accessed");                               
                            }
                        }
                    }
                }
            }
        }
        if (!empty($scene_warnings)){
            $warning_list_cookie_value = json_encode($scene_warnings);
            setcookie("scene_warnings", $warning_list_cookie_value, time() + 10, "/");          
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
        $scene_fields['scene_info_entries'] = $_POST['scene_info_entries'];
        $scene_fields['scene_photo_entries'] = $_POST['scene_photo_entries'];
     //   $scene_fields['scene_info_link'] = $_POST['scene_info_link'];
     //   $scene_fields['scene_info_photo_link'] = $_POST['scene_info_photo_link'];
        $scene_fields_cookie_value = json_encode($scene_fields);

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


}
