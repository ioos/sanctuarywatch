(function( $ ) {
	'use strict';
    
    // adding jquery to the console
    // var script = document.createElement('script');
    // script.src='https://code.jquery.com/jquery-latest.min.js';
    // document.getElementsByTagName('head')[0].appendChild(script);

    // The following code changes the number of visible about boxes depending on the value of "Number of About Boxes"
    displayAboutBoxes();
    
    function displayAboutBoxes (){
        const numAboutBoxes = document.getElementsByName("numberAboutBoxes")[0].value;
        let target_element = "";
		for (let i = 10; i > numAboutBoxes; i--){
			target_element =  "aboutBoxMain" + i;
            document.getElementById(target_element).closest('.exopite-sof-field-fieldset').style.display="none";
        }

		for (let i = 1; i <= numAboutBoxes; i++){
			target_element =  "aboutBoxMain" + i;
            document.getElementById(target_element).closest('.exopite-sof-field-fieldset').style.display="block";
		}
	}

    $(".range[data-depend-id='numberAboutBoxes']").change(displayAboutBoxes);

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
    