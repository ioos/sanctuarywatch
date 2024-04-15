(function( $ ) {
    //	'use strict';
    
    // adding jquery to the console
    // var script = document.createElement('script');
    // script.src='https://code.jquery.com/jquery-latest.min.js';
    // document.getElementsByTagName('head')[0].appendChild(script);
// $('.exopite-sof-fieldset').first().mouseout(function(){
//    var tempo = $('.chosen-single').first()[0].innerText;
//    console.log(tempo + " new1");
//});


//$('.chosen-results').eq(1)[0].innerHTML = '<li class="active-result result-selected" data-option-array-index="0">Modal Scene</li><li class="active-result" data-option-array-index="1">Channel Islands NMS</li><li class="active-result" data-option-array-index="2">Florida Keys NMS</li><li class="active-result" data-option-array-index="3">Olympic Coast NMS</li><li class="active-result" data-option-array-index="4">Tempo5</li>';


//document.getElementsByName("modal_scene")[0].innerHTML = '<option value="">Modal Scene</option><option value="Channel Islands NMS">Channel Islands NMS</option><option value="Florida Keys NMS">Florida Keys NMS</option><option value="Olympic Coast NMS">Olympic Coast NMS</option><option value="Tempo 5">Tempo 5</option>';



// change spaces to %20
function urlifyRecursiveFunc(str) { 
    if (str.length === 0) { 
        return ''; 
    } 
    if (str[0] === ' ') { 
        return '%20' + urlifyRecursiveFunc(str.slice(1)); 
    } 
    return str[0] + urlifyRecursiveFunc(str.slice(1)); 
} 

function modal_location_change(){
    const modal_location = $('.chosen').first().val();
    if (modal_location != ""){
        const modal_location_no_space = urlifyRecursiveFunc(modal_location);
        const protocol = window.location.protocol;
        const host = window.location.host;
        const restURL = protocol + "//" + host  + "/wp-json/wp/v2/scene?_fields=title,id,scene_location&orderby=title&order=asc&scene_location=" + modal_location_no_space;
        console.log(restURL);
        fetch(restURL)
            .then(response => response.json())
            .then(data => {
                // Variable to hold the JSON object
                const jsonData = data;
                
                // Now you can use the jsonData variable to access the JSON object
                console.log(jsonData.length);
                let sceneArray = [];
                let newRow;
                jsonData.forEach(element => {
                    newRow = [element["id"], element["title"]["rendered"]];
                    sceneArray.push(newRow)
                });
                console.log(sceneArray);
            })
            .catch(error => console.error('Error fetching data:', error));
    }
}

// $('.chosen').first().change(function(){console.log($('.chosen').first().val());})
$('.chosen').first().change(modal_location_change);





 //   var dropdown = document.querySelector('select[name="modal_location"]').nextElementSibling;
 //   console.log(dropdown);
    // Add event listener for the change event
//    dropdown.addEventListener("change", function() {
      // Code to execute when the dropdown value changes
  //    console.log("hello");
    //});


    
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
    