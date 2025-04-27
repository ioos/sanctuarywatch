<?php

/**
 * The file that defines the validation methods for the fields of the Scene custom content type
 */

class webcr_validation {

    //public $save_fields;

    public function master_validate($validate_content_type){
        switch ($validate_content_type) {
            case "about":
                return true;
                break;
            case "scene":
                return $this->validate_scene();
                break;
            case "modal":
                return $this->validate_modal();
                break;
            case "figure":
                return $this->validate_figure();
                break;
            case "instance":
                return $this->validate_instance();
                break;
            case "default":
                return false;
        }
    }

    public function validate_instance (){
        $save_instance_fields = true;
        return $save_instance_fields;
    }

    public function validate_figure (){
        $save_figure_fields = true;

        // Set the error list cookie expiration time to a past date in order to delete it, if it is there
        setcookie("figure_errors", 0, time() - 3000, "/");

        $figure_errors = [];
        $figure_warnings = [];

        $field_types = array("figure_science_", "figure_data_");

        foreach ($field_types as $field_type){
            $form_fieldset = $field_type .  "info";
            $field_couplet = $_POST[$form_fieldset];

            $field_text = $field_type . "link_text";
            $field_url = $field_type . "link_url";
            if (!$field_couplet[$field_url] == "" || !$field_couplet[$field_text] == "" ){
                if ($field_couplet[$field_url] == "" || $field_couplet[$field_text] == "" ){
                    $save_figure_fields = FALSE;
                    array_push($figure_errors,  "The URL or Text is blank for Link " . $field_type);
                }
                if (!$field_couplet[$field_url] == "" ) {
                    if ( $this -> url_check($field_couplet[$field_url]) == FALSE ) {
                        $save_figure_fields = FALSE;
                        array_push($figure_errors, "The URL for " . $field_type . " is not valid");
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
                            array_push($figure_warnings, "The URL for " . $field_type . " cannot be accessed");                               
                        }
                    }
                }
            }
        }

        if (!empty($figure_warnings)){
            $warning_list_cookie_value = json_encode($figure_warnings);
            setcookie("figure_warnings", $warning_list_cookie_value, time() + 10, "/");          
        }
        if ($save_figure_fields == FALSE) {
            $error_list_cookie_value = json_encode($figure_errors);
            setcookie("figure_errors", $error_list_cookie_value, time() + 10, "/");           
            setcookie("figure_post_status", "post_error", time() + 10, "/");
            $this->figure_fields_to_cookie();
        } else {
            setcookie("figure_post_status", "post_good", time() + 10, "/");
        }
        return $save_figure_fields;
    }
    
    public function validate_modal(){
        $save_modal_fields = true;

        // Set the error list cookie expiration time to a past date in order to delete it, if it is there
        setcookie("modal_errors", 0, time() - 3000, "/");

        $modal_errors = [];
        $modal_warnings = [];

        //Check modal title for potential errors
        $modal_title = $_POST["post_title"];
        $words = explode(' ', $modal_title);
        
        foreach ($words as $word) {
            // Remove any punctuation for accurate word length
            $clean_word = preg_replace('/[^\p{L}\p{N}]/u', '', $word);
            if (strlen($clean_word) > 14) {
                array_push($modal_warnings, "The word '" . $clean_word . "' in the modal title is longer than 14 characters, which may cause issues in mobile view.");
            } 
        }

        // Report warning if total title length exceeds 70 characters
        $string_length = strlen($modal_title);
        if ($string_length > 70) {
            array_push($modal_warnings, "The title length is {$string_length} characters long, which exceeds the 70 character limit recommendation for proper layout.");
        }

        if ($_POST["modal_location"] == ""){
            array_push($modal_errors,  "The Instance field cannot be left blank.");
            $save_modal_fields = FALSE;
        }

        if ($_POST["modal_scene"] == ""){
            array_push($modal_errors,  "The Scene field cannot be left blank.");
            $save_modal_fields = FALSE;
        }

        if ($_POST["modal_icons"] == ""){
            array_push($modal_errors,  "The Icons field cannot be left blank.");
            $save_modal_fields = FALSE;
        }

        // If the associated scene contains sections, force the use of sections with this modal
        if ($_POST["modal_scene"] != ""){
            $scene_ID = intval($_POST["modal_scene"]);
            $scene_toc_style = get_post_meta($scene_ID, "scene_toc_style", true);
            $scene_section_number = get_post_meta($scene_ID, "scene_section_number", true);
            if ($scene_toc_style != "list" && $scene_section_number != 0){
                if ($_POST["icon_toc_section"] == ""){
                    array_push($modal_errors,  "The Icon Section field cannot be left blank.");
                    $save_modal_fields = FALSE;
                }
            }
        }

        // Based upon the value of the icon action field (that's the title, but the actual name is icon function), do some error checking
        switch ($_POST["icon_function"]) {
            case "External URL":
                $icon_external_url = $_POST["icon_external_url"];

                if ($icon_external_url == ""){
                    $save_modal_fields = FALSE;
                    array_push($modal_errors,  "The Icon External URL field is blank.");
                } else {
                    if ( $this -> url_check($icon_external_url) == FALSE ) {
                        $save_modal_fields = FALSE;
                        array_push($modal_errors, "The Icon External URL is not valid");
                    } else {
                        // Set cURL options
                        $ch = curl_init($icon_external_url);
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
                            array_push($modal_warnings, "The Icon External URL cannot be accessed.");                               
                        }
                    }
                }
                break;
            case "Modal":
                $modal_tab_number = $_POST["modal_tab_number"];
                if ($modal_tab_number == 0){
                    $save_modal_fields = FALSE;
                    array_push($modal_errors,  "There must be at least one modal tab if the Icon Action is set to Modal");
                } else {
                    for ($i = 1; $i <= $modal_tab_number; $i++) {
                        $tab_title = $_POST["modal_tab_title" . $i]; 
                        if (empty($tab_title) || is_null($tab_title) ){
                            $save_modal_fields = FALSE;
                            array_push($modal_errors,  "The Modal Tab Title " . $i . " is blank.");
                        }
                    }            
                }
                break;
            case "Scene":
                $icon_scene_out = $_POST["icon_scene_out"];

                if ($icon_scene_out == ""){
                    $save_modal_fields = FALSE;
                    array_push($modal_errors,  "The Icon Scene Out field is blank.");
                } 
                break;
        }

        $field_types = array("info", "photo");

        foreach ($field_types as $field_type){
            for ($i = 1; $i < 7; $i++){

                $form_fieldset = 'modal_' . $field_type .  $i;
                $field_couplet = $_POST[$form_fieldset];
                $field_text = "modal_" . $field_type . "_text" . $i;
                $field_url = "modal_" . $field_type . "_url" . $i;
                $field_photo_internal = "modal_photo_internal" . $i;
                if (!$field_couplet[$field_url] == "" || !$field_couplet[$field_text] == "" ){
                    if ( ($field_type == "info" && ($field_couplet[$field_url] == "" || $field_couplet[$field_text] == "")) || ($field_type == "photo" && ( ($field_couplet[$field_url] == "" && $field_couplet[$field_photo_internal]  == "")  || $field_couplet[$field_text] == ""))   ){
                        $save_modal_fields = FALSE;
                        array_push($modal_errors,  "Error in Modal " . ucfirst($field_type) . " Link " . $i);
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

    // Write all values from the fields of the edit modal post to a cookie. 
    // This is used to repopulate the fields in the modal edit form if there are errors in the submission.
    public function modal_fields_to_cookie () {

        $modal_field_names = ["modal_published", "modal_location", "modal_scene", "modal_icons", "modal_icon_order", "icon_toc_section",
            "icon_function", "icon_external_url", "icon_scene_out", "modal_tagline", "modal_info_entries", "modal_photo_entries", "modal_tab_number"];

        $modal_fields = [];
        foreach ($modal_field_names as $individual_modal_field_name){
            $modal_fields[$individual_modal_field_name] = $_POST[$individual_modal_field_name];
        }

        for ($i = 1; $i < 7; $i++){
            $modal_fields['modal_info_url' . $i] = $_POST["modal_info" . $i]["modal_info_url" . $i];
            $modal_fields['modal_info_text' . $i] = $_POST["modal_info" . $i]["modal_info_text" . $i];
            $modal_fields['modal_photo_url' . $i] = $_POST["modal_photo" . $i]["modal_photo_url" . $i];
            $modal_fields['modal_photo_text' . $i] = $_POST["modal_photo" . $i]["modal_photo_text" . $i];
            $modal_fields['modal_photo_location' . $i] = $_POST["modal_photo" . $i]["modal_photo_location" . $i];
            $modal_fields['modal_photo_internal' . $i] = $_POST["modal_photo" . $i]["modal_photo_internal" . $i];
            $modal_fields['modal_tab_title' . $i] = $_POST['modal_tab_title' . $i];
        }
        $modal_fields_cookie_value = json_encode($modal_fields);

        setcookie("modal_error_all_fields", $modal_fields_cookie_value, time() + 10, "/"); 
    }

    // The purpose of this function is to validate the fields of the Scene custom content type. If validation fails, it sets a cookie with the error messages and the values of the fields that were submitted. 
    // It also sets a cookie to indicate whether the post was successful or not. If the function returns false, it means that the validation failed and the post was not saved. 
    // However, the page is reloaded and an error message is displayed to the user.
    public function validate_scene (){
        $save_scene_fields = true;

        // Set the error list cookie expiration time to a past date in order to delete it, if it is there
        setcookie("scene_errors", 0, time() - 3000, "/");

        $scene_errors = [];
        $scene_warnings = [];

        if ($_POST["scene_location"] == ""){
            array_push($scene_errors,  "The Instance field cannot be left blank.");
            $save_scene_fields = FALSE;
        }

        $scene_infographic = $_POST["scene_infographic"];

        if (is_null($scene_infographic) || $scene_infographic == "" ){
            array_push($scene_errors,  "The Infographic field cannot be left blank.");
            $save_scene_fields = FALSE;
        }

        if (!(is_null($scene_infographic)) && !($scene_infographic == "") ){
            // Parse the URL to extract the path
            $parsed_url = parse_url($scene_infographic);

            // Get the path from the parsed URL
            $path_url = $parsed_url['path'];
            $content_path = rtrim(get_home_path(), '/') . $path_url;
            $content = file_get_contents( $content_path);
            //$content = file_get_contents( $scene_infographic );
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

        if ($_POST["scene_toc_style"] != "list" && $_POST["scene_section_number"] != "0"){
            $section_number = intval($_POST["scene_section_number"]);
            for ($q = 1; $q <= $section_number; $q++){
                if ($_POST["scene_section". $q ]["scene_section_title" . $q] == ""){
                    array_push($scene_errors,  "Scene section title " . $q . " is blank.");
                    $save_scene_fields = FALSE;
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
                $field_photo_internal = "scene_photo_internal" . $i;
                if (!$field_couplet[$field_url] == "" || !$field_couplet[$field_text] == "" ){
                    if ( ($field_type == "info" && ($field_couplet[$field_url] == "" || $field_couplet[$field_text] == "")) || ($field_type == "photo" && ( ($field_couplet[$field_url] == "" && $field_couplet[$field_photo_internal]  == "")  || $field_couplet[$field_text] == ""))   ){
                        $save_scene_fields = FALSE;
                        array_push($scene_errors,  "Error in Scene " . ucfirst($field_type) . " Link " . $i);
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

        $scene_field_names = ["scene_published", "scene_location", "scene_infographic", "scene_tagline", "scene_info_entries", "scene_photo_entries", 
            "scene_order", "scene_orphan_icon_action", "scene_orphan_icon_color", "scene_toc_style", "scene_same_hover_color_sections", "scene_hover_color", 
            "scene_full_screen_button", "scene_text_toggle", "scene_section_number"];

        $scene_fields = [];
        foreach ($scene_field_names as $individual_scene_field_name){
            $scene_fields[$individual_scene_field_name] = $_POST[$individual_scene_field_name];
        }

        for ($i = 1; $i < 7; $i++){
            $scene_fields['scene_info_url' . $i] = $_POST["scene_info" . $i]["scene_info_url" . $i];
            $scene_fields['scene_info_text' . $i] = $_POST["scene_info" . $i]["scene_info_text" . $i];
            $scene_fields['scene_photo_url' . $i] = $_POST["scene_photo" . $i]["scene_photo_url" . $i];
            $scene_fields['scene_photo_text' . $i] = $_POST["scene_photo" . $i]["scene_photo_text" . $i];
            $scene_fields['scene_photo_location' . $i] = $_POST["scene_photo" . $i]["scene_photo_location" . $i];
            $scene_fields['scene_photo_internal' . $i] = $_POST["scene_photo" . $i]["scene_photo_internal" . $i];
            $scene_fields['scene_section_title' . $i] = $_POST["scene_section" . $i]["scene_section_title" . $i];
            $scene_fields['scene_section_hover_color' . $i] = $_POST["scene_section" . $i]["scene_section_hover_color" . $i];
        }

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
