/**
 * Utility functions used across javascript files within admin/js folder
 */

/**
 * WordPress Admin Field Formatter
 * 
 * This function enhances the WordPress admin interface by:
 * 1. Adding an informational header to the first content section
 * 2. Highlighting required fields by coloring titles that end with an asterisk
 * 
 * @function redText
 * @description Adds an instructional header and visually marks required fields in WordPress admin forms
 * @returns {void} This function does not return a value
 * @example
 * // Call the function when the DOM is loaded
 * document.addEventListener('DOMContentLoaded', redText);
 */
function redText () {

    // Find only the first element with class "exopite-sof-content"
    const contentElement = document.querySelector('.exopite-sof-content');



    // Check if the element exists before proceeding
    if (contentElement) {

        
        // Create the new h4 element
        const infoHeader = document.createElement('h4');
        
        // Set the text content
        infoHeader.textContent = "Required fields have red titles with asterisks at the end.";
        
        // Style the text color red
        infoHeader.style.color = 'red';
        infoHeader.style.padding = '15px 0px 0px 30px';

        // Insert at the beginning of the content element
        contentElement.insertBefore(infoHeader, contentElement.firstChild);

        let sceneStatusExists = false;

        // Check if "Scene Status*" is in the content
        document.querySelectorAll('.exopite-sof-title').forEach(function(el) {
            const titleText = el.textContent.trim();
            if (titleText == "Scene Status*Should the Scene be live?") {
                sceneStatusExists = true;
            }
        });

        // If Overview Scene is missing, append a paragraph with the message
        if (sceneStatusExists == true) {
            const overviewSceneMessage = document.createElement('p');
            overviewSceneMessage.textContent = "⦁ To set this scene to as an overview scene, please select an instance below, save this post, then go to the instance and set this scene as the 'Overview Scene'.";
            overviewSceneMessage.style.color = 'red';
            overviewSceneMessage.style.padding = '15px 0px 0px 30px';

            contentElement.insertBefore(overviewSceneMessage, infoHeader.nextSibling);
        }
    }

    // Find all h4 elements with class "exopite-sof-title"
    const titleElements = document.querySelectorAll('h4.exopite-sof-title');
    
    // Loop through each matching element
    titleElements.forEach(function(element) {
    // Get the text content of the h4 element, excluding the p element
    // We find the first text node as it contains the title text
    const titleNode = Array.from(element.childNodes).find(node => 
        node.nodeType === Node.TEXT_NODE);
    
    if (titleNode) {
        // Extract and trim the text from the node
        const titleText = titleNode.textContent.trim();
        
        // Check if the text exists and its last character is an asterisk
        if (titleText.length > 0 && titleText[titleText.length - 1] === '*') {
        // Create a span element to wrap the text content
        const span = document.createElement('span');
        // Set the span's text to the original title text
        span.textContent = titleText;
        // Apply red color styling to the span
        span.style.color = 'red';
        
        // Replace the original text node with our styled span
        // This preserves the DOM structure while applying the style
        element.replaceChild(span, titleNode);
        }
    }
    });
}

// Show relevant photo and info fields for scene and modal forms
function displayEntries (entry_number, string_prefix){

	for (let i = 6; i > entry_number; i--){
		let target_text = string_prefix + "text" + i + "']";
		let target_text_div = document.querySelector(target_text);
		target_text_div.value ="";
		let target_url = string_prefix + "url" + i + "']";
		let target_url_div = document.querySelector(target_url);
		target_url_div.value ="";
		target_text_div.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.style.display="none";
	}

	for (let i = 1; i <= entry_number; i++){
		let target_text = string_prefix + "text" + i + "']";
		let target_text_div = document.querySelector(target_text);
		target_text_div.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.style.display="block";
	}
}