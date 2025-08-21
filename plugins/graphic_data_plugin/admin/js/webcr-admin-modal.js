'use strict';

// the last stop in the field validation process (if needed)
replaceFieldValuesWithTransientValues();


let hoverColor = "red"; // hacky solution to solving problem of hoverColor in promise. FIX

// Makes title text red if it ends with an asterisk in "exopite-sof-title" elements. Also adds a line giving the meaning of red text at top of form.
document.addEventListener('DOMContentLoaded', redText);

let opening_scene_info_entries = document.querySelector(".range[data-depend-id='modal_info_entries']").value;
displayEntries(opening_scene_info_entries, ".text-class[data-depend-id='modal_info_");
let opening_scene_photo_entries = document.querySelector(".range[data-depend-id='modal_photo_entries']").value;
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



hideIconSection();


// If a given Scene does not have any sections, then let's hide the Icon Section field in the modal page
function hideIconSection (){
    const sectionField = document.getElementsByName("icon_toc_section")[0];
    if (sectionField.options.length < 2){
        sectionField.parentElement.parentElement.style.display = "none";
    } else {
        sectionField.parentElement.parentElement.style.display = "block";
    }
}

// Function to display either URL or image under scene image link
function displayPhotoPath (fieldNumber){
    const targetElement = "modal_photo" + fieldNumber + "[modal_photo_location" + fieldNumber + "]";
    const targetLocation = document.getElementsByName(targetElement)[0];
    const imageElement = '[data-depend-id="modal_photo_internal' + fieldNumber + '"]';
    const imageField = document.querySelector(imageElement);
    const urlElement = "modal_photo" + fieldNumber + "[modal_photo_url" + fieldNumber + "]";
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

//initialize photopath six times and also set it for onchange of dropdown
for (let i = 1; i < 7; i++){
    displayPhotoPath(i);
    let targetPhotoElement = document.querySelector('select[name="modal_photo' + i + '[modal_photo_location' + i + ']"]');
    targetPhotoElement.addEventListener("change", function() {
        displayPhotoPath(i);
    });
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

    const modalScene = document.getElementsByName("modal_scene")[0].value;
    if (modalScene != "") {
    //     const modal_location_no_space = urlifyRecursiveFunc(modal_location);
        const protocol = window.location.protocol;
        const host = window.location.host;
        const restURL = protocol + "//" + host  + "/wp-json/wp/v2/scene?_fields=title,id&orderby=title&order=asc&per_page=100&scene_location=" + modal_location;
        fetch(restURL)
            .then(response => response.json())
            .then(data => {
                let option = document.createElement('option');
                option.value = "";
                option.text = "";
                option.selected = true;
                iconSceneOut.appendChild(option);
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
        displayTabEntries(document.getElementsByName("modal_tab_number")[0].value);
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
        document.getElementsByName("modal_tab_number")[0].value = 1;
        document.getElementsByName("modal_tab_number")[0].nextSibling.value = 1;
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

function modalSceneDropdown (dropdownElements=[]){

    const sceneDropdown = document.getElementsByName("modal_scene")[0];
    //   if (!(sceneDropdown.value > 0)) {

        sceneDropdown.innerHTML ='';
        let optionScene = document.createElement('option');
        optionScene.text = "";
        optionScene.value = "";
        sceneDropdown.add(optionScene);
        const elementNumber = dropdownElements.length;
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
    const currentFieldValue = iconsDropdown.value;
    iconsDropdown.innerHTML ='';
    let optionIcon = document.createElement('option');
    optionIcon.text = "";
    optionIcon.value = "";
    iconsDropdown.add(optionIcon);
    const elementNumber = dropdownElements.length;
    if (elementNumber > 0) {
        for (let i = 0; i <= elementNumber -1; i++){
            let option = document.createElement('option');
            option.value = dropdownElements[i];
            option.text = dropdownElements[i];
            if (option.value == currentFieldValue){
                option.selected = true;
            }
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

        // let's remove all options from the Icon Section field and make it invisible
        const sectionField = document.getElementsByName("icon_toc_section")[0];
        sectionField.innerHTML ='';
        sectionField.value =''; 
        sectionField.parentElement.parentElement.style.display = "none";

        // Let's remove the preview window if it already exists
		const previewWindow = document.getElementById('preview_window');
		// If the element exists
		if (previewWindow) {
			// Remove the scene window
			previewWindow.parentNode.removeChild(previewWindow);
		}
        const modal_location = document.querySelector('select[name="modal_location"]').value;
        if (modal_location != " " && modal_location != ""){

            const modal_location_no_space = urlifyRecursiveFunc(modal_location);
            const protocol = window.location.protocol;
            const host = window.location.host;
            const restURL = protocol + "//" + host  + "/wp-json/wp/v2/scene?_fields=title,id,scene_location&orderby=title&order=asc&per_page=100&scene_location=" + modal_location_no_space;
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

                    const iconsDropdown = document.getElementsByName("modal_icons")[0];
                    iconsDropdown.innerHTML ='';
                    iconsDropdown.value ='';
                    let optionIcon = document.createElement('option');
                    optionIcon.text = " ";
                    optionIcon.value = "";
                    iconsDropdown.add(optionIcon);

                })
                .catch(error => console.error('Error fetching data:', error));
        }
    }
}

// Change the options for the select field with the name icon_toc_section when the scene changes. 
// This is done to reflect the sections associated with the new scene
function modal_section_options (){
    const sceneID = document.getElementsByName("modal_scene")[0].value;
    let modalSection = document.getElementsByName("icon_toc_section")[0];
    modalSection.innerHTML ='';
    modalSection.value ='';
  
    let sceneSection = "";
    for (let i = 1; i < 7; i++){
        sceneSection = sceneSection + "scene_section" + i + ",";    
    }
    sceneSection = sceneSection.slice(0, -1);

    const protocol = window.location.protocol;
    const host = window.location.host;
    const restURL = protocol + "//" + host  + "/wp-json/wp/v2/scene/" + sceneID + "?_fields=title,id,scene_toc_style,scene_section_number," + sceneSection;
    fetch(restURL)
    .then(response => response.json())
    .then(data => {   
        const sceneTocStyle = data["scene_toc_style"];
        const sceneSectionNumber = parseInt(data["scene_section_number"]);
        if (sceneTocStyle != "list" && sceneSectionNumber > 0){
            let option = document.createElement('option');
            option.text = "";
            option.value = "";
            modalSection.add(option);

            for (let j = 1; j <= sceneSectionNumber; j++){
                let option = document.createElement('option');
                option.value = j;
                option.text = data["scene_section" + j]["scene_section_title" + j];   
                modalSection.add(option);
            }

            // let's set the value of the icon_toc_section field to the value of the icon_toc_section field in the database
            const modalId = document.querySelector('input[name="post_ID"]');

            if (modalId && modalId.value) {
              const restURL2 = protocol + "//" + host  + "/wp-json/wp/v2/modal/" + modalId.value + "?_fields=id,icon_toc_section";
              fetch(restURL2)
                .then(response => response.json())
                .then(data => {
                    const iconTocSection = data["icon_toc_section"];
                    if (iconTocSection != null && iconTocSection != "") {
                        modalSection.value = iconTocSection;
                    }
                })
                .catch(error => console.error('Error fetching data:', error));
            }
        } 
        hideIconSection();
    })
    .catch(error => console.error('Error fetching data:', error));
}

function modal_scene_change(){

    // Let's remove the preview window if it already exists
    const previewWindow = document.getElementById('preview_window');
    // If the element exists
    if (previewWindow) {
        // Remove the scene window
        previewWindow.parentNode.removeChild(previewWindow);
    }

    const sceneID = document.querySelector("select[name='modal_scene']").value;

    if (sceneID != " " && sceneID != "" && sceneID != null) {
        if (!isPageLoad){
            iconSceneOutDropdown();
        }

        modal_section_options();

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
        const restURL = protocol + "//" + host  + "/wp-json/wp/v2/scene/" + sceneID + "?_fields=scene_infographic&per_page=100";

        const restHoverColor = protocol + "//" + host  + "/wp-json/wp/v2/scene/" + sceneID + "?_fields=scene_hover_color";

        fetch(restHoverColor)
            .then(response => response.json())
            .then(data => {
                const rawHoverColorString = data['scene_hover_color'];
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
                const svgUrl = svgJson["scene_infographic"];
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

                                if (iconValue != null && iconValue != ""){
                                    let svgIcons = imageColumn.querySelector('g[id="icons"]');
                                    let svgIconTarget = svgIcons.querySelector('g[id="' + iconValue + '"]');

                                    // Select all child elements 
                                    let subElements = svgIconTarget.querySelectorAll("*");

                                    // Loop through each sub-element and update its stroke-width and color
                                    subElements.forEach(subElement => {
                                        let svgIconHighlight = subElement.cloneNode(true);
                                        svgIconHighlight.id = "icon_highlight"; //replaced id with name
                                        svgIconHighlight.style.strokeWidth = "3";
                                        svgIconHighlight.style.stroke =  hoverColor;
                                        svgIcons.prepend(svgIconHighlight);
                                    });
                                }
                            }

                            let iconsLayer = document.getElementById("previewSvg").querySelector('g[id="icons" i]');
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

        let subElementsCheck = svgIcons.querySelectorAll("*");
        subElementsCheck.forEach(subElementCheck => {
            if(subElementCheck.id == "icon_highlight"){
                subElementCheck.remove();
            }
        });

        const protocol = window.location.protocol;
        const host = window.location.host;
        const modalInstance = document.getElementsByName("modal_location")[0].value;
        const restHoverColor = protocol + "//" + host  + "/wp-json/wp/v2/instance/" + modalInstance + "?per_page=100";


        fetch(restHoverColor)
            .then(response => response.json())
            .then(data => {
                const rawHoverColorString = data['instance_hover_color'];
              //  let hoverColor = "yellow"; 
                if (rawHoverColorString) {
                    hoverColor = rawHoverColorString;
                    const commaIndex = hoverColor.indexOf(',');
                    if (commaIndex !== -1) {
                        hoverColor = hoverColor.substring(0, commaIndex);
                    }
                }

                if (iconValue != ""){
                    let svgIconTarget = svgIcons.querySelector('g[id="' + iconValue + '"]');
        
                    // Select all child elements 
                    let subElements = svgIconTarget.querySelectorAll("*");
        
                    // Loop through each sub-element and update its stroke-width and color
                    subElements.forEach(subElement => {
                        let svgIconHighlight = subElement.cloneNode(true);
                        svgIconHighlight.id = "icon_highlight";
                        svgIconHighlight.style.strokeWidth = "6";
                        svgIconHighlight.style.stroke = hoverColor;
                        svgIcons.prepend(svgIconHighlight);
                    });
                }
            })


    }
}

document.querySelector('select[name="modal_location"]').addEventListener("change", modal_location_change);
document.querySelector('select[name="modal_scene"]').addEventListener("change", modal_scene_change);
document.querySelector('select[name="modal_icons"]').addEventListener("change", modal_icons_change);
document.querySelector('select[name="icon_function"]').addEventListener("change", iconFunction);

document.querySelector(".range[data-depend-id='modal_tab_number']").addEventListener("change", function(){ 
    let opening_tab_entries = document.getElementsByName("modal_tab_number")[0].value;
    displayTabEntries(opening_tab_entries);
});

// Add on change event handlers to the two "modal tab number" entry fields
let modalTabRangeElement = document.querySelector(".range[data-depend-id='modal_tab_number']");
modalTabRangeElement.addEventListener("change", function() {
    let opening_tab_entries = document.getElementsByName("modal_tab_number")[0].value;
    displayTabEntries(opening_tab_entries);
});

let modalTabRangeElement2 = modalTabRangeElement.nextElementSibling;
modalTabRangeElement2.addEventListener("change", function() {
    let opening_tab_entries2 = document.getElementsByName("modal_tab_number")[0].value;
    displayTabEntries(opening_tab_entries2);
});


// Add on change event handlers to the two "modal info number" entry fields
let modalInfoRangeElement = document.querySelector(".range[data-depend-id='modal_info_entries']");
modalInfoRangeElement.addEventListener("change", function() {
    let number_of_modal_info_entries = modalInfoRangeElement.value;
    displayEntries(number_of_modal_info_entries, ".text-class[data-depend-id='modal_info_");
});

let modalInfoRangeElement2 = modalInfoRangeElement.nextElementSibling;
modalInfoRangeElement2.addEventListener("change", function() {
    let number_of_modal_info_entries2 = modalInfoRangeElement2.value;
    displayEntries(number_of_modal_info_entries2, ".text-class[data-depend-id='modal_info_");
});

// Add on change event handlers to the two "modal photo number" entry fields
let modalPhotoRangeElement = document.querySelector(".range[data-depend-id='modal_photo_entries']");
modalPhotoRangeElement.addEventListener("change", function() {
    let number_of_modal_photo_entries = modalPhotoRangeElement.value;
    displayEntries(number_of_modal_photo_entries, ".text-class[data-depend-id='modal_photo_");
});

let modalPhotoRangeElement2 = modalPhotoRangeElement.nextElementSibling;
modalPhotoRangeElement2.addEventListener("change", function() {
    let number_of_modal_photo_entries2 = modalPhotoRangeElement2.value;
    displayEntries(number_of_modal_photo_entries2, ".text-class[data-depend-id='modal_photo_");
});

document.querySelector('[data-depend-id="modal_preview"]').addEventListener('click', function() {
    // Let's remove the preview window if it already exists
    var previewWindow = document.getElementById('modal_preview');
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
    newDiv.id = "modal_preview";
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

