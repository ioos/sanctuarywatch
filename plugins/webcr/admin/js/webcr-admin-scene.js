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

		// check to see if any photo link and info link fields are not empty

		let scene_info_elements = [];
		let scene_photo_elements = [];
		let text_field;
		let url_field;
		let haveAccordions = false;
		for (let i = 1; i < 7; i++){
			text_field = "scene_photo" + i + "[scene_photo_text" + i + "]";
			url_field = "scene_photo" + i + "[scene_photo_url" + i + "]";
			if (document.getElementsByName(text_field)[0].value != "" && document.getElementsByName(url_field)[0].value != ""){
				scene_photo_elements.push(i);
			}
			text_field = "scene_info" + i + "[scene_info_text" + i + "]";
			url_field = "scene_info" + i + "[scene_info_url" + i + "]";
			if (document.getElementsByName(text_field)[0].value != "" && document.getElementsByName(url_field)[0].value != ""){
				scene_info_elements.push(i);
			}
		}

		if (scene_info_elements.length > 0 || scene_photo_elements.length > 0) {
			haveAccordions = true;
		}

		if (haveAccordions === true){
			let firstColumn = document.createElement("div");
			firstColumn.classList.add("col-2", "accordion");
			firstColumn.id = "accordionInfo";
			
			if (scene_info_elements.length > 0) {
				let accordionInfo = document.createElement("div");
				accordionInfo.classList.add("accordion-item");

				let accordionInfoHeader = document.createElement("h4");
				accordionInfoHeader.classList.add("accordion-header");

				let accordionInfoHeaderButton = document.createElement("button");
				accordionInfoHeaderButton.classList.add("accordion-button");
				accordionInfoHeaderButton.setAttribute("type", "button");
				accordionInfoHeaderButton.setAttribute("data-bs-toggle", "collapse");
				accordionInfoHeaderButton.setAttribute("data-bs-target", "#collapseInfo");
				accordionInfoHeaderButton.setAttribute("aria-expanded", "true");
				accordionInfoHeaderButton.setAttribute("aria-controls", "collapseInfo");
				accordionInfoHeaderButton.textContent = "More info";

				accordionInfoHeader.appendChild(accordionInfoHeaderButton);
				accordionInfo.appendChild(accordionInfoHeader);

				let accordionSecondPart = document.createElement("div");
				accordionSecondPart.classList.add("accordion-collapse", "collapse");
				accordionSecondPart.setAttribute("data-bs-parent", "#accordionInfo");
				accordionSecondPart.id = "collapseInfo";

				let accordionBody = document.createElement("div");
				accordionBody.classList.add("accordion=body");

				let accordionList = document.createElement("ul");

				for (let i = 0; i < scene_info_elements.length; i++){
					let listItem = document.createElement("li");
					let listLink = document.createElement("a");

					let targetElement = scene_info_elements[i];	
					text_field = document.getElementsByName("scene_info" + targetElement + "[scene_info_text" + targetElement + "]")[0].value;
					url_field = document.getElementsByName("scene_info" + targetElement + "[scene_info_url" + targetElement + "]")[0].value;

					listLink.setAttribute("href", url_field);
					listLink.textContent = text_field;
					listLink.setAttribute("target", "_blank");
					listItem.appendChild(listLink);
					accordionList.appendChild(listItem);
				}

				accordionBody.appendChild(accordionList); 
				accordionSecondPart.appendChild(accordionBody);
				accordionInfo.appendChild(accordionSecondPart);

				firstColumn.appendChild(accordionInfo);
			}
		
			if (scene_photo_elements.length > 0) {
				let accordionPhoto = document.createElement("div");
				accordionPhoto.classList.add("accordion-item");

				let accordionPhotoHeader = document.createElement("h4");
				accordionPhotoHeader.classList.add("accordion-header");

				let accordionPhotoHeaderButton = document.createElement("button");
				accordionPhotoHeaderButton.classList.add("accordion-button");
				accordionPhotoHeaderButton.setAttribute("type", "button");
				accordionPhotoHeaderButton.setAttribute("data-bs-toggle", "collapse");
				accordionPhotoHeaderButton.setAttribute("data-bs-target", "#collapsePhoto");
				accordionPhotoHeaderButton.setAttribute("aria-expanded", "true");
				accordionPhotoHeaderButton.setAttribute("aria-controls", "collapsePhoto");
				accordionPhotoHeaderButton.textContent = "Images";

				accordionPhotoHeader.appendChild(accordionPhotoHeaderButton);
				accordionPhoto.appendChild(accordionPhotoHeader);

				let accordionSecondPart = document.createElement("div");
				accordionSecondPart.classList.add("accordion-collapse", "collapse");
				accordionSecondPart.setAttribute("data-bs-parent", "#accordionPhoto");
				accordionSecondPart.id = "collapsePhoto";

				let accordionBody = document.createElement("div");
				accordionBody.classList.add("accordion=body");

				let accordionList = document.createElement("ul");

				for (let i = 0; i < scene_photo_elements.length; i++){
					let listItem = document.createElement("li");
					let listLink = document.createElement("a");

					let targetElement = scene_photo_elements[i];	
					text_field = document.getElementsByName("scene_photo" + targetElement + "[scene_photo_text" + targetElement + "]")[0].value;
					url_field = document.getElementsByName("scene_photo" + targetElement + "[scene_photo_url" + targetElement + "]")[0].value;

					listLink.setAttribute("href", url_field);
					listLink.textContent = text_field;
					listLink.setAttribute("target", "_blank");
					listItem.appendChild(listLink);
					accordionList.appendChild(listItem);
				}

				accordionBody.appendChild(accordionList); 
				accordionSecondPart.appendChild(accordionBody);
				accordionPhoto.appendChild(accordionSecondPart);

				firstColumn.appendChild(accordionPhoto);
			}
			
			secondRow.appendChild(firstColumn);

		}
		let secondColumn = document.createElement("div");
		if (haveAccordions == true){
			secondColumn.classList.add("col-10");
		} else {
			secondColumn.classList.add("col-12");
		}
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
