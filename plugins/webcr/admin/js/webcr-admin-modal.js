(function( $ ) {
    //	'use strict';
    
    // adding jquery to the console
    // var script = document.createElement('script');
    // script.src='https://code.jquery.com/jquery-latest.min.js';
    // document.getElementsByTagName('head')[0].appendChild(script);
 $('.exopite-sof-fieldset').first().mouseout(function(){
    var tempo = $('.chosen-single').first()[0].innerText;
    console.log(tempo + " new1");
});








 //   var dropdown = document.querySelector('select[name="modal_location"]').nextElementSibling;
 //   console.log(dropdown);
    // Add event listener for the change event
//    dropdown.addEventListener("change", function() {
      // Code to execute when the dropdown value changes
  //    console.log("hello");
    //});

    $('.scene_preview').click(function(){ });

   // document.getElementsByName("modal_location")[0].addEventListener("mouseover",() =>{ console.log("Hello");});

    $('.chosen-container').change(function(){ alert("hello");});
    
    $('.chosen-container').change(function(){ alert("hello");});

    
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
    