(function( $ ) {
    //	'use strict';
    
    // adding jquery to the console
    // var script = document.createElement('script');
    // script.src='https://code.jquery.com/jquery-latest.min.js';
    // document.getElementsByTagName('head')[0].appendChild(script);

    function modalSceneDropdown (dropdownElements=[]){
        const sceneDropdown = document.getElementsByName("modal_scene")[0];
        sceneDropdown.innerHTML ='';
        let optionScene = document.createElement('option');
        optionScene.text = "Modal Scene";
        optionScene.value = " ";
        sceneDropdown.add(optionScene);
        const elementNumber = dropdownElements.length;
        if (elementNumber > 0) {
            for (let i = 0; i <= elementNumber -1; i++){
                let option = document.createElement('option');
                option.value = dropdownElements[i][0];
                option.text = dropdownElements[i][1];
                sceneDropdown.appendChild(option);
            }
        }
    }

        // Function to resize the SVG
    function resizeSvg() {
		// Get the SVG element
		const svg = document.getElementById('previewSvg');
  
		// Get the parent div (with class "col-10")
		const svgContainer = document.getElementById('previewSvgContainer');
  
		// Set SVG width to match the container width
		const width = svgContainer.clientWidth;
		svg.setAttribute('width', width);
	  }

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
        fetch(restURL)
            .then(response => response.json())
            .then(data => {
                // Variable to hold the JSON object
                const jsonData = data;

                // Now you can use the jsonData variable to access the JSON object
                let sceneArray = [];
                let newRow;
                jsonData.forEach(element => {
                    newRow = [element["id"], element["title"]["rendered"]];
                    sceneArray.push(newRow)
                });
                modalSceneDropdown(sceneArray);
            })
            .catch(error => console.error('Error fetching data:', error));
    }
}

function modal_scene_change(){
    const sceneID = $( "select[name='modal_scene']" ).val();

    if (sceneID != " ") {
        const protocol = window.location.protocol;
        const host = window.location.host;
        const restURL = protocol + "//" + host  + "/wp-json/wp/v2/scene/" + sceneID + "?_fields=scene_infographic";
        console.log(restURL);
        fetch(restURL)
    //        .then(response => response.text())
            .then(data => {
                // Let's remove the preview window if it already exists
                var previewWindow = document.getElementById('preview_window');
                // If the element exists
                if (previewWindow) {
                    // Remove the scene window
                    previewWindow.parentNode.removeChild(previewWindow);
                }
                // Variable to hold the JSON object
                const jsonData = data.url;
                console.log(data.url);
            
                let newDiv = document.createElement("div");
                newDiv.id = "preview_window";
                newDiv.classList.add("container");
                if (jsonData == ""){
                    newDiv.innerHTML = "No infographic for scene";
                } else {

                    // Fetch the remote SVG file
                    fetch(jsonData)
                    .then(response => response.text())
                    .then(svgContent => {
                        const svgElement = new DOMParser().parseFromString(svgContent, 'image/svg+xml').documentElement;

                        // Create a temporary div to hold the SVG content
                        let thirdRow = document.createElement("div");
                        thirdRow.classList.add("row", "thirdPreviewRow");
                        
                        let imageColumn = document.createElement("div");
                        imageColumn.classList.add("col-9");
                        imageColumn.innerHTML = svgElement;
                        imageColumn.id = "previewSvgContainer";

                        thirdRow.append(imageColumn);
                        document.getElementById("previewSvgContainer").children[0].id = "previewSvg";

                        document.getElementById("previewSvgContainer").children[0].classList.add("previewSvg");
                        document.getElementById("previewSvgContainer").children[0].removeAttribute("height");
                        resizeSvg();

                    })
                    .catch(error => {
                        console.error('Error fetching or processing SVG:', error);
                    });



                    newDiv.innerHTML = "infographic";
                }

                document.getElementsByClassName("exopite-sof-field-select")[1].appendChild(newDiv);
                // Now you can use the jsonData variable to access the JSON object
                console.log(jsonData);
            })
            .catch(error => console.error('Error fetching data:', error));
    }

}

$('.chosen').first().change(modal_location_change);
$( "select[name='modal_scene']" ).change(modal_scene_change);




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
    