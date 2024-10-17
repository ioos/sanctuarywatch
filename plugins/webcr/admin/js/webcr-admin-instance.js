// These functions only fire upon editing or creating a post of Instance custom content type
(function( $ ) {
    'use strict';

    displayLegacyContentField();

    // should the "legacy content url" field be shown?
    function displayLegacyContentField () {
        const legacyContent = document.getElementsByName("instance_legacy_content")[0].value;
        if (legacyContent == "no"){
            document.getElementsByName("instance_legacy_content_url")[0].parentElement.parentElement.style.display = "none";
            document.getElementsByName("instance_legacy_content_url")[0].value = "";          
        } else {
            document.getElementsByName("instance_legacy_content_url")[0].parentElement.parentElement.style.display = "block";
        }
    }

    $('select[name="instance_legacy_content"]').change(function(){
		displayLegacyContentField();
	});
    
})( jQuery );