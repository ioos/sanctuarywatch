/**
 * Utility functions used across javascript files within admin/js folder
 */

// Check if a cookie exists
function cookieExists(cookieName) {
    return document.cookie.split(';').some(cookie => cookie.trim().startsWith(cookieName + '='));
}

// Get a cookie with a specified name
function getCookie(cookieName) {
    const name = cookieName + "=";
    const decodedCookie = decodeURIComponent(document.cookie);
    const cookieArray = decodedCookie.split(';');
    
    for (let i = 0; i < cookieArray.length; i++) {
        let cookie = cookieArray[i].trim();
        if (cookie.indexOf(name) === 0) {
            return cookie.substring(name.length, cookie.length);
        }
    }
    return null;
}

// determine if we are on the correct edit page for a custom post type; a precursor step for further functions to run
function onCorrectEditPage(customPostType) {

    // Get the current URL
    const currentUrl = window.location.href;
    
    // Check if the URL indicates we're editing a post
    const isEditPage = currentUrl.includes('post.php') || currentUrl.includes('post-new.php');
    
    // Look for the post type parameter in the URL
    const urlParams = new URLSearchParams(window.location.search);
    const postType = urlParams.get('post_type') || 'post'; // Default to 'post' if not specified
    
    // For editing existing posts, the post type might not be in the URL
    // In that case, we can rely on the global typenow variable
    const actualPostType = window.typenow || postType;
    
    // Check if we're editing the right kind of custom content post
    if (isEditPage && actualPostType === customPostType) {
        return true;      
    } else {
        return false;
	}
}

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