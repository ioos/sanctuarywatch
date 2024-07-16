<?php

/**
 * The file that defines the validation methods for the fields of the Scene custom content type
 */

class webcr_validation {

    //public $save_fields;

    public function master_validate($validate_content_type){
        switch ($validate_content_type) {
            case "scene":
                return $this->validate_scene();
                break;
            case "modal":
                return $this->validate_modal();
                break;
            case "figure":
                return $this->validate_figure();
                break;
            case "default":
                return false;
        }
    }

    public function validate_figure (){
        $save_figure_fields = true;
        return $save_figure_fields;
    }
    
    public function validate_modal(){
        $save_modal_fields = true;

        // Set the error list cookie expiration time to a past date in order to delete it, if it is there
        setcookie("modal_errors", 0, time() - 3000, "/");

        $modal_errors = [];
        $modal_warnings = [];
        
        $field_types = array("info", "photo");

        foreach ($field_types as $field_type){
            for ($i = 1; $i < 7; $i++){
                $form_fieldset = 'modal_' . $field_type .  $i;
                $field_couplet = $_POST[$form_fieldset];
                $field_text = "modal_" . $field_type . "_text" . $i;
                $field_url = "modal_" . $field_type . "_url" . $i;
                if (!$field_couplet[$field_url] == "" || !$field_couplet[$field_text] == "" ){
                    if ($field_couplet[$field_url] == "" || $field_couplet[$field_text] == "" ){
                        $save_modal_fields = FALSE;
                        array_push($modal_errors,  "The URL or Text is blank for Modal " . ucfirst($field_type) . " Link " . $i);
                    }
                    if (!$field_couplet[$field_url] == "" ) {
                        if ( $this -> url_check($field_couplet[$field_url]) == FALSE ) {
                            $save_modal_fields = FALSE;
                            array_push($modal_errors, "The URL for Modal " . ucfirst($field_type) . " Link " . $i . " is not valid");
                        } else {

                            // Set cURL options
                            $ch = curl_init($field_couplet[$field_url]);
                            $userAgent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36";
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Return the transfer as a string
                            curl_setopt($ch, CURLOPT_NOBODY, true);  // Exclude the body from the output
                            curl_setopt($ch, CURLOPT_HEADER, true);  // Include the header in the output
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Follow redirects
                            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);  // Set User-Agent header

                            // Execute cURL session
                            curl_exec($ch);

                            // Get the headers
                            $headers = curl_getinfo($ch);

                            // Close cURL session
                            curl_close($ch);

                            if ($headers["http_code"] != 200){
                                array_push($modal_warnings, "The URL for Modal " . ucfirst($field_type) . " Link " . $i . " cannot be accessed");                               
                            }
                        }
                    }
                }
            }
        }
        if (!empty($modal_warnings)){
            $warning_list_cookie_value = json_encode($modal_warnings);
            setcookie("modal_warnings", $warning_list_cookie_value, time() + 10, "/");          
        }
        if ($save_modal_fields == FALSE) {
            $error_list_cookie_value = json_encode($modal_errors);
            setcookie("modal_errors", $error_list_cookie_value, time() + 10, "/");           
            setcookie("modal_post_status", "post_error", time() + 10, "/");
            $this->modal_fields_to_cookie();
        } else {
            setcookie("modal_post_status", "post_good", time() + 10, "/");
        }


        return $save_modal_fields;
    }

    public function modal_fields_to_cookie () {
        $modal_fields = [];
        $modal_fields['modal_location'] = $_POST['modal_location'];
        $modal_fields['modal_scene'] = $_POST['modal_scene'];
        $modal_fields['modal_icons'] = $_POST['modal_icons'];
        $modal_fields['icon_function'] = $_POST['icon_function'];
        $modal_fields['icon_external_url'] = $_POST['icon_external_url'];
        $modal_fields['icon_scene_out'] = $_POST['icon_scene_out'];        
        $modal_fields['modal_tagline'] = $_POST['modal_tagline'];        
        $modal_fields['modal_info_entries'] = $_POST['modal_info_entries'];        
        $modal_fields['modal_photo_entries'] = $_POST['modal_photo_entries'];        
        $modal_fields['modal_tab_number'] = $_POST['modal_tab_number'];        
        $modal_fields['icon_out_type'] = $_POST['icon_out_type'];


        for ($i = 1; $i < 7; $i++){
            $modal_fields['modal_info_url' . $i] = $_POST["modal_info" . $i]["modal_info_url" . $i];
            $modal_fields['modal_info_text' . $i] = $_POST["modal_info" . $i]["modal_info_text" . $i];
            $modal_fields['modal_photo_url' . $i] = $_POST["modal_photo" . $i]["modal_photo_url" . $i];
            $modal_fields['modal_photo_text' . $i] = $_POST["modal_photo" . $i]["modal_photo_text" . $i];
            $modal_fields['modal_tab_title' . $i] = $_POST['modal_tab_title' . $i];
        }
        $modal_fields_cookie_value = json_encode($modal_fields);

        setcookie("modal_error_all_fields", $modal_fields_cookie_value, time() + 10, "/"); 
    }

    public function validate_scene (){
        $save_scene_fields = true;

        // Set the error list cookie expiration time to a past date in order to delete it, if it is there
        setcookie("scene_errors", 0, time() - 3000, "/");

        $scene_errors = [];
        $scene_warnings = [];

        $scene_infographic = $_POST["scene_infographic"];

        if (!(is_null($scene_infographic)) && !($scene_infographic == "") ){
            $content = file_get_contents( $scene_infographic );
            if ($content == false) {
                array_push($scene_errors,  "The infographic does not exist.");
                $save_scene_fields = FALSE;
            } else {
                // Use finfo or getimagesize to determine the MIME type
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_buffer($finfo, $content);
                finfo_close($finfo);
                if ($mime_type != "image/svg+xml"){
                    array_push($scene_errors,  "The infographic is not a svg file.");
                    $save_scene_fields = FALSE;
                } else {
                    // Search for the <icons> tag
                    $iconPosition = strpos($content, 'icons');
                    if ($iconPosition == false) {
                        array_push($scene_errors,  "The infographic does not contain an Icons layer.");
                        $save_scene_fields = FALSE;
                    }
                }
            }
        }
        
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

                            // Set cURL options
                            $ch = curl_init($field_couplet[$field_url]);
                            $userAgent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36";
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Return the transfer as a string
                            curl_setopt($ch, CURLOPT_NOBODY, true);  // Exclude the body from the output
                            curl_setopt($ch, CURLOPT_HEADER, true);  // Include the header in the output
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Follow redirects
                            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);  // Set User-Agent header

                            // Execute cURL session
                            curl_exec($ch);

                            // Get the headers
                            $headers = curl_getinfo($ch);

                            // Close cURL session
                            curl_close($ch);

                            if ($headers["http_code"] != 200){
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
        for ($i = 1; $i < 7; $i++){
            $scene_fields['scene_info_url' . $i] = $_POST["scene_info" . $i]["scene_info_url" . $i];
            $scene_fields['scene_info_text' . $i] = $_POST["scene_info" . $i]["scene_info_text" . $i];
            $scene_fields['scene_photo_url' . $i] = $_POST["scene_photo" . $i]["scene_photo_url" . $i];
            $scene_fields['scene_photo_text' . $i] = $_POST["scene_photo" . $i]["scene_photo_text" . $i];
        }

        // $_POST["scene_info1"]["scene_info_url1"]
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
