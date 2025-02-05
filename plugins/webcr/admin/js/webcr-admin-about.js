(function( $ ) {
    //	'use strict';
    
    // adding jquery to the console
    // var script = document.createElement('script');
    // script.src='https://code.jquery.com/jquery-latest.min.js';
    // document.getElementsByTagName('head')[0].appendChild(script);

// document.getElementById("wp-aboutBoxMain1-wrap").parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.style.display="block"


    function displayAboutBoxes (){
        let target_element = "";
		for (let i = 6; i > entry_number; i--){
			target_element = "modal_tab_title" + i;
            document.getElementsByName(target_element)[0].parentElement.parentElement.style.display = "none";
            document.getElementsByName(target_element)[0].value = "";
		}

		for (let i = 1; i <= entry_number; i++){
			target_element = "modal_tab_title" + i;
            document.getElementsByName(target_element)[0].parentElement.parentElement.style.display = "block";
		}
	}


$('select[name="modal_location"]').change(modal_location_change);
$( "select[name='modal_scene']" ).change(modal_scene_change);
$( "select[name='modal_icons']" ).change(modal_icons_change);
$( "select[name='icon_function']" ).change(iconFunction);

$(".range[data-depend-id='modal_tab_number']").change(function(){ 
    let opening_tab_entries = document.getElementsByName("modal_tab_number")[0].value;
    displayTabEntries(opening_tab_entries);
});

$(".range[data-depend-id='modal_info_entries']").change(function(){ 
    let number_of_scene_info_entries = $(".range[data-depend-id='modal_info_entries']").val();
    displayEntries(number_of_scene_info_entries, ".text-class[data-depend-id='modal_info_");
});

$(".range[data-depend-id='modal_photo_entries']").change(function(){ 
    let number_of_scene_info_entries = $(".range[data-depend-id='modal_photo_entries']").val();
    displayEntries(number_of_scene_info_entries, ".text-class[data-depend-id='modal_photo_");
});


        /**
         * All of the code for your admin-facing JavaScript source
         * should reside in this file.
         *
         * Note: It has been assumed you will write jQuery code here, so the
         * $ function reference has been prepared for usage within the scope
         * of this function.
         *
         * This enables you to define handlers, for when the DOM is ready:
         *
         * $(function() {
         *
         * });
         *
         * When the window is loaded:
         *
         * $( window ).load(function() {
         *
         * });
         *
         * ...and/or other possibilities.
         *
         * Ideally, it is not considered best practise to attach more than a
         * single DOM-ready or window-load handler for a particular page.
         * Although scripts in the WordPress core, Plugins and Themes may be
         * practising this, we should strive to set a better example in our own work.
         */
    
    })( jQuery );
    