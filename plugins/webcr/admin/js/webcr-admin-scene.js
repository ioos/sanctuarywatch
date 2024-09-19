// These functions only fire upon editing or creating a post of Scene custom content type
(function( $ ) {
	'use strict';

	//displayPhotoPath(1);

	let openingSceneSections = document.getElementsByName("scene_section_number")[0].value;
	displaySceneEntries(openingSceneSections);

	   // function to display Scene Section fields
	function displaySceneEntries (entry_number){
		let target_element = "";
		for (let i = 6; i > entry_number; i--){
			target_element = "scene_section" + i;
			document.getElementsByName(target_element)[0].parentElement.parentElement.style.display = "none";
			document.getElementsByName(target_element)[0].value = "";
		}

		for (let i = 1; i <= entry_number; i++){
			target_element = "scene_section" + i;
			document.getElementsByName(target_element)[0].parentElement.parentElement.style.display = "block";
		}
	}

	// Function to display either URL or image under scene image link
	function displayPhotoPath (fieldNumber){
		const targetElement = "scene_photo" + fieldNumber + "[scene_photo_location" + fieldNumber + "]";
		const targetLocation = document.getElementsByName(targetElement)[0];
		const imageElement = '[data-depend-id="scene_photo_internal' + fieldNumber + '"]';
		const imageField = document.querySelector(imageElement);
		const urlElement = "scene_photo" + fieldNumber + "[scene_photo_url" + fieldNumber + "]";
		const urlField = document.getElementsByName(urlElement)[0];
		if (targetLocation.value == "Internal"){
			urlField.value = "";
			urlField.parentElement.parentElement.style.display = "none";
			imageField.parentElement.parentElement.style.display="block";
		} else if (targetLocation.value == "External"){
			imageField.children[1].value = "";
			imageField.children[0].children[0].children[1].src="";
			imageField.children[0].classList.add("hidden");
			imageField.parentElement.parentElement.style.display = "none";
			urlField.parentElement.parentElement.style.display="block";

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
  

	function createAccordion(accordionType, parentDiv, listElements){

		let accordionItem = document.createElement("div");
		accordionItem.classList.add("accordion-item");

		let accordionFirstPart = document.createElement("div");
		accordionFirstPart.classList.add("accordion-header");

		let accordionHeaderButton = document.createElement("button");
		accordionHeaderButton.classList.add("accordion-button", "accordionTitle");
		accordionHeaderButton.setAttribute("type", "button");
		accordionHeaderButton.setAttribute("data-bs-toggle", "collapse");
		accordionHeaderButton.setAttribute("data-bs-target", "#collapse" + accordionType);
		accordionHeaderButton.setAttribute("aria-expanded", "true");
		accordionHeaderButton.setAttribute("aria-controls", "collapse" + accordionType);
		if (accordionType == "info"){ 
			accordionHeaderButton.textContent = "More info";
		} else {
			accordionHeaderButton.textContent = "Images";
		}
		accordionFirstPart.appendChild(accordionHeaderButton);
		accordionItem.appendChild(accordionFirstPart);

		let accordionSecondPart = document.createElement("div");
		accordionSecondPart.classList.add("accordion-collapse", "collapse");
		accordionSecondPart.setAttribute("data-bs-parent", "#accordion" + accordionType);
		accordionSecondPart.id = "collapse" + accordionType;

		let accordionBody = document.createElement("div");
		accordionBody.classList.add("accordion_body");

		let accordionList = document.createElement("ul");
		accordionList.classList.add("previewAccordionElements");
		for (let i = 0; i < listElements.length; i++){
			let listItem = document.createElement("li");
			let listLink = document.createElement("a");

			let targetElement = listElements[i];	
			let text_field = document.getElementsByName("scene_" + accordionType + targetElement + "[scene_" + accordionType + "_text" + targetElement + "]")[0].value;
			let url_field = document.getElementsByName("scene_" + accordionType + targetElement + "[scene_" + accordionType + "_url" + targetElement + "]")[0].value;

			listLink.setAttribute("href", url_field);
			listLink.textContent = text_field;
			listLink.setAttribute("target", "_blank");
			listItem.appendChild(listLink);
			accordionList.appendChild(listItem);
		}

		accordionBody.appendChild(accordionList); 
		accordionSecondPart.appendChild(accordionBody);
		accordionItem.appendChild(accordionSecondPart);

		parentDiv.appendChild(accordionItem);
		
	}
	// Create scene preview from clicking on the "Scene preview button"
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
			firstColumn.id = "allAccordions";
			
			if (scene_info_elements.length > 0) {
				createAccordion("info", firstColumn, scene_info_elements);
			}
		
			if (scene_photo_elements.length > 0) {
				createAccordion("photo", firstColumn, scene_photo_elements);
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
		secondColumn.classList.add("sceneTagline");
		secondRow.appendChild(secondColumn);

		newDiv.appendChild(secondRow);

		// add row 
		let thirdRow = document.createElement("div");
		thirdRow.classList.add("row", "thirdPreviewRow");
		
		let imageColumn = document.createElement("div");
		imageColumn.classList.add("col-9");
	//	imageColumn.id = "previewSvgContainer";
		
		let svgPath = document.getElementsByName("scene_infographic")[0].value;
		if (svgPath == ""){
			imageColumn.innerText = "No image.";
			thirdRow.append(imageColumn);
		} else {
			let imageExtension = svgPath.split('.').pop().toLowerCase();
			if (imageExtension != "svg"){
				imageColumn.innerText = "Image is not a svg.";
				thirdRow.append(imageColumn);
			} else {

				const protocol = window.location.protocol;
				const host = window.location.host;
				const sceneInstance = document.getElementsByName("scene_location")[0].value;
				const restHoverColor = protocol + "//" + host  + "/wp-json/wp/v2/instance/" + sceneInstance;

				fetch(restHoverColor)
					.then(response => response.json())
					.then(data => {
						let hoverColor = "yellow"; 
						const rawHoverColorString = data['instance_hover_color'];

						if (rawHoverColorString) {
							hoverColor = rawHoverColorString;
							const commaIndex = hoverColor.indexOf(',');
							if (commaIndex !== -1) {
								hoverColor = hoverColor.substring(0, commaIndex);
							}
						}
						return fetch(svgPath);
					})
				.then(response => response.text())
				.then(svgContent => {
					// Create a temporary div to hold the SVG content
					imageColumn.innerHTML = svgContent;
					imageColumn.id = "previewSvgContainer";

					thirdRow.append(imageColumn);
					document.getElementById("previewSvgContainer").children[0].id = "previewSvg";

					//document.getElementById("previewSvgContainer").children[0].classList.add("previewSvg");
					document.getElementById("previewSvgContainer").children[0].removeAttribute("height");
					resizeSvg();

					// Find the "icons" layer
					let iconsLayer = document.getElementById("previewSvg").querySelector('g[id="icons"]');

					if (iconsLayer) {

						// Initialize an array to hold the sublayers
						let sublayers = [];

						// Iterate over the child elements of the "icons" layer
						iconsLayer.childNodes.forEach(node => {
							// Check if the node is an element and push its id to the sublayers array
							if (node.nodeType === Node.ELEMENT_NODE) {
							sublayers.push(node.id);
							}
						});
						sublayers = sublayers.sort();

						let tocColumn = document.createElement("div");
						tocColumn.classList.add("col-3", "previewSceneTOC");
						let tocList = document.createElement("ul");
						sublayers.forEach (listElement => {
							let tocElement = document.createElement("li");
							tocElement.innerText = listElement;
							tocList.appendChild(tocElement);
						})
						tocColumn.append(tocList);
						thirdRow.append(tocColumn);

						//let's highlight the clickable elements of the svg

						sublayers.forEach (listElement => {
						//	document.getElementById("previewSvg").querySelector('g[id="' + listElement + '"]').classList.add("highlightIcons");
						//console.log  (hoverColor);
							document.getElementById("previewSvg").querySelector('g[id="' + listElement + '"]').style.stroke = "yellow"; //hoverColor; 
							document.getElementById("previewSvg").querySelector('g[id="' + listElement + '"]').style.strokeWidth = "2";
						})

					} else {
						imageColumn.innerText = 'No "icons" layer found in the SVG.';
						thirdRow.append(imageColumn);
					}
				})
				.catch(error => {
					console.error('Error fetching or processing SVG:', error);
				});
	
			}
		}

		newDiv.appendChild(thirdRow);
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

		for (let i = 6; i > entry_number; i--){
			let target_text = string_prefix + "text" + i + "']";
			let target_url = string_prefix + "url" + i + "']";
			$(target_text).parents().eq(6).css("display", "none");
			$(target_text).val(function(){return  "";});
			$(target_url).val(function(){return  "";});
		}

		for (let i = 1; i <= entry_number; i++){
			let target = string_prefix + "text" + i + "']";
			$(target).parents().eq(6).css("display", "block");
		}
	}

	//initialize photopath six times and also set it for onchange of dropdown
	for (let i = 1; i < 7; i++){
		displayPhotoPath(i);
		let targetPhotoElement = 'select[name="scene_photo' + i + '[scene_photo_location' + i + ']"]';
		$(targetPhotoElement).change(function(){
			displayPhotoPath(i);
		});
	}



	$('select[name="scene_section_number"]').change(function(){
		let openingSceneSections = document.getElementsByName("scene_section_number")[0].value;
		displaySceneEntries(openingSceneSections);
	});

	$('select[name="scene_section_number"]').change(function(){
		let openingSceneSections = document.getElementsByName("scene_section_number")[0].value;
		displaySceneEntries(openingSceneSections);
	});

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
