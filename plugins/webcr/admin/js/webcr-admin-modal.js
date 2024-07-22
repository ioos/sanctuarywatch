(function( $ ) {
    //	'use strict';
    
    // adding jquery to the console
    // var script = document.createElement('script');
    // script.src='https://code.jquery.com/jquery-latest.min.js';
    // document.getElementsByTagName('head')[0].appendChild(script);

	let opening_scene_info_entries = $(".range[data-depend-id='modal_info_entries']").val();
	displayEntries(opening_scene_info_entries, ".text-class[data-depend-id='modal_info_");
	let opening_scene_photo_entries = $(".range[data-depend-id='modal_photo_entries']").val();
	displayEntries(opening_scene_photo_entries, ".text-class[data-depend-id='modal_photo_");	

    let opening_tab_entries = document.getElementsByName("modal_tab_number")[0].value;
    displayTabEntries(opening_tab_entries);

    // used by modal_scene_change function to determine if the page has just loaded
    let isPageLoad = true;
    function changePageLoad() {
        isPageLoad = false;
    }

    // Use the window.onload event to change isPageLoad to false 3 seconds after page loads 
    window.onload = function() {
        setTimeout(changePageLoad, 1000);
    };

    iconFunction();
    modalWindow();
    modal_scene_change();
    modal_location_change();

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
			let text_field = document.getElementsByName("modal_" + accordionType + targetElement + "[modal_" + accordionType + "_text" + targetElement + "]")[0].value;
			let url_field = document.getElementsByName("modal_" + accordionType + targetElement + "[modal_" + accordionType + "_url" + targetElement + "]")[0].value;

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

    function iconSceneOutDropdown(){
        const modal_location = document.getElementsByName("modal_location")[0].value;
        const iconSceneOut = document.getElementsByName("icon_scene_out")[0];
        iconSceneOut.innerHTML ='';
        let optionIcon = document.createElement('option');
        optionIcon.text = "Icon Scene Out";
        optionIcon.value = " ";
        iconSceneOut.add(optionIcon);

        const modalScene = document.getElementsByName("modal_scene")[0].value;
        if (modalScene != "") {
       //     const modal_location_no_space = urlifyRecursiveFunc(modal_location);
            const protocol = window.location.protocol;
            const host = window.location.host;
            const restURL = protocol + "//" + host  + "/wp-json/wp/v2/scene?_fields=title,id&orderby=title&order=asc&scene_location=" + modal_location;
            fetch(restURL)
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    data.forEach( element => {
                        if (element.id != modalScene){
                            let option = document.createElement('option');
                            option.value = element.id;
                            option.text = element.title.rendered;
                            iconSceneOut.appendChild(option);
                        }
                    });

                })
                .catch(error => console.error('Error fetching data:', error));
        }

    }

    function modalWindow(){
        const iconFunctionValue = document.getElementsByName("icon_function")[0].value;
        if (iconFunctionValue == "Modal"){ 
          //  document.getElementsByName("icon_out_type")[0].value = "External";
            document.getElementsByName("icon_external_url")[0].parentElement.parentElement.style.display = "none";
            document.getElementsByName("icon_external_url")[0].value = "";
            document.getElementsByName("icon_scene_out")[0].value = "";
            document.getElementsByName("icon_scene_out")[0].parentElement.parentElement.style.display = "none";
            document.getElementsByName("modal_tagline")[0].parentElement.parentElement.style.display = "block";
            document.getElementsByName("modal_info_entries")[0].parentElement.parentElement.style.display = "block";
            document.getElementsByName("modal_photo_entries")[0].parentElement.parentElement.style.display = "block";
            document.getElementsByName("modal_tab_number")[0].parentElement.parentElement.style.display = "block";
            document.getElementsByClassName("modal_preview")[0].parentElement.parentElement.style.display = "block";
        } else {

            document.getElementsByName("modal_tagline")[0].parentElement.parentElement.style.display = "none";

            // Set the Modal Info entries to 0, run displayEntries to hide all of the resulting Modal Info fields 
            // and then hide the Modal Info range 
            document.getElementsByName("modal_info_entries")[0].value = 0;
            document.getElementsByName("modal_info_entries")[0].nextSibling.value = 0;
            displayEntries(0, ".text-class[data-depend-id='modal_info_");
            document.getElementsByName("modal_info_entries")[0].parentElement.parentElement.style.display = "none";

            // Set the Modal Photo entries to 0, run displayEntries to hide all of the resulting Modal Photo fields 
            // and then hide the Modal Photo range 
            document.getElementsByName("modal_photo_entries")[0].value = 0;
            document.getElementsByName("modal_photo_entries")[0].nextSibling.value = 0;
            displayEntries(0, ".text-class[data-depend-id='modal_photo_");
            document.getElementsByName("modal_photo_entries")[0].parentElement.parentElement.style.display = "none";

            // Set the Modal Tab entries to 0, run displayTabEntries to hide all of the resulting Modal Tab fields 
            // and then hide the Modal Tab range 
            document.getElementsByName("modal_tab_number")[0].value = 0;
            document.getElementsByName("modal_tab_number")[0].nextSibling.value = 0;
            displayTabEntries(0);
            document.getElementsByName("modal_tab_number")[0].parentElement.parentElement.style.display = "none";

            // Turn off the Modal preview button
            document.getElementsByClassName("modal_preview")[0].parentElement.parentElement.style.display = "none";
        }
    }

    function iconFunction(){
        let iconFunctionType = document.getElementsByName("icon_function")[0].value;
        switch (iconFunctionType){
            case "External URL":
                document.getElementsByName("icon_scene_out")[0].value = "";
                document.getElementsByName("icon_scene_out")[0].parentElement.parentElement.style.display = "none";
                document.getElementsByName("icon_external_url")[0].parentElement.parentElement.style.display = "block";
                break;
            case "Modal":
                document.getElementsByName("icon_scene_out")[0].value = "";
                document.getElementsByName("icon_external_url")[0].value = "";
                document.getElementsByName("icon_scene_out")[0].parentElement.parentElement.style.display = "none";
                document.getElementsByName("icon_external_url")[0].parentElement.parentElement.style.display = "none";
                break;
            case "Scene":
                document.getElementsByName("icon_external_url")[0].value = "";
                document.getElementsByName("icon_scene_out")[0].parentElement.parentElement.style.display = "block";
                document.getElementsByName("icon_external_url")[0].parentElement.parentElement.style.display = "none";
                break;
        }
        modalWindow();
    }

    function displayTabEntries (entry_number){
        let target_element = "";
		for (let i = 6; i > entry_number; i--){
			target_element = "modal_tab_title" + i;
            document.getElementsByName(target_element)[0].parentElement.parentElement.style.display = "none";
            document.getElementsByName(target_element)[0].value = "";
		}

		for (let i = 1; i <= entry_number; i++){
			target_element = "modal_tab_title" + i;
            document.getElementsByName(target_element)[0].parentElement.parentElement.style.display = "block";
		}
	}

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

    function modalSceneDropdown (dropdownElements=[]){


        const sceneDropdown = document.getElementsByName("modal_scene")[0];
        console.log("sceneDropdown: "+ sceneDropdown.value);
        console.log(!(sceneDropdown.value > 0));
     //   if (!(sceneDropdown.value > 0)) {

            sceneDropdown.innerHTML ='';
            let optionScene = document.createElement('option');
            optionScene.text = "Modal Scene";
            optionScene.value = "";
            sceneDropdown.add(optionScene);
            const elementNumber = dropdownElements.length;
            console.log("elementNumber: "+ elementNumber);
            if (elementNumber > 0) {
                for (let i = 0; i <= elementNumber -1; i++){
                    let option = document.createElement('option');
                    option.value = dropdownElements[i][0];
                    option.text = dropdownElements[i][1];
                    sceneDropdown.appendChild(option);
                }
       //     }

            }
    }

    function modalIconsDropdown (dropdownElements=[]){
        const iconsDropdown = document.getElementsByName("modal_icons")[0];
        iconsDropdown.innerHTML ='';
        let optionIcon = document.createElement('option');
        optionIcon.text = "Icons";
        optionIcon.value = "";
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

    if (isPageLoad == false){
        // Let's remove the preview window if it already exists
		const previewWindow = document.getElementById('preview_window');
		// If the element exists
		if (previewWindow) {
			// Remove the scene window
			previewWindow.parentNode.removeChild(previewWindow);
		}

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
                    let sceneArray = [];
                    let newRow;
                    jsonData.forEach(element => {
                        newRow = [element["id"], element["title"]["rendered"]];
                        sceneArray.push(newRow)
                    });
                    console.log(sceneArray);
                    modalSceneDropdown(sceneArray);

                    const iconsDropdown = document.getElementsByName("modal_icons")[0];
                    iconsDropdown.innerHTML ='';
                    iconsDropdown.value ='';
                    let optionIcon = document.createElement('option');
                    optionIcon.text = "Icons";
                    optionIcon.value = "";
                    iconsDropdown.add(optionIcon);

                })
                .catch(error => console.error('Error fetching data:', error));
        }
    }
}

function modal_scene_change(){
    const sceneID = $( "select[name='modal_scene']" ).val();

    if (sceneID != "" && sceneID != null) {
        if (!isPageLoad){
            iconSceneOutDropdown();
        }
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

        const modalInstance = document.getElementsByName("modal_location")[0].value;
        const restHoverColor = protocol + "//" + host  + "/wp-json/wp/v2/instance/" + modalInstance;

        fetch(restHoverColor)
            .then(response => response.json())
            .then(data => {
                const rawHoverColorString = data['instance_hover_color'];
                let hoverColor = "yellow"; 
                if (rawHoverColorString) {
                    hoverColor = rawHoverColorString;
                    const commaIndex = hoverColor.indexOf(',');
                    if (commaIndex !== -1) {
                        hoverColor = hoverColor.substring(0, commaIndex);
                    }
                }
                return fetch(restURL);
            })
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

                            if (isPageLoad == true) {
                                const iconValue = document.getElementsByName("modal_icons")[0].value;

                                if (iconValue != null && iconValue != " "){
                                    let svgIcons = imageColumn.querySelector('g[id="icons"]');
                                    let svgIconTarget = svgIcons.querySelector('g[id="' + iconValue + '"]');
                                    const svgIconHighlight = svgIconTarget.cloneNode(true);
                                    svgIconHighlight.id = "icon_highlight";
                                    svgIconHighlight.style.stroke = hoverColor; // "yellow";
                                    svgIconHighlight.style.strokeWidth = "6";
                                    svgIcons.prepend(svgIconHighlight);
                                }
                            }

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
                            if (isPageLoad == false) {
                                modalIconsDropdown(sublayers);
                            }
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

    if (iconValue != null && iconValue != " "){

        let svg = document.getElementById("previewSvg");

        let svgIcons = svg.getElementById("icons");

        if(svgIcons.querySelector('g[id="icon_highlight"]')){
            svgIcons.querySelector('g[id="icon_highlight"]').remove();
        }

        let svgIconTarget = svgIcons.querySelector('g[id="' + iconValue + '"]');

        const protocol = window.location.protocol;
        const host = window.location.host;
        const modalInstance = document.getElementsByName("modal_location")[0].value;
        const restHoverColor = protocol + "//" + host  + "/wp-json/wp/v2/instance/" + modalInstance;


        fetch(restHoverColor)
            .then(response => response.json())
            .then(data => {
                const rawHoverColorString = data['instance_hover_color'];
                let hoverColor = "yellow"; 
                if (rawHoverColorString) {
                    hoverColor = rawHoverColorString;
                    const commaIndex = hoverColor.indexOf(',');
                    if (commaIndex !== -1) {
                        hoverColor = hoverColor.substring(0, commaIndex);
                    }
                }


        const svgIconHighlight = svgIconTarget.cloneNode(true);
        svgIconHighlight.id = "icon_highlight";
        svgIconHighlight.style.stroke = hoverColor; //"yellow";
        svgIconHighlight.style.strokeWidth = "6";
        svgIcons.prepend(svgIconHighlight);
            })


    }
}

$('.chosen').first().change(modal_location_change);
$( "select[name='modal_scene']" ).change(modal_scene_change);
$( "select[name='modal_icons']" ).change(modal_icons_change);
$( "select[name='icon_function']" ).change(iconFunction);

$(".range[data-depend-id='modal_tab_number']").change(function(){ 
    let opening_tab_entries = document.getElementsByName("modal_tab_number")[0].value;
    displayTabEntries(opening_tab_entries);
});

$(".range[data-depend-id='modal_info_entries']").change(function(){ 
    let number_of_scene_info_entries = $(".range[data-depend-id='modal_info_entries']").val();
    displayEntries(number_of_scene_info_entries, ".text-class[data-depend-id='modal_info_");
});

$(".range[data-depend-id='modal_photo_entries']").change(function(){ 
    let number_of_scene_info_entries = $(".range[data-depend-id='modal_photo_entries']").val();
    displayEntries(number_of_scene_info_entries, ".text-class[data-depend-id='modal_photo_");
});

$('.modal_preview').click(function(){ 

    // Let's remove the preview window if it already exists
    var previewWindow = document.getElementById('preview_window');
    // If the element exists
    if (previewWindow) {
        // Remove the scene window
        previewWindow.parentNode.removeChild(previewWindow);
    }

    // Find element
    const firstModalPreview = document.querySelector('.modal_preview');

    // Find the second parent element
    const secondParent = firstModalPreview.parentElement.parentElement;

    // Create a new div element
    let newDiv = document.createElement('div');
    newDiv.id = "preview_window";
    newDiv.classList.add("container", "modal_preview");

    let modalTitle = document.createElement("div");

    let h4 = document.createElement('h4');
    h4.textContent = document.getElementById("title").value

    let closeButton = document.createElement('span');
    closeButton.classList.add("close");
    closeButton.innerHTML = "&times;";

    modalTitle.appendChild(closeButton);
    modalTitle.appendChild(h4);
    newDiv.appendChild(modalTitle);

    let secondRow = document.createElement("div");
    secondRow.classList.add("row", "modalSecondRow");

    // check to see if any photo link and info link fields are not empty

    let modal_info_elements = [];
    let modal_photo_elements = [];
    let text_field;
    let url_field;
    let haveAccordions = false;
    for (let i = 1; i < 7; i++){
        text_field = "modal_photo" + i + "[modal_photo_text" + i + "]";
        url_field = "modal_photo" + i + "[modal_photo_url" + i + "]";
        if (document.getElementsByName(text_field)[0].value != "" && document.getElementsByName(url_field)[0].value != ""){
            modal_photo_elements.push(i);
        }
        text_field = "modal_info" + i + "[modal_info_text" + i + "]";
        url_field = "modal_info" + i + "[modal_info_url" + i + "]";
        if (document.getElementsByName(text_field)[0].value != "" && document.getElementsByName(url_field)[0].value != ""){
            modal_info_elements.push(i);
        }
    }

    if (modal_info_elements.length > 0 || modal_photo_elements.length > 0) {
        haveAccordions = true;
    }

    if (haveAccordions === true){
        let firstColumn = document.createElement("div");
        firstColumn.classList.add("col-2", "accordion");
        firstColumn.id = "allAccordions";
        
        if (modal_info_elements.length > 0) {
            createAccordion("info", firstColumn,modal_info_elements);
        }
    
        if (modal_photo_elements.length > 0) {
            createAccordion("photo", firstColumn, modal_photo_elements);
        }
        
        secondRow.appendChild(firstColumn);

    }
    let secondColumn = document.createElement("div");
    if (haveAccordions == true){
        secondColumn.classList.add("col-10");
    } else {
        secondColumn.classList.add("col-12");
    }
    secondColumn.textContent = document.getElementsByName('modal_tagline')[0].value;
    secondColumn.classList.add("sceneTagline");
    secondRow.appendChild(secondColumn);

    newDiv.appendChild(secondRow);

    const modalTabNumber = document.getElementsByName("modal_tab_number")[0].value;
    if (modalTabNumber > 0) {
        let thirdRow = document.createElement("div");
        thirdRow.classList.add("row", "modalThirdRow");
        let tabHolder = document.createElement("ul");
        tabHolder.classList.add("nav", "nav-tabs");
        for (let i = 1; i <= modalTabNumber; i++){
            let optionTab = document.createElement("li");
            optionTab.classList.add("nav-item");
            let clickableTitle = document.createElement("a");
            clickableTitle.classList.add("nav-link");
            if (i==1){
                clickableTitle.classList.add("active");
                clickableTitle.setAttribute("aria-current", "page");
            }
            clickableTitle.href = "#modal" + i;
            let visibleText = document.getElementsByName("modal_tab_title" + i)[0].value;
            clickableTitle.textContent = visibleText;
            optionTab.appendChild(clickableTitle);
            tabHolder.appendChild(optionTab);
        }
        thirdRow.appendChild(tabHolder);
        newDiv.appendChild(thirdRow);

        let fourthRow = document.createElement("div");
        fourthRow.classList.add("tab-content");
        fourthRow.style.color = "white";
        for (let i = 1; i <= modalTabNumber; i++){
            let panelTab = document.createElement("div");
            panelTab.id = "modal" + i;
            panelTab.classList.add("tab-pane", "fade");
            if (i==1) {
                panelTab.classList.add("show", "active");
            }
            panelTab.textContent = i;
            fourthRow.appendChild(panelTab);
        }
        newDiv.appendChild(fourthRow);
    }
    secondParent.appendChild(newDiv);

});


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
    