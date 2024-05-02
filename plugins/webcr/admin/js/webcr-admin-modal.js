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

    function modalIconsDropdown (dropdownElements=[]){
        const iconsDropdown = document.getElementsByName("modal_icons")[0];
        iconsDropdown.innerHTML ='';
        let optionIcon = document.createElement('option');
        optionIcon.text = "Modal Icons";
        optionIcon.value = " ";
        iconsDropdown.add(optionIcon);
        const elementNumber = dropdownElements.length;
        if (elementNumber > 0) {
            for (let i = 0; i <= elementNumber -1; i++){
                let option = document.createElement('option');
                option.value = dropdownElements[i];
                option.text = dropdownElements[i];
                iconsDropdown.appendChild(option);
            }
        }
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

        // Let's remove the preview window if it already exists
		const previewWindow = document.getElementById('preview_window');
		// If the element exists
		if (previewWindow) {
			// Remove the scene window
			previewWindow.parentNode.removeChild(previewWindow);
		}

		let newDiv = document.createElement('div');
		newDiv.id = "preview_window";
		newDiv.classList.add("container");
        let imageRow = document.createElement("div");
        imageRow.classList.add("row", "thirdPreviewRow");
        let imageColumn = document.createElement("div");
        imageColumn.classList.add("col-9");
        imageColumn.id = "previewSvgContainer";

        const protocol = window.location.protocol;
        const host = window.location.host;
        const restURL = protocol + "//" + host  + "/wp-json/wp/v2/scene/" + sceneID + "?_fields=scene_infographic";
        fetch(restURL)
            .then(response => response.json())
            .then(svgJson => {
                svgUrl = svgJson["scene_infographic"];
                if(svgUrl == ""){
                    imageColumn.innerHTML = "No infographic for scene";
                    modalIconsDropdown([]);
                }
                else {
                    fetch(svgUrl)
                        .then(response => response.text())
                        .then(svgContent => {
                            imageColumn.innerHTML = svgContent
                            imageColumn.children[0].id="previewSvg";
                            document.getElementById("previewSvg").removeAttribute("height");

                            const width = imageColumn.clientWidth;
                            document.getElementById("previewSvg").setAttribute('width', width);

                            let iconsLayer = document.getElementById("previewSvg").querySelector('g[id="icons"]');
                            // Initialize an array to hold the sublayer names
                            let sublayers = [];
                            if (iconsLayer) {
                                // Iterate over the child elements of the "icons" layer
                                iconsLayer.childNodes.forEach(node => {
                                    // Check if the node is an element and push its id to the sublayers array
                                    if (node.nodeType === Node.ELEMENT_NODE) {
                                    sublayers.push(node.id);
                                    }
                                });
                                sublayers = sublayers.sort();
                            }
                            modalIconsDropdown(sublayers);
                        })
                }
            })
            .catch((err) => {console.error(err)});
            
            imageRow.appendChild(imageColumn);
            newDiv.appendChild(imageRow);
            document.getElementsByClassName("exopite-sof-field-select")[1].appendChild(newDiv);
    }
}

function modal_icons_change() {
    const iconValue = document.getElementsByName("modal_icons")[0].value;
    const svg = document.getElementById("previewSvg");
    const svgIcons = svg.getElementById("icons");
    const svgIconTarget = svgIcons.querySelector('g[id="' + iconValue + '"]');

    if(svgIcons.querySelector('g[id="icon_highlight"]')){
        svgIcons.querySelector('g[id="icon_highlight"]').remove();
    }

    const svgIconHighlight = svgIconTarget.cloneNode(true);
    svgIconHighlight.id = "icon_highlight";
    svgIconHighlight.style.stroke = "yellow";
    svgIconHighlight.style.strokeWidth = "6";
    svgIcons.prepend(svgIconHighlight);
}

$('.chosen').first().change(modal_location_change);
$( "select[name='modal_scene']" ).change(modal_scene_change);
$( "select[name='modal_icons']" ).change(modal_icons_change);




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
    