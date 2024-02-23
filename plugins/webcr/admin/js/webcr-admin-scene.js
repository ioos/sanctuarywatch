// These functions only fire upon editing or creating a post of Scene custom content type
(function( $ ) {
	'use strict';

	$('.scene_preview').click(function(){ 

		// Let's remove the preview window if it already exists
		var previewWindow = document.getElementById('preview_window');
		// If the element exists
		if (previewWindow) {
			// Remove the scene window
			previewWindow.parentNode.removeChild(previewWindow);
		}
			
		// Find element
		const firstScenePreview = document.querySelector('.scene_preview');

		// Find the second parent element
		const secondParent = firstScenePreview.parentElement.parentElement;

		// Create a new div element
		let newDiv = document.createElement('div');
		newDiv.id = "preview_window";
		newDiv.classList.add("container");

		// Create an h1 element
		let h1 = document.createElement('h1');
		// Set the text content of the h1 element to "Hello World"
		h1.textContent = document.getElementById("title").value
		// Append the h1 element to the new div
		newDiv.appendChild(h1);

		let secondRow = document.createElement("div");
		secondRow.classList.add("row");

		let firstColumn = document.createElement("div");
		firstColumn.classList.add("col-2");
		firstColumn.textContent = "hello";
		secondRow.appendChild(firstColumn);

		let secondColumn = document.createElement("div");
		secondColumn.classList.add("col-10");
		secondColumn.textContent = document.getElementsByName('scene_tagline')[0].value;
		secondRow.appendChild(secondColumn);

		newDiv.appendChild(secondRow);

		// Append the new div to the second parent element
		secondParent.appendChild(newDiv);


	});

// Run jquery from console 
// var script = document.createElement('script');
// script.src='https://code.jquery.com/jquery-latest.min.js';
// document.getElementsByTagName('head')[0].appendChild(script);

	let opening_scene_info_entries = $(".range[data-depend-id='scene_info_entries']").val();
	displayEntries(opening_scene_info_entries, ".text-class[data-depend-id='scene_info_");
	let opening_scene_photo_entries = $(".range[data-depend-id='scene_photo_entries']").val();
	displayEntries(opening_scene_photo_entries, ".text-class[data-depend-id='scene_photo_");	

	function displayEntries (entry_number, string_prefix){
		if (string_prefix == ".text-class[data-depend-id='photo_info_"){
			console.log("entry_number " + entry_number);
		}
		for (let i = 6; i > entry_number; i--){
			let target_text = string_prefix + "text" + i + "']";
			let target_url = string_prefix + "url" + i + "']";
			if (string_prefix == ".text-class[data-depend-id='photo_info_"){
				console.log(i + " " + target_text + " " + target_url);
			}
			$(target_text).parents().eq(6).css("display", "none");
			$(target_text).val(function(){return  "";});
			$(target_url).val(function(){return  "";});
		}

		for (let i = 1; i <= entry_number; i++){
			let target = string_prefix + "text" + i + "']";
			$(target).parents().eq(6).css("display", "block");
			if (string_prefix == ".text-class[data-depend-id='photo_info_"){
				console.log(i + " " + target);
			}
		}
	}

	$(".range[data-depend-id='scene_info_entries']").change(function(){ 
		let number_of_scene_info_entries = $(".range[data-depend-id='scene_info_entries']").val();
		displayEntries(number_of_scene_info_entries, ".text-class[data-depend-id='scene_info_");
	});

	$(".range[data-depend-id='scene_photo_entries']").change(function(){ 
		let number_of_scene_info_entries = $(".range[data-depend-id='scene_photo_entries']").val();
		displayEntries(number_of_scene_info_entries, ".text-class[data-depend-id='scene_photo_");
	});


	const OnSceneEditPage = document.getElementsByName("scene_tagline").length; //determining if we are on a page where we are editing a scene
	const SceneError = getCookie("scene_post_status");

	if (OnSceneEditPage === 1 && SceneError === "post_error") {
		let SceneFields = JSON.parse(getCookie("scene_error_all_fields"));

		const SceneFieldNames =["scene_location", "scene_infographic", "scene_tagline", "scene_info_entries", "scene_photo_entries"];
		SceneFields["scene_tagline"] = SceneFields["scene_tagline"].replace("\\'","\'");

		SceneFieldNames.forEach((element) => document.getElementsByName(element)[0].value = SceneFields[element]);

		document.getElementsByName("scene_info_entries")[0].parentElement.childNodes[1].value = SceneFields["scene_info_entries"];
		displayEntries(SceneFields["scene_info_entries"], ".text-class[data-depend-id='scene_info_");

		document.getElementsByName("scene_photo_entries")[0].parentElement.childNodes[1].value = SceneFields["scene_photo_entries"];
		displayEntries(SceneFields["scene_photo_entries"], ".text-class[data-depend-id='scene_photo_");

		let elementName;
		let secondElementName;
		const fieldClass = ["info", "photo"];
		for (let i = 1; i < 7; i++){
			fieldClass.forEach((array_value) => {
				elementName = "scene_" + array_value + i + "[scene_" + array_value + "_url" + i + "]";
				secondElementName = "scene_" + array_value + "_url" + i;
				document.getElementsByName(elementName)[0].value = SceneFields[secondElementName];
				elementName = "scene_" + array_value + i + "[scene_" + array_value + "_text" + i + "]";
				secondElementName = "scene_" + array_value + "_text" + i;
				document.getElementsByName(elementName)[0].value = SceneFields[secondElementName];
			});
		}
	}

	function getCookie(cookieName) {
		let cookies = document.cookie;
		let cookieArray = cookies.split("; ");
	 
		for (let i = 0; i < cookieArray.length; i++) {
		   let cookie = cookieArray[i];
		   let [name, value] = cookie.split("=");
		  
		   if (name === cookieName) {
			  return decodeURIComponent(value);
		   }
		}
		
		return null;
	 }


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
