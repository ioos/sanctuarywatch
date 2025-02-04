// import { make_plots } from './plots.js';

console.log("THIS IS A TEST");
console.log(post_id);
// screen.orientation.lock('landscape');
console.log("new wp")

console.log(child_ids);

// access echoed JSON here for use. 
//get all links from single-scene.php



function hexToRgba(hex, opacity) {
    // Remove the hash if it's present
    hex = hex.replace(/^#/, '');

    // Parse the r, g, b values from the hex string
    let bigint = parseInt(hex, 16);
    let r = (bigint >> 16) & 255;
    let g = (bigint >> 8) & 255;
    let b = bigint & 255;

    // Return the rgba color string
    return `rgba(${r}, ${g}, ${b}, ${opacity})`;
}
/**
 * Traps the focus within a specified modal element, ensuring that the user cannot tab out of it.
 *
 * This function ensures that accessibility keyboard navigation (specifically tabbing) is confined within the modal,
 * and if the user tries to tab past the last focusable element, focus will loop back to the first focusable element.
 * It also brings focus back to the modal if the user attempts to focus on an element outside of it.
 *
 * @param {HTMLElement} modalElement - The modal element within which focus should be trapped.
 * @returns {Function} cleanup - A function that removes the event listeners and deactivates the focus trap.
 */

function trapFocus(modalElement) {
    function getFocusableElements() {
        return Array.from(modalElement.querySelectorAll(
            'button, [href], input, select, textarea, summary, [tabindex]:not([tabindex="-1"])'
        )).filter(el => !el.hasAttribute('disabled') && el.offsetParent !== null);
    }

    function handleKeydown(e) {
        const focusableElements = getFocusableElements();
        const firstFocusableElement = focusableElements[0];
        const lastFocusableElement = focusableElements[focusableElements.length - 1];

        if (e.key === 'Tab' || e.keyCode === 9) {
            if (e.shiftKey) { // shift + tab
                if (document.activeElement === firstFocusableElement) {
                    lastFocusableElement.focus();
                    e.preventDefault();
                }
            } else { // tab
                if (document.activeElement === lastFocusableElement) {
                    firstFocusableElement.focus();
                    e.preventDefault();
                }
            }
        } 
    }

    function handleFocus(e) {
        if (!modalElement.contains(e.target)) {
            const focusableElements = getFocusableElements();
            if (focusableElements.length > 0) {
                focusableElements[0].focus();
            }
        }
    }

    document.addEventListener('keydown', handleKeydown);
    document.addEventListener('focus', handleFocus, true);

    const initialFocusableElement = getFocusableElements()[0];
    if (initialFocusableElement) initialFocusableElement.focus();

    return function cleanup() {
        document.removeEventListener('keydown', handleKeydown);
        document.removeEventListener('focus', handleFocus, true);
    };
}




let child_obj = JSON.parse(JSON.stringify(child_ids));
console.log(child_obj);
// console.log("this is the post id: ", post_id);//prob dont need this
let url1 =(JSON.stringify(svg_url));
url = url1.substring(2, url1.length - 2);
console.log(url1);
// let hover_color;
let testData;
let thisInstance;
let thisScene;
let sceneLoc;
// let colors;
let sectionObj = {};
let sectColors = {};

if (!is_mobile()) {
    // Create a new style element
    const style = document.createElement('style');
    // style.type = 'text/css';
    style.innerHTML = `
        @media (min-width: 512px) and (max-width: 768px) {
            #toc-container{
                margin-left: 0px !important;
            }
            #scene-row > div.col-md-9{
                margin-left: 0px !important;
            }
            #title-container{
                margin-left: 0px !important;
            }
            #title-container > div > div.col-md-2 > div{
                max-width: 96% !important;
            }
            #top-button{
                margin-bottom: 5px;
                font-size: large;
                z-index: 1;
                margin-top: 2%;
            }
            #toggleButton{
                margin-bottom: 0px;
                font-size: large;
                z-index: 1;
            }
            #toc-group{
                padding-top: 2%;
            }
        }
    `;
    // Append the style to the head of the document
    document.head.appendChild(style);
}

/**
 
 * This function pre-processes the `child_obj` dictionary to ensure that each element (scene icon) belongs to the 
 * current scene by checking if its scene ID matches the post ID.
 * This ensures that elements from other scenes are excluded, and keys are updated as needed to avoid duplicates.
 *
 * @returns {void} Modifies child_obj dictionary in place
 */
function process_child_obj(){
    for (let key in child_obj){
        if (child_obj[key]["scene"]["ID"] !== post_id){
            delete child_obj[key];
        }
        else{
           
            let oldkey = String(key);
            console.log(typeof(oldkey));
            console.log(oldkey);
            // child_obj[newkey] = child_obj[key];
                // child_obj[newkey]["original_name"] = null;
                // delete child_obj[key];
            // }
            let lastChar = oldkey.charAt(oldkey.length - 1);

            let isNumeric = /\d/.test(lastChar);
            console.log(lastChar);
            console.log(isNumeric);
            if (isNumeric){
                let newkey = child_obj[key]["original_name"];
                console.log(newkey);
                child_obj[newkey] = child_obj[key];
                delete child_obj[key];
            }
        }
    }
    //now sort by icon order
    // If you need it back as an object:
}


process_child_obj();
const sorted_child_objs = Object.values(child_obj).sort((a, b) => a.modal_icon_order - b.modal_icon_order);
console.log("MODIFIED");
console.log(child_obj);
child_ids_helper = {};
for (let child in child_obj) {
    const childData = child_obj[child];
    // console.log(childData); 
    child_ids_helper[childData.title] = child;
}
console.log(child_ids_helper);
// document.getElementById("svg1").innerHTML =`<img src="${url}" alt="">`;

/**
 * Creates HTML elements that represent collapsible sections with links to additional scene information.
 * This function generates a list of scene information items (like text and URLs) and wraps them in an accordion component.
 * 
 * @param {string} info - The base name of the field in `scene_data` representing scene information. 
 *                        This value will be concatenated with a number (1 to 6) to create the full field name.
 * @param {string} iText - The base name of the field in `scene_data` representing the text information for the scene. 
 *                         This will be concatenated with a number (1 to 6) to fetch the corresponding text.
 * @param {string} iUrl - The base name of the field in `scene_data` representing the URL information for the scene. 
 *                        This will be concatenated with a number (1 to 6) to fetch the corresponding URL.
 * @param {object} scene_data - The dataset containing information about the scene, which includes fields for text and URL.
 * @param {string} type - The type identifier, used to generate unique HTML element IDs.
 * @param {string} name - The display name for the accordion section header.
 * 
 * @returns {HTMLElement} - Returns an accordion item element (generated via `createAccordionItem`) containing the list of scene links.
 *
 * This function is typically used in `make_title` to generate the "More Info" and "Images" sections for each scene. It iterates through 
 * a predefined set of numbered fields (from 1 to 6) in the `scene_data`, checking for non-empty text and URLs. If valid data is found, 
 * it creates a collapsible accordion section with the relevant links and displays them.
 */

function make_scene_elements(info, iText, iUrl, scene_data, type, name){
    let collapseListHTML = '<div>';
    for (let i = 1; i < 7; i++){
                // let info_field = "scene_info" + i;
                let info_field = info + i;

                // let info_text = "scene_info_text" + i;
                let info_text = iText + i;

                // let info_url = "scene_info_url" + i;
                let info_url = iUrl + i;

                let scene_info_url;
                if (iUrl == "scene_photo_url"){
                    let photoLoc = "scene_photo_location" + i;
                    if (scene_data[info_field][photoLoc] == "External"){
                        scene_info_url = scene_data[info_field][info_url];
                    } else {
                        let internal = "scene_photo_internal" + i;
                        scene_info_url = scene_data[info_field][internal];
                    }
                }

                let scene_info_text = scene_data[info_field][info_text];
                
                // console.log(scene_info_text)
                // console.log(scene_info_url)
                if ((scene_info_text == '') && (scene_info_url == '')){
                    continue;
                }
                // console.log(scene_info_text);
                // console.log(scene_info_url);
                let listItem = document.createElement('li');
                let anchor = document.createElement('a');
                anchor.setAttribute('href', 'test'); 
                anchor.textContent = 'test';

                listItem.appendChild(anchor);

                // collapseList.appendChild(listItem);
                collapseListHTML += `<div> <a href="${scene_info_url}">${scene_info_text}</a> </div>`;
                collapseListHTML += '</div>';
    }
    // let acc = createAccordionItem("test-item-1", "test-header-1", "test-collapse-1", "More Info", collapseListHTML);
    let acc = createAccordionItem(`${type}-item-1`, `${type}-header-1`, `${type}-collapse-1`, name, collapseListHTML);

    
    return acc;
}

/**
 * Creates and renders the scene title, tagline, more information/photo dropdowns after scene API call. Called asynchronously within init function
 * @returns {String} `String` - Numerical location of the scene (which instance its found in) but still a string, returned so scene location can be used within init
 * @throws {Error} - Throws an error if the network response is not OK or if the SVG cannot be fetched or parsed.
 *  @throws {Error} - Throws an error if scene data not found or error fetching data
 */

async function make_title() {
    const protocol = window.location.protocol;
    const host = window.location.host;
    // const fetchURL = `${protocol}//${host}/wp-json/wp/v2/scene?&order=asc`;

    try {
        scene_data = title_arr;

        let scene_location = scene_data["scene_location"];
        let title = scene_data['post_title'];

        let titleDom = document.getElementById("title-container");
        let titleh1 = document.createElement("h1");
        titleh1.innerHTML = title;
        titleDom.appendChild(titleh1);

        let accgroup = document.createElement("div");
        if (!is_mobile()) {
            accgroup.setAttribute("style", "margin-top: 2%");
        } else {
            accgroup.setAttribute("style", "max-width: 85%; margin-top: 2%");
        }
        accgroup.classList.add("accordion");

        if (scene_data["scene_info_entries"]!=0){
            let acc = make_scene_elements("scene_info", "scene_info_text", "scene_info_url", scene_data, "more-info", "More Info");
            accgroup.appendChild(acc);
        }
        if (scene_data["scene_photo_entries"] != 0){
            let acc1 = make_scene_elements("scene_photo", "scene_photo_text", "scene_photo_url", scene_data, "images", "Images");
            accgroup.appendChild(acc1); 
        }
   
        let row = document.createElement("div");
        row.classList.add("row");

       

        let col1 = document.createElement("div");
        // col1.classList.add("col-md-2");
        col1.appendChild(accgroup);

        let col2 = document.createElement("div");
        // col2.classList.add("col-md-10");

        if (!is_mobile()) {
            col1.classList.add("col-md-2");
            col2.classList.add("col-md-10");
            // col2.style.marginLeft =  `-12%`;
            // col1.style.marginLeft = '-12%';
            // document.querySelector("#title-container").style.marginLeft = '0%';
            function adjustTitleContainerMargin() {
                if (window.innerWidth < 512) {
                    document.querySelector("#title-container").style.marginLeft = '0%';
                } else {
                    document.querySelector("#title-container").style.marginLeft = '9%'; // Reset or apply other styles if needed
                }
            }
            adjustTitleContainerMargin();
            window.addEventListener('resize', adjustTitleContainerMargin);

        } else {
            col1.classList.add("col-md-2");
            col2.classList.add("col-md-10");
        }

        if (is_mobile()){
            col2.setAttribute("style", "padding-top: 5%; align-content: center; margin-left: 7%;");
        }

        let titleTagline = document.createElement("p");
        titleTagline.innerHTML = scene_data.scene_tagline;
        titleTagline.style.fontStyle = 'italic';
        if (is_mobile()){
            let item = createAccordionItem("taglineAccId", "taglineHeaderId", "taglineCollapseId", "Tagline", scene_data.scene_tagline);
            accgroup.prepend(item);

        } else {
            col2.appendChild(titleTagline);
        }
        // row.setAttribute("style", "display: flex; justify-content: center; margin-right: -15px; margin-left: -15px; margin-top: 1%");
        row.appendChild(col2);
        row.appendChild(col1);
        // row.appendChild(col2);
        row.setAttribute("style", "margin-top: 1%");

        titleDom.append(row);
        // return scene_location;
        return scene_data;

    } catch (error) {
        console.error('If this fires you really screwed something up', error);
    }
}

let mobileBool = false;


/**
 * Checks whether or not an icon has an associated mobile layer. Looks at mob_icons elementm
 * @returns {Boolean} `Boolean` - Numerical location of the scene (which instance its found in) but still a string, returned so scene location can be used within init
 * @throws {Error} - Throws an error if the network response is not OK or if the SVG cannot be fetched or parsed.
 * * @throws {Error} - Throws an error if scene data not found or error fetching data
 */
function has_mobile_layer(mob_icons, elemname){
    // console.log("mobile icons here:");
    // console.log(mob_icons);
    if (mob_icons == null){
        console.log("uh oh");
        return false;
    }
    for (let i = 0; i < mob_icons.children.length; i++) {
        let child = mob_icons.children[i];
        // console.log("mob icons helper here");
        // console.log(child); 
        let label = child.getAttribute('inkscape:label');
        if (label === elemname){
            console.log(`found ${label}`);
            return true;
        }             
    }
    // console.log("uh oh");
    return false;
}

//returns DOM elements for mobile layer
/**
 * Retrieves the DOM element corresponding to a specific layer in a mobile SVG structure based on its label.
 * 
 * @param {HTMLElement} mob_icons - The parent DOM element that contains all child elements (icons) to search through.
 * @param {string} elemname - The name of the layer or icon to search for. It matches the 'inkscape:label' attribute of the child element.
 * 
 * @returns {HTMLElement|null} - Returns the DOM element that matches the given `elemname` in the 'inkscape:label' attribute.
 *                                If no match is found, it returns `null`.
 */
function get_mobile_layer(mob_icons, elemname){
    for (let i = 0; i < mob_icons.children.length; i++) {
        let child = mob_icons.children[i];
        // console.log("mob icons helper here");
        // console.log(child); 
        let label = child.getAttribute('inkscape:label');
        if (label === elemname){
            // console.log("in get mobile laters");
            // console.log(child);
            return child;
        }             
    }
    return null;
}

/**
 * Removes the outer container with the ID 'entire_thing' and promotes its child elements to the body. 
 * This is because we want to get rid of entire_thing if we are on pc/tablet view, and keep it otherwise (ie mobile)
 * 
 * This function locates the container element with the ID 'entire_thing', moves all its child elements 
 * directly to the `document.body`, and then removes the container itself from the DOM.
 * 
 * @returns {void}
 */
function remove_outer_div(){
    let container =  document.querySelector("#entire_thing");
    while (container.firstChild) {
        document.body.insertBefore(container.firstChild, container);
    }
    container.remove();

}

//helper function for creating mobile grid for loadSVG:
/**
 * Creates a mobile grid layout for displaying icons in an SVG element.
 * 
 * This function removes the outer container (using `remove_outer_div`), clones icons from an SVG element, 
 * and organizes them into a responsive grid based on the screen's width and height. It adjusts the layout
 * when the window is resized, dynamically setting the number of columns and rows.
 * 
 * @param {SVGElement} svgElement - The main SVG element that contains the icons to be displayed.
 * @param {Array} iconsArr - An array of icon objects containing the icon IDs and their metadata.
 * @param {HTMLElement} mobile_icons - A DOM element containing specific mobile versions of icons, if available.
 * 
 * @returns {void}
 */
function mobile_helper(svgElement, iconsArr, mobile_icons){
    console.log("iconsArr below for mobile");
    console.log(iconsArr);
    remove_outer_div();
    let defs = svgElement.firstElementChild;
    let ignore = 0;

   
    function updateLayout(numCols, numRows) {
        let outer_cont = document.querySelector("body > div.container-fluid");
        outer_cont.innerHTML = '';
    
        let idx = 0;
        for (let i = 0; i < numRows; i++) {
            let row_cont = document.createElement("div");
            row_cont.classList.add("row");
            row_cont.setAttribute("id", `row-${i}`);
            
            for (let j = 0; j < numCols; j++) {
                if (idx < iconsArr.length) {
                    let cont = document.createElement("div");
                    cont.classList.add("col-4");
                    cont.style.paddingBottom = '10px';
                    cont.style.paddingTop = '5px';
                    cont.style.fontWeight = 'bold'; 
                    cont.style.border = '2px solid #000';
                    // cont.style.color = '#008da8';
                    // cont.style.background = 'white';
                    cont.style.background = 'radial-gradient(white, #f0f0f0)'; 
                   
                    let svgClone = document.createElementNS("http://www.w3.org/2000/svg", "svg");
                    svgClone.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
                    svgClone.setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
                    cont.appendChild(svgClone);
                    let currIcon = iconsArr[idx].id;
                    if (currIcon && currIcon in child_obj) {
                        console.log('we good');
                    } else {
                        console.log("GTFO");
                        idx+=1
                        ignore+=1
                        console.lo
                        continue;
                    }
                    console.log(child_obj[currIcon]);
                    let key  ='';
                    if (!has_mobile_layer(mobile_icons, currIcon)){
                        key = svgElement.querySelector(`#${currIcon}`).cloneNode(true);
                    } else {
                        key = get_mobile_layer(mobile_icons, currIcon);
                        let temp = svgElement.querySelector(`#${currIcon}`).cloneNode(true);
                        let tempId = temp.getAttribute("id");
                        key.setAttribute("id",  tempId);
                    }
                    cont.setAttribute("id", `${currIcon}-container`);
                    svgClone.append(defs);
                    svgClone.append(key);
                    
                    let caption = document.createElement("div");
                    if (child_obj[currIcon]){
                        caption.innerText = child_obj[currIcon].title;
                    } else {
                        // idx+=1
                        // continue;
                        // caption.innerText = "not in wp yet, have to add";
                    }
                    
                    caption.setAttribute("style", "font-size: 15px")
                    cont.appendChild(caption);
                    row_cont.appendChild(cont);
                    setTimeout(() => {
                        let bbox = key.getBBox(); 
                        svgClone.setAttribute('viewBox', `${bbox.x} ${bbox.y} ${bbox.width} ${bbox.height}`);
                    }, 0);
    
                    idx += 1;
                } else {
                    continue;
                }
            }
            // outer_cont.style.marginTop = '70%';
            outer_cont.style.marginLeft = '-1.5%';
            outer_cont.appendChild(row_cont);
        }
    }

    function updateNumCols() {
        let numCols;
        let numRows;
        // let mobViewImage = document.querySelector("#mobile-view-image");
        // console.log(mobViewImage.style);
        let ogMobViewImage = 'transform: scale(0.3); margin-right: 65%; margin-top: -70%; margin-bottom: -70%'
        let sceneFluid = document.querySelector("#scene-fluid");
        let ogSceneFluid = 'margin-top: 70%; margin-left: -1.5%;'
        let colmd2 = document.querySelector("#title-container > div > div.col-md-2");
        let ogColmd2 = colmd2.getAttribute("style", "");

        if (window.innerWidth > window.innerHeight) {
            let mobViewImage = document.querySelector("#mobile-view-image");
            let sceneFluid = document.querySelector("#scene-fluid");
            let colmd2 = document.querySelector("#title-container > div > div.col-md-2");
            let mobModalDialog = document.querySelector("#mobileModal > div");
            let modalDialogInfo = document.querySelector("#myModal > div");

            console.log("aaahahaha");
            numCols = 4;
            console.log("landscapeee");  
            mobViewImage.setAttribute("style", "transform: scale(0.5); margin-right: 35%; margin-top: -23%")
            sceneFluid.setAttribute("style", "margin-top: 25%;margin-left: -1.5%; display: block");
            colmd2.setAttribute("style", "width: 100%")
            mobModalDialog.setAttribute("style", "z-index: 9999;margin-top: 10%;max-width: 88%;");
            modalDialogInfo.setAttribute("style", "z-index: 9999;margin-top: 10%;max-width: 88%;");
        //   updateLayout();

        } else  {
          numCols = 3;
            let mobViewImage = document.querySelector("#mobile-view-image");
            let sceneFluid = document.querySelector("#scene-fluid");
            let colmd2 = document.querySelector("#title-container > div > div.col-md-2");
            let mobModalDialog = document.querySelector("#mobileModal > div");
            let modalDialogInfo = document.querySelector("#myModal > div");

            console.log("Portrait mode");
            mobViewImage.setAttribute("style", '');
            mobViewImage.setAttribute("style", ogMobViewImage);
            sceneFluid.setAttribute("style", '');
            sceneFluid.setAttribute("style", ogSceneFluid);
            colmd2.setAttribute("style", '');
            colmd2.setAttribute("style", ogColmd2);
            mobModalDialog.setAttribute("style", "z-index: 9999;margin-top: 60%;max-width: 88%;");
            modalDialogInfo.setAttribute("style", "z-index: 9999;margin-top: 60%;max-width: 88%;");

        //   updateLayout();

        }
        // updateLayout();
        console.log("ignored: ");
        console.log(ignore);
        numRows = Math.ceil((iconsArr.length/numCols));
        console.log(`Number of columns: ${numCols}`);
        console.log("number of rows: ");
        console.log(numRows);

        updateLayout(numCols, numRows);
        // mobViewImage.remove();
        add_modal();

      }
    updateNumCols();
    window.addEventListener("resize", updateNumCols);

   
}

// Below is the function that will be used to include SVGs within each scene

/**
 * Accesses the SVG image for the scene, checks type of device, renders appropriate scene layout by calling other helper functions. 
 * all of the top-level helper functions that render different elements of the DOM are called within here. 
 * based on link_svg from infographiq.js
 *
 * @param {string} url - The URL of the SVG to be fetched, provided from the PHP backend.
 * @param {string} containerId - The ID of the DOM element to which the SVG will be appended.
 * @returns {void} `void` - Modifies the DOM but does not return any value.
 * @throws {Error} - Throws an error if the network response is not OK or if the SVG cannot be fetched or parsed.
 */

async function loadSVG(url, containerId) {
    try {
        // Step 1: Fetch the SVG content
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const svgText = await response.text();
        // console.log(svgText);

        // Step 2: Parse the SVG content
        const parser = new DOMParser();
        const svgDoc = parser.parseFromString(svgText, "image/svg+xml");
        const svgElement = svgDoc.documentElement;
        // console.log(svgElement);
        // console.log(svgElement);
        svgElement.setAttribute("id", "svg-elem");

        //Append the SVG to the DOM
        const container = document.getElementById(containerId);
        console.log(container);
        // container.appendChild(svgElement);
        // console.log(svgElement);
        // checking if user device is touchscreen
        if (is_touchscreen()){
            console.log("this is touchscreen");
            // flicker_highlight_icons();
            // console.log("touchscreen recognized");
            if (is_mobile() && (deviceDetector.device != 'tablet')){ //a phone and not a tablet; screen will be its own UI here
                // console.log("mobile recognized within conditional");

                //smaller image preview here for mobile
                let fullImgCont = document.querySelector("#mobile-view-image");

                
                // fullImgCont.setAttribute("style", "");

                console.log(fullImgCont);
                
                let titleRowCont = document.querySelector("#title-container > div");
                let sceneButton = document.createElement("button");
                sceneButton.innerHTML = "<strong>View Full Scene</strong>";
                // sceneButton.setAttribute("style", "margin-left: -13%; max-width: 80%; border-radius: 10px");
                sceneButton.setAttribute("style", "margin-left: -13%; max-width: 80%; border-radius: 10px; background-color: #008da8; color: white;");

                sceneButton.setAttribute("class", "btn ");
                sceneButton.setAttribute("data-toggle", "modal");
                // sceneButton.setAttribute("data-target", "#exampleModal");

                titleRowCont.appendChild(sceneButton);
                let svgElementMobileDisplay = svgElement.cloneNode(true);
                svgElementMobileDisplay.style.height = '10%';
                svgElementMobileDisplay.style.width = '100%';

              
                let modal = document.getElementById("mobileModal");
                // let modalContent = document.querySelector("#mobileModal > div > div");
                let modalBody = document.querySelector("#mobileModal > div > div > div.modal-body")
                modalBody.appendChild(svgElementMobileDisplay);
                // let span = document.getElementsByClassName("close")[0];

                sceneButton.onclick = function() {
                    modal.style.display = "block";
                  }
                  
                  
                  // When the user clicks anywhere outside of the modal, close it
                window.onclick = function(event) {
                    if (event.target == modal) {
                      modal.style.display = "none";
                      history.pushState("", document.title, window.location.pathname + window.location.search);
                    }
                  }
                // let closeButton = document.querySelector("#mobileModal > div > div > div.modal-footer > button");
                let closeButton = document.querySelector("#close1");
                closeButton.onclick = function() {
                    // if (event.target == modal) {
                      modal.style.display = "none";
                      history.pushState("", document.title, window.location.pathname + window.location.search);
                    // }
                  }
        
        
                mobileBool = true;
                const iconsElement = svgElement.getElementById("icons");
                //fix here
                let mobileIcons = null;
                if (svgElement.getElementById("mobile")){
                    mobileIcons = svgElement.getElementById("mobile").cloneNode(true);
                } 
                // const mobileIcons = svgElement.getElementById("mobile").cloneNode(true);
                // console.log(iconsElement);
                // console.log("mobile icons here:");
                // console.log(mobileIcons);

                //for mobile: only leave icons, nothing else
                // const parentElement = svgElement.querySelector('g.cls-3');
                // let parentElement = svgElement.querySelector("#g");
                // console.log(svgElement.lastElementChild);
                let parentElement = svgElement.lastElementChild;
                    // console.log(Array.from(parentElement.children));
                const children = Array.from(parentElement.children);
                // console.log(children);
                children.forEach(child => {
                    if ((child !== iconsElement ) ) {
                            parentElement.removeChild(child);
                    }
                });
                // parentElement.appendChild(mobileIcons);
                // console.log(svgElement);
                let iconsArr = Array.from(iconsElement.children);
                mobile_helper(svgElement, iconsArr, mobileIcons);
                // mobile_icons_helper(mobileIcons);

                // add_modal();
                // window.addEventListener('load', function() {
                //     make_title();
                // });
                // make_title();
                // highlight_icons();
                // flicker_highlight_icons();
                
                
            } else{ //if it gets here, device is a tablet
                //hide mobile icons
                
                console.log("tablet");
                // remove_outer_div();
                window.addEventListener('load', function() {
                    let mob_icons = document.querySelector("#mobile");
                    if (mob_icons) {
                        mob_icons.setAttribute("display", "none");
                    }
                });
                
                
                container.appendChild(svgElement);
                // flicker_highlight_icons();
                toggle_text();
                full_screen_button('svg1');
                // if (thisInstance.instance_toc_style == "list"){
                if (scene_toc_style === "list"){
                    list_toc();
                } else {
                    table_of_contents();
                }               
                add_modal();
                flicker_highlight_icons();
                // make_title();


            }
        }
        else{ //device is a PC
            //hide mobile icons
            window.addEventListener('load', function() {
                let mob_icons = document.querySelector("#mobile");
                if (mob_icons) {
                    mob_icons.setAttribute("display", "none");
                }
            });
            
            container.appendChild(svgElement);
            highlight_icons();
            // table_of_contents();
            // list_toc();
            toggle_text();
            full_screen_button('svg1');
            // if (thisInstance.instance_toc_style == "list"){
            if (scene_toc_style === "list"){
                list_toc();
            } else {
                table_of_contents();
            }               
            add_modal();
            // make_title();

            
        }
        // highlight_icons();
        // table_of_contents();
        // add_modal();
        // make_title();
        // full_screen_button('svg1');
        // toggle_text();
        // window.addEventListener('load', function() {
        //     make_title();
        //     // console.log(child_obj);
        // });



    } catch (error) {
        console.error('Error fetching or parsing the SVG:', error);
    }
}


//highlight items on mouseover, remove highlight when off; 
//CHANGE HERE FOR TABLET STUFF

/**
 * Adds hover effects to SVG elements based on `child_obj` keys, meant for PC layout. 
 * Highlights the icon by changing its stroke color and width on mouseover, 
 * using section-specific colors if enabled, and resets the style on mouseout.
 *
 * @returns {void} - `void` Modifies DOM element styles in place.
 */
function highlight_icons(){
    for (let key in child_obj){
        let elem = document.querySelector('g[id="' + key + '"]');
        // console.log(elem);
        elem.addEventListener('mouseover', function(){
            // console.log('mousing over: ', key); 
            // elem.style.stroke = "yellow"; //this is no longer hard-coded
            // elem.style.stroke = thisInstance.instance_hover_color; //this is no longer hard-coded; //ideally, make a dictionary mapping each  key to a section to a color
            // elem.style.stroke = sectColors[sectionObj[key]];
            // if (thisInstance.instance_colored_sections === "yes"){ //needs to be changed
                // console.log("yes!");
                // elem.style.stroke = scene_sections[sectionObj[key]];//sectColors[sectionObj[key]];
            // } else{
            //     elem.style.stroke = colors[0];
            // }
            // console.log(thisInstance);
            // elem.style.stroke = sectColors[sectionObj[key]];
            if (scene_same_hover_color_sections != "yes" && sectionObj[key]!="None"){ //this should be done on the SCENE side of things, will havet o bring this back
                // console.log(scene_sections[sectionObj[key]]);
                // elem.style.stroke = scene_sections[sectionObj[key]];
                let section_name = sectionObj[key];
                let section_num = section_name.substring(section_name.length - 1, section_name.length);
                // console.log(section_num);
                let this_color = `scene_section_hover_color${section_num}`;
                elem.style.stroke = scene_data[sectionObj[key]][this_color];
            } else{
                elem.style.stroke = scene_default_hover_color;
            }

            elem.style.strokeWidth = "3px";
        });
        elem.addEventListener('mouseout', function(){
            // console.log('mousing out: ', key); 
            elem.style.stroke = "";
            elem.style.strokeWidth = "";
        });
    }  
}
/**
 * Adds flicker effects to SVG elements based on `child_obj` keys, meant for tablet layout. 
 * Icons flicker their corresponding color on a short time interval
 * using section-specific colors if enabled
 * 
 * @returns {void} - `void` Modifies DOM element styles in place.
 */
function flicker_highlight_icons() {
    for (let key in child_obj) {
        let elem = document.querySelector('g[id="' + key + '"]');
        if (elem) {
            // Add transition for smooth fading
            // console.log("elem here is: ");
            // console.log(elem);
            elem.style.transition = 'stroke-opacity 1s ease-in-out';
            
            // Initial state
            // elem.style.stroke = "yellow";
            // elem.style.stroke = sectColors[sectionObj[key]];
            // if (thisInstance.instance_colored_sections === "yes"){ //needs to be changed
                // elem.style.stroke =  scene_sections[sectionObj[key]];//sectColors[sectionObj[key]];
            if (scene_same_hover_color_sections != "yes" && sectionObj[key]!="None"){ //this should be done on the SCENE side of things, will havet o bring this back
                    // console.log(scene_sections[sectionObj[key]]);
                let section_name = sectionObj[key];
                let section_num = section_name.substring(section_name.length - 1, section_name.length);
                // console.log(section_num);
                let this_color = `scene_section_hover_color${section_num}`;
                elem.style.stroke = scene_data[sectionObj[key]][this_color];
                } else {
                    elem.style.stroke = scene_default_hover_color;
                }
                // console.log("yes here");
            // } else{
            //     elem.style.stroke = colors[0];
            // }

            elem.style.strokeWidth = "3";
            elem.style.strokeOpacity = "0";

            // Create flickering effect
            let increasing = true;
            setInterval(() => {
                if (increasing) {
                    elem.style.strokeOpacity = "0.5";
                    increasing = false;
                } else {
                    elem.style.strokeOpacity = "0";
                    increasing = true;
                }
            }, 1500); // Change every 1 second
        }
    }
}



/**
 * Checks if the device being used is touchscreen or not. 
 * @returns {boolean} `True` if touchscreen else `False`.
 */
function is_touchscreen(){
    //check multiple things here: type of device, screen width, 
    return ( 'ontouchstart' in window ) || 
           ( navigator.maxTouchPoints > 0 ) || 
           ( navigator.msMaxTouchPoints > 0 );
    
}


/**
 * Checks if the device being used is a mobile device or not.
 * Checks operating system and screen dimensions
 * @returns {boolean} `True` if mobile else `False`.
 */
function is_mobile() {
    return (/Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) 
           && (window.innerWidth < 512 || window.innerHeight < 512);
}

/**
 * A utility object from the internet for detecting the user's device type based on the user agent string.
 * Helper function from the internet; using it to check type of device. 
 * Properties:
 * - `device` {string}: The detected device type ('tablet', 'phone', or 'desktop').
 * - `isMobile` (boolean): Indicates if the device is mobile (true for 'tablet' or 'phone', false for 'desktop').
 * - `userAgent` (string): The user agent string in lowercase.
 * 
 * Methods:
 * - `detect(s)`: Detects the device type from the user agent string `s` (or the current user agent if not provided).
 *     - @returns {string} - The detected device type ('tablet', 'phone', or 'desktop').
 */
var deviceDetector = (function ()
{
  var ua = navigator.userAgent.toLowerCase();
  var detect = (function(s)
  {
    if(s===undefined)s=ua;
    else ua = s.toLowerCase();
    if(/(ipad|tablet|(android(?!.*mobile))|(windows(?!.*phone)(.*touch))|kindle|playbook|silk|(puffin(?!.*(IP|AP|WP))))/.test(ua))
                return 'tablet';
          else
      if(/(mobi|ipod|phone|blackberry|opera mini|fennec|minimo|symbian|psp|nintendo ds|archos|skyfire|puffin|blazer|bolt|gobrowser|iris|maemo|semc|teashark|uzard)/.test(ua))            
                    return 'phone';
                else return 'desktop';
    });
    return{
        device:detect(),
        detect:detect,
        isMobile:((detect()!='desktop')?true:false),
        userAgent:ua
    };
}());
 

//creates an accordion item w/custom IDs based on input
/**
 * Creates and returns a fully structured Bootstrap accordion item with a header, button, and collapsible content.
 * Called in scenarios where accordion needs to be created - within `render_modal` (for modal info and modal images), `make_scene_elements` (for scene info and scene photo accordions), and `make_title' (for mobile tagline)
 *
 * @param {string} accordionId - The unique ID for the accordion item.
 * @param {string} headerId - The unique ID for the accordion header.
 * @param {string} collapseId - The unique ID for the collapsible section.
 * @param {string} buttonText - The text to display on the accordion button.
 * @param {string} collapseContent - The content to display within the collapsible section.
 * 
 * @returns {HTMLElement} `accordionItem` The complete accordion item containing the header, button, and collapsible content.
 */
function createAccordionItem(accordionId, headerId, collapseId, buttonText, collapseContent) {
    // Create Accordion Item
    let accordionItem = document.createElement("div");
    accordionItem.classList.add("accordion-item");
    accordionItem.setAttribute("id", accordionId);

    // Create Accordion Header
    let accordionHeader = document.createElement('h2');
    accordionHeader.classList.add("accordion-header");
    accordionHeader.setAttribute("id", headerId);

    // Create Accordion Button
    let accordionButton = document.createElement('button');
    // accordionButton.classList.add('accordion-button');
    accordionButton.classList.add('accordion-button', 'collapsed'); // Add 'collapsed' class
    accordionButton.setAttribute("type", "button");
    accordionButton.setAttribute("data-bs-toggle", "collapse");
    accordionButton.setAttribute("data-bs-target", `#${collapseId}`);
    accordionButton.setAttribute("aria-expanded", "false");
    accordionButton.setAttribute("aria-controls", collapseId);
    accordionButton.innerHTML = buttonText;

    // Append Button to Header
    accordionHeader.appendChild(accordionButton);

    // Create Accordion Collapse
    let accordionCollapse = document.createElement('div');
    accordionCollapse.classList.add("accordion-collapse", "collapse");
    accordionCollapse.setAttribute("id", collapseId);
    accordionCollapse.setAttribute("aria-labelledby", headerId);

    // Create Accordion Collapse Body
    let accordionCollapseBody = document.createElement('div');
    accordionCollapseBody.classList.add("accordion-body");
    accordionCollapseBody.innerHTML = collapseContent;

    // Append Collapse Body to Collapse
    accordionCollapse.appendChild(accordionCollapseBody);

    // Append Header and Collapse to Accordion Item
    accordionItem.appendChild(accordionHeader);
    accordionItem.appendChild(accordionCollapse);

    return accordionItem;
}

/**
 * Renders tab content into the provided container element based on the information passed in the `info_obj` object. 
 * This function creates a styled layout that includes links, an image with a caption, and an expandable details section.
 * 
 * @param {HTMLElement} tabContentElement - The HTML element where the content for the tab will be inserted.
 * @param {HTMLElement} tabContentContainer - The container element that holds the tab content and allows appending the tab content element.
 * @param {Object} info_obj - An object containing information used to populate the tab content.
 *     @property {string} scienceLink - URL for the "More Science" link.
 *     @property {string} scienceText - Text displayed for the "More Science" link. This text is prepended with a clipboard icon.
 *     @property {string} dataLink - URL for the "More Data" link.
 *     @property {string} dataText - Text displayed for the "More Data" link. This text is prepended with a database icon.
 *     @property {string} imageLink - URL of the image to be displayed in the figure section.
 *     @property {string} shortCaption - Short description that serves as the image caption.
 *     @property {string} longCaption - Detailed text that is revealed when the user clicks on the expandable 'Click for Details' section.
 * @returns {void} Modifies dom
 * Function Workflow:
 * 1. A container `div` element is created with custom styling, including background color, padding, and border-radius.
 * 2. Inside this container, a `table-row`-like structure is created using `div` elements that display two links:
 *      a. A "More Science" link on the left, prepended with a clipboard icon.
 *      b. A "More Data" link on the right, prepended with a database icon.
 * 3. The function appends the container to `tabContentElement` only if both the science link text and data link exist.
 * 4. An image with a caption is added to `tabContentElement`, using the URL and caption provided in `info_obj`.
 * 5. A `details` element is created, which reveals more information (the long caption) when the user clicks the 'Click for Details' summary.
 * 6. The function appends the entire tab content (container with links, figure with image, caption, and details) to `tabContentContainer`.
 *
 * Styling and Layout:
 * - The function uses a `table-row` and `table-cell` approach for laying out the links side by side.
 * - Links are decorated with icons, styled to remove the underline, and open in a new tab.
 * - The image is styled to be responsive (100% width) and centered within the figure.
 * - The `details` element is collapsible, providing a clean way to show the long caption when needed.
 *
 * Usage:
 * This function is called for each tab, populating one or more figures (and other corresponding info)
 */
function render_tab_info(tabContentElement, tabContentContainer, info_obj){
    const containerDiv = document.createElement('div');
    containerDiv.style.background = '#e3e3e354';
    containerDiv.style.width = '100%';
    containerDiv.style.display = 'table';
    containerDiv.style.fontSize = '120%';
    containerDiv.style.padding = '10px';
    containerDiv.style.marginBottom = '10px';
    containerDiv.style.margin = '0 auto'; 
    containerDiv.style.borderRadius = '6px 6px 6px 6px'; 
    containerDiv.style.borderWidth = '1px'; 
    containerDiv.style.borderColor = 'lightgrey'; 
    



    // Create the table row div
    const tableRowDiv = document.createElement('div');
    tableRowDiv.style.display = 'table-row';

    // Create the left cell div
    const leftCellDiv = document.createElement('div');
    leftCellDiv.style.textAlign = 'left';
    leftCellDiv.style.display = 'table-cell';

    // More Science Link Here
    const firstLink = document.createElement('a');
    firstLink.href = info_obj['scienceLink'];
    firstLink.target = '_blank';
    if (info_obj['scienceText']!=''){
        firstLink.appendChild(document.createTextNode(info_obj['scienceText']));
        let icon1 = `<i class="fa fa-clipboard-list" role="presentation" aria-label="clipboard-list icon" style=""></i> `;
        firstLink.innerHTML = icon1 + firstLink.innerHTML;
        firstLink.style.textDecoration = 'none';
        firstLink.style.color = '#03386c';

        leftCellDiv.appendChild(firstLink);
    }
    // firstLink.appendChild(document.createTextNode(info_obj['scienceText']));
    // let icon1 = `<i class="fa fa-clipboard-list" role="presentation" aria-label="clipboard-list icon" style=""></i> `;
    // firstLink.innerHTML = icon1 + firstLink.innerHTML;
    // firstLink.style.textDecoration = 'none';
    // leftCellDiv.appendChild(firstLink);

    // Create the right cell div
    const rightCellDiv = document.createElement('div');
    rightCellDiv.style.textAlign = 'right';
    rightCellDiv.style.display = 'table-cell';

    // Create the second link
    if (info_obj['dataLink']!=''){
        const secondLink = document.createElement('a');
        secondLink.href = info_obj['dataLink'];
        secondLink.target = '_blank';
        secondLink.style.color = '#03386c';
        let icon2 = `<i class="fa fa-database" role="presentation" aria-label="database icon"></i>`;
        secondLink.appendChild(document.createTextNode(info_obj['dataText']));
        // secondLink.innerHTML = secondLink.innerHTML + `  ` + icon2;
        secondLink.innerHTML = icon2 + `  ` + secondLink.innerHTML;
        secondLink.style.textDecoration = 'none';
        rightCellDiv.appendChild(secondLink);
    }

    


    tableRowDiv.appendChild(leftCellDiv);
    tableRowDiv.appendChild(rightCellDiv);
    containerDiv.appendChild(tableRowDiv);
    if (info_obj['dataLink']!='' && info_obj['scienceText']!=''){
        tabContentElement.appendChild(containerDiv);
    }
    // tabContentElement.appendChild(containerDiv);

    const figureDiv = document.createElement('div');
    figureDiv.classList.add('figure');

    let img;
    let interactiveBool = false;
    if (info_obj["interactive"] != "Interactive" ){
        img = document.createElement('img');
        img.src = info_obj['imageLink'];
        if (info_obj['externalAlt']){
            img.alt = info_obj['externalAlt'];
        } else {
            img.alt = '';
        }
        

    }  else {  
        img = document.createElement('div'); // Create a div to hold the plot
        img.id = 'plotly-plot'; 
        interactiveBool = true;
    }
   
    figureDiv.appendChild(img);
    figureDiv.setAttribute("display","flex");
    // figureDiv.style.display = "flex";
    figureDiv.style.justifyContent = "center"; // Center horizontally
    figureDiv.style.alignItems = "center";
    // img.setAttribute("style", "max-width: 100%;margin-top: 3%; justify-content: center");
    figureDiv.setAttribute("style", "width: 100% !important; height: auto; display: block; margin: 0; margin-top: 2%");
    // img.setAttribute("style", "width: 100% !important; height: auto; display: block; margin: 0; margin-top: 2%");

    // img.setAttribute("style", "margin-top: 2px;");

    

    // img.setAttribute("style", "justify-content: center;");

    const caption = document.createElement('p');
    caption.classList.add('caption');
    caption.innerHTML = info_obj['shortCaption'];
    caption.style.marginTop = '10px';

    figureDiv.appendChild(caption);
    tabContentElement.appendChild(figureDiv);

    // Create the details element
    const details = document.createElement('details');
    const summary = document.createElement('summary');
    summary.textContent = 'Click for Details';
    // details.appendChild(summary);
    // details.appendChild(document.createTextNode(info_obj['longCaption']));
    let longCaption = document.createElement("p");
    longCaption.innerHTML = info_obj['longCaption'];
    if (info_obj['longCaption'] != ''){
        details.appendChild(summary);
        details.appendChild(longCaption);
        tabContentElement.appendChild(details);

    }
    

    // Add the details element to the tab content element
    // tabContentElement.appendChild(details);
    tabContentContainer.appendChild(tabContentElement);

    console.log("tab content container");
    console.log(tabContentContainer);
    if (interactiveBool){
        let fetchLink = 'http://sanctuarywatch.local/wp-content/uploads/2024/09/test.json';
        // let plotType = 'markers';
        // make_plots(img, fetchLink, plotType);
        x = ['Year']; //have to make sure this is in the data
        y = ['Whales', 'Fish']; //have to make sure this is in the data
        cols = ['blue', 'red'];
        let plotInstance = new Plot(img, fetchLink, x, y, cols);
        plotInstance.execute('lines');

    }
    img.setAttribute("style", "width: 100% !important; height: auto; display: block; margin: 0; margin-top: 2%");

    
}

/**
 * Fetches tab information from a WordPress REST API endpoint and renders it into the specified tab content element and container.
 * This function retrieves figure data associated with a specific tab label and ID, and then processes and displays the data using the `render_tab_info` function.
 * 
 * @param {HTMLElement} tabContentElement - The HTML element where the individual tab content will be rendered.
 * @param {HTMLElement} tabContentContainer - The container element that holds all tab contents.
 * @param {string} tab_label - The label of the tab used to filter data. This parameter is currently unused
 * @param {string} tab_id - The ID of the tab, used to filter the figure data from the fetched results. Is a number but type is string, type casted when used
 *
 * Function Workflow:
 * 1. Constructs the API URL to fetch figure data using the current page's protocol and host.
 * 2. Makes a fetch request to the constructed URL to retrieve figure data in JSON format.
 * 3. Filters the retrieved data based on the provided `tab_id`, looking for figures that match this ID.
 * 4. If no figures match the `tab_id`, the function exits early without rendering any content.
 * 5. If matching figures are found:
 *      a. Iterates through the filtered figure data.
 *      b. Constructs an `info_obj` for each figure, containing URLs, text, image links, and captions.
 *      c. Calls the `render_tab_info` function to render each figure's information into the specified tab content element.
 *
 * Error Handling:
 * - If the fetch request fails, an error message is logged to the console.
 *
 * Usage:
 * Called at the end of the create_tabs function
 */
function fetch_tab_info(tabContentElement, tabContentContainer, tab_label, tab_id, modal_id){
    // let id = child_obj['infauna']['id'];
    // console.log(id);
    // console.log(tab_label);
    // tab_label = "test";
    const protocol = window.location.protocol;
    const host = window.location.host;
    const fetchURL  =  protocol + "//" + host  + "/wp-json/wp/v2/figure?&order=asc"
    // let fetchURL = 'http://sanctuary.local/wp-json/wp/v2/modal?&order=asc'; //will have to change eventually, relevant code in admin-modal
    fetch(fetchURL)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            // console.log(tab_label);
            // figure_data = data.find(figure => figure.figure_tab === tab_label); //this needs to be all the instances where === tab_label, not j the first one
            // all_figure_data = data.filter(figure => figure.figure_tab === tab_label); //this needs to be all the instances where === tab_label, not j the first one
            all_figure_data = data.filter(figure => Number(figure.figure_tab) === Number(tab_id));
            all_figure_data = all_figure_data.filter(figure => Number(figure.figure_modal) === Number(modal_id));
            
            if (!all_figure_data){
                //we don't create anything here...
                //don't have to render any of the info
                // tabContentContainer.setAttribute("display", "hidden");
                console.log("womp womp");
                return;
                
            } else{
                console.log(all_figure_data); 
                // tabContentContainer.setAttribute("display", "");

            // console.log(figure_data['figure_caption_long']);
            //title stuff:
                for (let idx in all_figure_data){
                    figure_data = all_figure_data[idx];
                    console.log(all_figure_data[idx]);
                    let img = '';
                    let external_alt = '';
                    if (figure_data['figure_path']==='External'){
                        img = figure_data['figure_external_url'];
                        external_alt = figure_data['figure_external_alt'];
                    } else {
                        img = figure_data['figure_image'];
                    } // add smth here for external
                    info_obj = {
                    "scienceLink": figure_data["figure_science_info"]["figure_science_link_url"],
                    "scienceText": figure_data["figure_science_info"]["figure_science_link_text"],
                    "dataLink": figure_data["figure_data_info"]["figure_data_link_url"],
                    "dataText": figure_data["figure_data_info"]["figure_data_link_text"],
                    "imageLink" : img,
                    "externalAlt": external_alt,
                    "shortCaption" : figure_data["figure_caption_short"],
                    "longCaption": figure_data["figure_caption_long"],
                    "interactive": figure_data["figure_path"]
                    };
                    // console.log(info_obj);
                    render_tab_info(tabContentElement, tabContentContainer, info_obj); //to info_obj, add fields regarding interactive figure
                }
            }

        })
    .catch(error => console.error('Error fetching data:', error));
        //new stuff here
   
}

//create tabs
/**
 * Creates and adds a new tab within modal window. Each tab is associated with specific content that is displayed when the tab is active.
 * The function also sets up event listeners for copying the tab link to the clipboard (modified permalink structure)
 *
 * @param {number} iter - The index of the tab being created. This determines the order of the tabs. From render_modal, when iterating through all tabs
 * @param {string} tab_id - The unique identifier for the tab, generated from the `tab_label`. It is sanitized to replace spaces and special characters.
 * @param {string} tab_label - The label displayed on the tab, which the user clicks to activate the tab content.
 * @param {string} [title=""] - An optional title used to construct the IDs and classes associated with the tab. It is sanitized similarly to `tab_id`.
 *
 * Function Workflow:
 * 1. Sanitizes `tab_id` and `title` by replacing spaces and special characters with underscores to create valid HTML IDs.
 * 2. Constructs the target ID for the tab content and controls using the sanitized `title` and `tab_id`.
 * 3. Creates a new navigation item for the tab, including setting the necessary attributes for Bootstrap styling and functionality.
 * 4. Appends the new tab button to modal window
 * 5. Creates a corresponding tab content pane and sets its attributes for proper display and accessibility.
 * 6. Adds a "Copy Tab Link" button and link to the tab content that allows users to copy the tab's URL to the clipboard.
 * 7. Sets event listeners for the tab button and link/button to handle copying the URL to the clipboard when clicked.
 * 8. Updates the browser's hash in the URL to reflect the currently active tab when it is clicked based on what tab/figure is currently being displayed
 * 9. Calls the `fetch_tab_info` function to fetch and display data relevant to the newly created tab.
 *
 * Error Handling:
 * - The function handles potential errors during clipboard writing by providing user feedback through alerts.
 *
 * Usage:
 * Called within render_modal -- each modal has a certain amount of tabs, iterate through each tab and create/render tab info, fix tab permalink
 *
 */
function create_tabs(iter, tab_id, tab_label, title = "", modal_id) {
    // tab_id = tab_label.replace(/\s+/g, '_').replace(/[()]/g, '_');
    // title = title.replace(/\s+/g, '_').replace(/[()]/g, '_');
    tab_id = tab_label.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_]/g, '_'); //instead of tab id, it should just be the index (figure_data)
    title = title.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_]/g, '_');
    tab_id = iter;
    console.log(tab_id);
    console.log("creating a tab");

    let tab_target = `#${title}-${tab_id}-pane`;
    let tab_controls = `${title}-${tab_id}-pane`;

    let myTab = document.getElementById('myTab');
    let navItem = document.createElement("li");
    navItem.classList.add("nav-item");
    navItem.setAttribute("role", "presentation");
    navItem.style.color = 'black';
    
    const button = document.createElement('button');
    button.classList.add('nav-link');
    if (iter === 1) {
        button.classList.add('active');
        button.setAttribute('aria-selected', 'true');
    } else {
        button.setAttribute('aria-selected', 'false');
    }
    button.id = `${title}-${tab_id}`;
    button.setAttribute('data-bs-toggle', 'tab');
    button.setAttribute('data-bs-target', tab_target);
    button.setAttribute('type', 'button');
    button.setAttribute('role', 'tab');
    button.setAttribute('aria-controls', tab_controls);
    button.style.color = 'black';
    button.textContent = tab_label;

    navItem.appendChild(button);
    myTab.appendChild(navItem);

    let tabContentContainer = document.getElementById("myTabContent");
    const tabContentElement = document.createElement('div');
    tabContentElement.classList.add('tab-pane', 'fade');
    
    if (iter === 1) {
        tabContentElement.classList.add('show', 'active');
    }
    
    tabContentElement.id = tab_controls;
    tabContentElement.setAttribute('role', 'tabpanel');
    tabContentElement.setAttribute('aria-labelledby', `${title}-${tab_id}`);
    tabContentElement.setAttribute('tabindex', '0');

    tabContentContainer.appendChild(tabContentElement);
    
    let linkbutton = document.createElement("button");
    linkbutton.classList.add("btn", "btn-primary");
    linkbutton.innerHTML = '<i class="fa-solid fa-copy"></i> Copy Tab Link';
    linkbutton.type = "button"; 
    linkbutton.setAttribute('style', 'margin-bottom: 7px');
    tabContentElement.prepend(linkbutton);


    if (iter === 1) {
        window.location.hash = `${title}/${tab_id}`; 
    
        linkbutton.addEventListener("click", (e) => {
            e.preventDefault(); // Prevent the link from opening
            writeClipboardText(`${window.location.origin}${window.location.pathname}#${title}/${tab_id}`);
        });
    }

    button.addEventListener('click', function() {
        window.location.hash = `${title}/${tab_id}`; 
        console.log(`${title}/${tab_id}`);
       
        linkbutton.addEventListener("click", (e) => {
            e.preventDefault(); // Prevent the link from opening
            writeClipboardText(`${window.location.origin}${window.location.pathname}#${title}/${tab_id}`);
        });      
        
    });
    async function writeClipboardText(text) {
        try {
            await navigator.clipboard.writeText(text);
            alert('Link copied to clipboard!');
        } catch (error) {
            console.error('Failed to copy: ', error);
            alert('Failed to copy link. Please try again.');
        }
    }
    

    fetch_tab_info(tabContentElement, tabContentContainer, tab_label, tab_id, modal_id);
}



/**
 * Renders a modal dialog for corresponding icon with data fetched from a WordPress REST API endpoint.
 * The modal displays a title, tagline, and two sections of content (more info and images) 
 * using accordions, along with dynamic tab content based on the modal's data.
 *
 * @param {string} key - The key used to access specific child data in the `child_obj` object,
 *                       which contains modal configuration and content details.
 *
 * This function performs the following steps:
 * 1. Constructs the URL to fetch modal data based on the `modal_id` associated with the provided `key`.
 * 2. Fetches modal data from the WordPress REST API.
 * 3. Updates the modal title and tagline based on the fetched data.
 * 4. Generates two accordion sections:
 *    - A "More Info" section containing a list of items linked to URLs.
 *    - An "Images" section containing a list of image links.
 * 5. Dynamically creates tabs based on the number of tabs specified in the modal data.
 * 6. Adjusts layout and classes for mobile and desktop views.
 * 7. Traps focus within the modal dialog to improve accessibility.
 *
 * Usage:
 * Called in add_modal and table_of_contents; those functions iterate through keys of child_obj(which has all the icons in a scene )
 */
function render_modal(key){
    let id = child_obj[key]['modal_id'];
    // console.log(child_obj[key]);
    const protocol = window.location.protocol;
    const host = window.location.host;
    const fetchURL  =  protocol + "//" + host  + `/wp-json/wp/v2/modal/${id}`;
    // let fetchURL = 'http://sanctuary.local/wp-json/wp/v2/modal?&order=asc'; //will have to change eventually, relevant code in admin-modal
    fetch(fetchURL)
        .then(response => response.json())
        .then(data => {
            // console.log(data);
            console.log(id);
            modal_data = data //.find(modal => modal.id === id);
            console.log("modal data here:");
            console.log(modal_data); 
            //title stuff:
            let title = child_obj[key]['title'];  //importat! pass as argument
            let modal_title = document.getElementById("modal-title");
            
            modal_title.innerHTML = title;

            //tagline container
            let tagline_container = document.getElementById('tagline-container');
            //add stuff for formatting here...
            // console.log(modal_data);
            let modal_tagline = modal_data["modal_tagline"];
            if (!is_mobile()){
                tagline_container.innerHTML =  "<em>" + modal_tagline + "<em>";
            }
            // tagline_container.innerHTML =  "<em>" + modal_tagline + "<em>";

            //generate accordion
            // Select the container where the accordion will be appended
            let accordion_container = document.getElementById('accordion-container');
            //add stuff for formatting here...
            // accordion_container.innerHTML = '';
            // Create the accordion element
            let acc = document.createElement("div");
            acc.classList.add("accordion");

            if (is_mobile()){
                console.log("is mobile");
                // tagline_container.setAttribute("class", "");
                accordion_container.setAttribute("class", "");

                // tagline_container.classList.add("col-6");
                // tagline_container
                // tagline_container.remove();
                // accordion_container.classList.add("col-6");
                // tagline_container.setAttribute("style", "min-width: 300px;max-width: 85%; margin-left: -20%");
                // tagline_container.setAttribute("style", "min-width: 300px");


            } else{
                tagline_container.setAttribute("class", "");
                accordion_container.setAttribute("class", "");
                tagline_container.classList.add("col-9");
                accordion_container.classList.add("col-3");
                // tagline_container.setAttribute("style", "min-width: 300px;max-width: 85%; margin-left: -20%");
                // accordion_container.setAttribute("style", "min-width: 300px; min-width: 10%; max-width: 20%;");
            }
            // let collapseList = document.createElement("ul");
            //for more info
            let collapseListHTML = '<div>';
            for (let i = 1; i < 7; i++){
                let info_field = "modal_info" + i;
                let info_text = "modal_info_text" + i;
                let info_url = "modal_info_url" + i;

                let modal_info_text = modal_data[info_field][info_text];
                let modal_info_url = modal_data[info_field][info_url];
                if ((modal_info_text == '') && (modal_info_url == '')){
                    continue;
                }
                // console.log(modal_info_text);
                // console.log(modal_info_url);
                let listItem = document.createElement('li');
                let anchor = document.createElement('a');
                anchor.setAttribute('href', modal_info_url); 
                anchor.textContent = modal_info_text;

                listItem.appendChild(anchor);

                // collapseList.appendChild(listItem);
                collapseListHTML += `<div> <a href="${modal_info_url}">${modal_info_text}</a> </div>`;
                collapseListHTML += '</div>';
            }
            //for photos:
            console.log(modal_data)
            let modal_id = modal_data.id;
            let collapsePhotoHTML = '<div>';
            for (let i = 1; i < 7; i++){
                let info_field = "modal_photo" + i;
                let info_text = "modal_photo_text" + i;

                // let info_url = "modal_photo_url" + i;
                let info_url;
                let loc = "modal_photo_location" + i;
                if (modal_data[info_field][loc] === "External"){
                    info_url = "modal_photo_url" + i;
                } else {
                    info_url = "modal_photo_internal" + i;
                }

                let modal_info_text = modal_data[info_field][info_text];
                let modal_info_url = modal_data[info_field][info_url];
                if ((modal_info_text == '') && (modal_info_url == '')){
                    continue;
                }
                // console.log(modal_info_text);
                // console.log(modal_info_url);
                let listItem = document.createElement('li');
                let anchor = document.createElement('a');
                anchor.setAttribute('href', modal_info_url); 
                anchor.textContent = modal_info_text;

                listItem.appendChild(anchor);

                // collapseList.appendChild(listItem);
                collapsePhotoHTML += `<div> <a href="${modal_info_url}">${modal_info_text}</a> </div>`;
                collapsePhotoHTML += '</div>';
            }
            
            let accordionItem1 = createAccordionItem("accordion-item-1", "accordion-header-1", "accordion-collapse-1", "More Info", collapseListHTML);
            let accordionItem2 = createAccordionItem("accordion-item-2", "accordion-header-2", "accordion-collapse-2", "Images", collapsePhotoHTML);
           
            acc.appendChild(accordionItem1);
            acc.appendChild(accordionItem2);
            if (is_mobile()){
                let accordionItem3 = createAccordionItem("accordion-item-3", "accordion-header-3", "accordion-collapse-3", "Tagline", modal_tagline);
                acc.prepend(accordionItem3);

            }



            accordion_container.appendChild(acc);
            // allkeyobj[key] = true;

            //for tabs jere:
            // window.addEventListener('load', function() {
            
            let num_tabs = Number(modal_data["modal_tab_number"]);
            console.log(num_tabs);
            for (let i =1; i <= num_tabs; i++){
                let tab_key = "modal_tab_title" + i;
                let tab_title = modal_data[tab_key];
                console.log(tab_title);

               
                create_tabs(i, tab_key, tab_title, title, modal_id);
                console.log(`iteration ${i}, tab key ${tab_key} tab title ${tab_title}`);
                if (i === num_tabs){
                    break;
                }
                // let tabContentContainer = document.getElementById("myTabContent");
                // tabContentContainer.innerHTML = '';
                // document.querySelector("#myModal > div > div>div").innerHTML = '';
                // let myModal = document.querySelector("#myModal");
                let mdialog = document.querySelector("#myModal > div"); //document.querySelector("#myModal");
                // trapFocus(myModal);
                trapFocus(mdialog);
              
            }
        // });
            



        })
    .catch(error => console.error('Error fetching data:', error));
    
}

/**
 * Creates and displays a full-screen button for the scene SVG element.
 * This button allows users to view scene in full screen (and escape to leave)
 *
 * @param {string} svgId - The ID of the SVG element to be made full-screen.
 * 
 * The function performs the following:
 * 1. Checks if the instance allows a full-screen button (within WP) and if the browser supports full-screen functionality.
 * 2. If supported, creates a button with appropriate attributes and prepends it to the container (`#toc-container`).
 * 3. Sets up an event listener on the SVG element to adjust its dimensions when entering or exiting full-screen mode.
 * 4. Defines the `openFullScreen` function to trigger full-screen mode for the SVG and appends a modal to it.
 * 5. Adds a click event to the button that calls the `openFullScreen` function.
 * 
 * Usage: called within load_svg
 */
function full_screen_button(svgId) {
    if (scene_full_screen_button != "yes") {
        return;
    }

    if ((document.fullscreenEnabled || document.webkitFullscreenEnabled)) {
        const svg = document.querySelector('#svg1 svg');
        const viewBox = svg.viewBox.baseVal;

        const g = document.createElementNS("http://www.w3.org/2000/svg", "g");
        const rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
        rect.setAttribute("width", "80");
        rect.setAttribute("height", "20");
        rect.setAttribute("fill", "#03386c");
        rect.setAttribute("rx", "5");

        const text = document.createElementNS("http://www.w3.org/2000/svg", "text");
        text.textContent = "Full Screen";
        text.setAttribute("fill", "white");
        text.setAttribute("font-size", "12");
        text.setAttribute("text-anchor", "middle");
        text.setAttribute("dominant-baseline", "middle");
        text.setAttribute("x", "40");
        text.setAttribute("y", "10");

        g.appendChild(rect);
        g.appendChild(text);
        g.setAttribute("transform", `translate(${viewBox.width - 87}, 10)`);
        // g.style.borderRadius = '0 0 0 0'

        svg.appendChild(g);
        
        var webkitElem = document.getElementById(svgId);
        webkitElem.addEventListener('webkitfullscreenchange', (event) => {
            if (document.webkitFullscreenElement) {
                webkitElem.style.width = (window.innerWidth) + 'px';
                webkitElem.style.height = (window.innerHeight) + 'px';
            } else {
                webkitElem.style.width = width;
                webkitElem.style.height = height;
            }
        });
        
        function toggleFullScreen() {
            var elem = document.getElementById(svgId);
            if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                }
                text.textContent = "Exit";

                let modal = document.getElementById("myModal");
                elem.prepend(modal);
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                }
                text.textContent = "Full Screen";
            }
        }

        g.addEventListener('click', toggleFullScreen);
        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement) {
                text.textContent = "Full Screen";
            }
        });

        document.addEventListener('webkitfullscreenchange', function() {
            if (!document.webkitFullscreenElement) {
                text.textContent = "Full Screen";
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape" && (document.fullscreenElement || document.webkitFullscreenElement)) {
                text.textContent = "Full Screen";
            }
        });

    }
}

/**
 * Creates a toggle button that lets user toggle on/off the text within the scene.
 * 
 * The function performs the following:
 * 1. Checks if the instance wants the toggle button or not (within WP)
 * 2. If supported, creates a button with appropriate attributes and prepends it to the container (`#toc-container`).
 * 3. Based on the user-defined initial state of the toggle (again in WP), either sets toggle to on or off. 
 * 4. Adds a click event to the button that shows/hides text from element
 * 
 * Usage: called within load_svg
 */
function toggle_text() {
    if (scene_text_toggle == "none"){
        return;
    }

    let initialState = scene_text_toggle === "toggle_on"; //this should be done on the SCENE side of things
    let svgText = document.querySelector("#text");
    // button.innerHTML = initialState ? "Hide Image Text" : "Show Image Text";

    if (initialState) {
        svgText.setAttribute("display", "");
    } else {
        svgText.setAttribute("display", "None");
    }

    const svg = document.querySelector('#svg1 svg');
    // Get the SVG's viewBox
    const viewBox = svg.viewBox.baseVal;
    // Create a group element to hold our button
    const g = document.createElementNS("http://www.w3.org/2000/svg", "g");
    // Create a rect element for the button background
    const rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
    rect.setAttribute("width", "60");
    rect.setAttribute("height", "20");
    rect.setAttribute("fill", "#007bff");
    rect.setAttribute("rx", "5");
    // Create a text element for the button label
    const text = document.createElementNS("http://www.w3.org/2000/svg", "text");
    text.textContent = "Click";
    text.setAttribute("fill", "white");
    text.setAttribute("font-size", "12");
    text.setAttribute("text-anchor", "middle");
    text.setAttribute("dominant-baseline", "middle");
    text.setAttribute("x", "30");
    text.setAttribute("y", "10");

    g.appendChild(rect);
    g.appendChild(text);
    g.setAttribute("transform", `translate(${viewBox.width - 70}, 10)`);
    svg.appendChild(g);

    const toggleGroup = document.createElementNS("http://www.w3.org/2000/svg", "g");

    const toggleRect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
    toggleRect.setAttribute("width", "80");
    toggleRect.setAttribute("height", "20");
    toggleRect.setAttribute("fill", "#03386c");
    toggleRect.setAttribute("rx", "5");

    const toggleText = document.createElementNS("http://www.w3.org/2000/svg", "text");
    toggleText.setAttribute("fill", "white");
    toggleText.setAttribute("font-size", "12");
    toggleText.setAttribute("text-anchor", "middle");
    toggleText.setAttribute("dominant-baseline", "middle");
    toggleText.setAttribute("x", "40");
    toggleText.setAttribute("y", "10");
    toggleText.textContent = initialState ? "Hide Text" : "Show Text";

    toggleGroup.appendChild(toggleRect);
    toggleGroup.appendChild(toggleText);

    toggleGroup.setAttribute("transform", `translate(${viewBox.width - 87}, 35)`);
    svg.appendChild(toggleGroup);

    toggleGroup.addEventListener('click', function() {
        if (svgText.getAttribute("display") === "none") {
            svgText.setAttribute("display", "");
            toggleText.textContent = "Hide Text";
        } else {
            svgText.setAttribute("display", "none");
            toggleText.textContent = "Show Text";
        }
    });
}

/**
 * Creates a sectioned list table of contents that is organized based on the user-defined sections in WP for any given scene.
 * This function generates sections dynamically and organizes them in a color-coded way
 * 
 * The function:
 * 1. Extracts unique section names from the `section_name` property of each object in `child_obj`.
 * 2. Sorts the sections and assigns each section a color from the `colors` array. Ensures that consecutive sections don't have the same color.
 * 3. Builds a sectioned table of contents, where each section name is a header and below are its icons,  styled with its assigned color.
 * 4. Appends the generated TOC structure to the `#toc-container` element in the DOM.
 * 
 * @returns {void} - The function modifies the DOM by adding a dynamically generated TOC.
 * 
 * Usage: 
 * called in table_of_contents, if user has selected sectioned list option in WP
 */
function sectioned_list(){
    let sections = [];
    for (let key in child_obj) {
        let section = child_obj[key]['section_name'];
        console.log('section herreeeeeee');

        if (!sections.includes(section) && section!='None') {
            sections.push(section);
        }
        sectionObj[key] = section;
    }
    sections.sort();
    console.log(sectionObj);
    console.log(sections);
    sections.push('None');

    let toc_container = document.querySelector("#toc-container");
    let toc_group = document.createElement("div");
    // toc_group.classList.add("accordion");
    toc_group.setAttribute("id", "toc-group");
    // let colorIdx = 0;

    for (let i = 0; i < sections.length; i++) {
    
        let sect = document.createElement("div");
        // sect.classList.add("accordion-item");

        let heading = document.createElement("h5");
        heading.setAttribute("id", `heading${i}`);
        if (sections[i] != "None"){
            // heading.innerHTML = sections[i];
            heading.innerHTML = scene_data[sections[i]][`scene_section_title${i+1}`];
            let color =  scene_data[sections[i]][`scene_section_hover_color${i+1}`];
            heading.style.backgroundColor = hexToRgba(color, 0.2);
            heading.style.color = 'black';
            heading.style.display = 'inline-block';
            // if (scene_same_hover_color_sections != "yes"){
            //     heading.style.backgroundColor = hexToRgba(color, 0.3);
            // }
            // heading.style.backgroundColor = hexToRgba(color, 0.3);
            heading.style.padding = '0 5px';
        } else {
            heading.innerHTML = 'No Section';
            let color = scene_default_hover_color;
            heading.style.backgroundColor = hexToRgba(color, 0.2);
            heading.style.color = 'black';
            heading.style.display = 'inline-block';
        }

        sect.appendChild(heading);

        let tocCollapse = document.createElement("div");

        let tocbody = document.createElement("div");
        // tocbody.classList.add("accordion-body");

        let sectlist = document.createElement("ul");
        sectlist.setAttribute("id", sections[i]);
        sectlist.setAttribute("style", `color: black`);


        tocbody.appendChild(sectlist);
        tocCollapse.appendChild(tocbody);

        sect.appendChild(tocCollapse);
        toc_group.appendChild(sect);
    }
    toc_container.appendChild(toc_group);
    console.log(sectColors);
}

/**
 * Creates a collapsible table of contents that is organized based on the user-defined sections in WP for any given scene.
 * This function generates sections dynamically and organizes them in an accordion-style layout.
 * 
 * The function:
 * 1. Extracts unique section names from the `section_name` property of each object in `child_obj`.
 * 2. Sorts the sections and assigns each section a color from the `colors` array. Ensures that consecutive sections don't have the same color.
 * 3. Builds an accordion-style TOC, where each section is collapsible and styled with its assigned color.
 * 4. Appends the generated TOC structure to the `#toc-container` element in the DOM.
 * 
 * @returns {void} - The function modifies the DOM by adding a dynamically generated TOC.
 * 
 * Usage: 
 * called in table_of_contents, if user has selected sectioned list option in WP
 */
function toc_sections() {
    let sections = [];
    // console.log(child_obj);
    for (let key in child_obj) {
        let section = child_obj[key]['section_name'];
        console.log('section herreeeeeee');
        console.log(child_obj[key]['section_name']);
        if (!sections.includes(section) && section!='None') {
            sections.push(section);
        }
        sectionObj[key] = section;
    }
    sections.sort();
    console.log(sectionObj); //use this for naming stuff
    sections.push('None');

    console.log(sections);
    console.log(scene_sections);

    let toc_container = document.querySelector("#toc-container");
    let toc_group = document.createElement("div");
    toc_group.classList.add("accordion");
    toc_group.setAttribute("id", "toc-group");
    // let colorIdx = 0;

    for (let i = 0; i < sections.length; i++) {


        let sect = document.createElement("div");
        sect.classList.add("accordion-item");

        let heading = document.createElement("h2");
        heading.classList.add("accordion-header");
        heading.setAttribute("id", `heading${i}`);

        let button = document.createElement("button");
        // let color = scene_sections[sections[i]];
      

        // button.classList.add("accordion-button");
        button.classList.add("accordion-button", "collapsed");
        button.setAttribute("type", "button");
        button.setAttribute("data-bs-toggle", "collapse");
        button.setAttribute("data-bs-target", `#toccollapse${i}`);
        button.setAttribute("aria-expanded", "false");
        button.setAttribute("aria-controls", `toccollapse${i}`);
        if (sections[i]!="None"){
            console.log(sections[i]);
            console.log(`scene_section_title${i}`);
            button.innerHTML = scene_data[sections[i]][`scene_section_title${i+1}`];
            // if (scene_same_hover_color_sections != "yes"){
            //     // heading.style.backgroundColor = hexToRgba(color, 0.3);
            //     button.style.backgroundColor = hexToRgba(color, 0.2);
            // }
            let color =  scene_data[sections[i]][`scene_section_hover_color${i+1}`];
            button.style.backgroundColor = hexToRgba(color, 0.2);
            // let span = document.createElement('span');
            // span.style.color = 'black';
            // span.innerHTML = sections[i];
            // span.style.backgroundColor = hexToRgba(color, 0.2); // Only highlight the text
            // button.innerHTML = ''; // Clear the button content
            // button.appendChild(span);

        } else {
            // button.innerHTML = "Table of Contents";
            button.innerHTML = 'No Section';
            let color = scene_default_hover_color;
            button.style.backgroundColor = hexToRgba(color, 0.2);
            // button.style.color = 'black';
            // button.style.display = 'inline-block';
        }
        

        let arrowSpan = document.createElement("span");
        arrowSpan.classList.add("arrow");
        button.appendChild(arrowSpan);
     
        if (sections[i].length > 20){
            console.log('section name: ');
            console.log(sections[i]);
            arrowSpan.style.marginRight = '15%';
        } else {
            arrowSpan.style.marginRight = '63%';
        }
        

        heading.appendChild(button);
        sect.appendChild(heading);

        let tocCollapse = document.createElement("div");
        tocCollapse.setAttribute("id", `toccollapse${i}`);
        tocCollapse.classList.add("accordion-collapse", "collapse");
        tocCollapse.setAttribute("aria-labelledby", `heading${i}`);
        // tocCollapse.setAttribute("data-bs-parent", "#toc-group");

        let tocbody = document.createElement("div");
        tocbody.classList.add("accordion-body");

        let sectlist = document.createElement("ul");
        sectlist.setAttribute("id", sections[i]);
        tocbody.appendChild(sectlist);
        tocCollapse.appendChild(tocbody);

        sect.appendChild(tocCollapse);
        toc_group.appendChild(sect);
    }
    toc_container.appendChild(toc_group);
    console.log(sectColors);
}


/**
 * Generates a Table of Contents (TOC) for a document, with links that either open modal windows or redirect to external URLs.
 * The TOC style is determined by `thisInstance.instance_toc_style`, which can be:
 *  - "accordion": Generates sections in an accordion layout.
 *  - "list": Uses a simple list layout.
 *  - "sectioned_list": Organizes content in sections based on their grouping.
 * 
 * For each TOC item:
 * - If `child_obj[key]['modal']` is true, the item will open a modal window and trigger `render_modal(key)` to load content.
 * - If `child_obj[key]['external_url']` is present, the item will link to an external URL.
 * 
 * Additional functionality includes:
 * - Mouse hover effects on associated SVG elements, highlighting sections.
 * - Event listeners for closing the modal window when clicking outside or on the close button.
 * 
 * @returns {void} - Modifies the DOM by generating TOC elements and attaching event listeners.
 * 
 * Usage:
 * Called in load_svg if user wants to show the sections
 */

function table_of_contents(){
    // toc_sections();
    // sectioned_list();
    // console.log(thisInstance);
    // if (thisInstance.instance_toc_style == "accordion"){ //this should be done on the SCENE side of things
    console.log('child_obj HERE');
    console.log(child_obj);
    if (scene_toc_style == "accordion"){ //this should be done on the SCENE side of things
        toc_sections();
    } else {
        sectioned_list();
    }               
    // let elem = document.getElementById("toc1");
    // let elem = document.createElement("ul")
    // use  sorted_child_objs
    console.log(sorted_child_objs);
 
   
    // for (let key in child_obj){
    for (let obj of sorted_child_objs){
        console.log('obj here..');
        console.log(obj.modal_icon_order);
        key = obj.original_name;
        console.log("key here...");
        console.log(key);
        // document.querySelector("#Section\\ 1")
        // console.log(key);
        if (sectionObj[key]=="None"){
            // continue;
            console.log("bruhhhhhh");
            console.log(key);
        }
        let elem = document.getElementById(child_obj[key]['section_name']);
        console.log(elem);
        let item = document.createElement("li");

        
        let title = child_obj[key]['title'];  
        let link = document.createElement("a");
        link.setAttribute("id", title.replace(/\s+/g, '_'));

        let modal = child_obj[key]['modal'];
        if (modal) {
            link.setAttribute("href", '#'); //just added
            link.classList.add("modal-link"); 
            link.innerHTML = title;

            item.appendChild(link);
            
            item.addEventListener('click', function() {
                
                let modal = document.getElementById("myModal");
                modal.style.display = "block";
                render_modal(key);
            });

            let closeButton = document.getElementById("close");
            closeButton.addEventListener('click', function() {
                    
                // modal.style.display = "none";
                let accordion_container = document.getElementById('accordion-container');
                accordion_container.innerHTML = '';
                if (!is_mobile()){
                    let tagline_container = document.getElementById('tagline-container');
                    tagline_container.innerHTML = '';


                }
                // let tagline_container = document.getElementById('tagline-container');
                document.getElementById("myTabContent").innerHTML = '';
                // tagline_container.innerHTML = '';
                history.pushState("", document.title, window.location.pathname + window.location.search);
        });
        window.onclick = function(event) {
            if (event.target === modal) { // Check if the click is outside the modal content
                // modal.style.display = "none";
                document.getElementById('accordion-container').innerHTML = '';
                if (!is_mobile()){
                    document.getElementById('tagline-container').innerHTML = '';
                }
                // document.getElementById('tagline-container').innerHTML = '';
                document.getElementById("myTabContent").innerHTML = '';
                history.pushState("", document.title, window.location.pathname + window.location.search);
            }
        };
        }
        
        else{
            link.href = child_obj[key]['external_url'];
            // console.log(link.href);
            link.innerHTML = title;
            item.appendChild(link);
        }
        let svg_elem = document.querySelector('g[id="' + key + '"]');
        // console.log(elem);
        //CHANGE HERE FOR TABLET STUFF
        link.style.textDecoration = 'none';
        // link.style.color = "#343a40";
        

        item.addEventListener('mouseover', function(){
            // console.log('mousing over: ', key); 
            // svg_elem.style.stroke = "yellow";
            // svg_elem.style.stroke = thisInstance.instance_hover_color;
            // console.log(sectionObj);
            // console.log(sectColors);
            // if (thisInstance.instance_colored_sections === "yes"){ //this should be done on the SCENE side of things, will havet o bring this back
            

            if (scene_same_hover_color_sections != "yes" && sectionObj[key]!="None" ){ //this should be done on the SCENE side of things, will havet o bring this back
                // console.log(scene_sections[sectionObj[key]]);

                let section_name = sectionObj[key];
                let section_num = section_name.substring(section_name.length - 1, section_name.length);
                // console.log(section_num);
                let this_color = `scene_section_hover_color${section_num}`;
                svg_elem.style.stroke = scene_data[sectionObj[key]][this_color];
                // console.log(scene_data[sectionObj[key]]);
                // let num = scene_data[sectionObj[key]];
                // console.log(num);
                // svg_elem.style.stroke = scene_data[sectionObj[key]];

            } else{
                svg_elem.style.stroke = scene_default_hover_color;
            }
            
                svg_elem.style.strokeWidth = "3";
        });
        item.addEventListener('mouseout', function(){
            // console.log('mousing out: ', key); 
            svg_elem.style.stroke = "";
            svg_elem.style.strokeWidth = "";
        });
        
        
        elem.appendChild(item);
        // console.log(elem);
        // return elem;
    }
        
    
}

/**
 * Generates a simple list-based Table of Contents (TOC), where items either open modal windows or link to external URLs.
 * The sections are not explicitly displayed, but their colors are used for highlighting.
 * 
 * Each TOC item:
 * - If `child_obj[key]['modal']` is true, the item will open a modal window and trigger `render_modal(key)` to load content.
 * - If `child_obj[key]['external_url']` is present, the item will link to an external URL.
 * 
 * Additional functionality:
 * - Mouse hover effects highlight associated SVG elements, using section colors if `thisInstance.instance_colored_sections` is set to "yes".
 * - Modal close event handling, including clicking outside the modal window to close it.
 * 
 * @returns {void} - Modifies the DOM by generating TOC list items and attaching event listeners.
 * 
 * Usage:
 * called in load_svg if user wants a list with no sections displayed/no sections exist
 * 
 */

function list_toc(){
 
    let sections = [];
    for (let key in child_obj) {
        let section = child_obj[key]['section_name'];
        if (!sections.includes(section)) {
            sections.push(section);
        }
        sectionObj[key] = section;
    }
    sections.sort();
    // console.log(sections);

    let toc_container = document.querySelector("#toc-container");
    let toc_group = document.createElement("ul");
    let colorIdx = 0;
    let i = 0;
    console.log("sorted list here...");
    console.log(sorted_child_objs);
    // for (let obj of sorted_child_objs){
    //     
    // for (let key in child_obj) {
    for (let obj of sorted_child_objs){
        // let elem = document.getElementById(child_obj[key]['section_name']);
        // sectColors[sections[i]] = colors[colorIdx]; 
        // colorIdx = (colorIdx + 1) % colors.length;
        console.log('obj here..');
        console.log(obj.modal_icon_order);
        key = obj.original_name;
        console.log("key here...");
        console.log(key);
        i++;

        let item = document.createElement("li");
    
        let title = child_obj[key]['title'];  
        let link = document.createElement("a");
        let modal = child_obj[key]['modal'];
    
        if (modal) {
            link.setAttribute("href", '#'); //just added
            link.setAttribute("id", title.replace(/\s+/g, '_'));
          
            // link.setAttribute("role", "button");

            link.classList.add("modal-link");
            link.innerHTML = title;
            item.appendChild(link);
    
            item.addEventListener('click', function() {
                let modal = document.getElementById("myModal");
                modal.style.display = "block";
                render_modal(key);
            });
    
            let closeButton = document.getElementById("close");
            closeButton.addEventListener('click', function() {
                let modal = document.getElementById("myModal");
                // link.setAttribute("href", ''); //just added

                modal.style.display = "none";
                history.pushState("", document.title, window.location.pathname + window.location.search);
            });
            window.onclick = function(event) {
                if (event.target === modal) { 
                    // link.setAttribute("href", ''); //just added
                    modal.style.display = "none";
                    history.pushState("", document.title, window.location.pathname + window.location.search);

                }
            };
        } else {
            link.href = child_obj[key]['external_url'];
            link.innerHTML = title;
            item.appendChild(link);
        }
    
        let svg_elem = document.querySelector('g[id="' + key + '"]');
    
        item.addEventListener('mouseover', function() {
          
            svg_elem.style.stroke = scene_default_hover_color;
            svg_elem.style.strokeWidth = "3";
        });
    
        item.addEventListener('mouseout', function() {
            svg_elem.style.stroke = "";
            svg_elem.style.strokeWidth = "";
        });
    
        toc_group.appendChild(item);
    }
    toc_container.appendChild(toc_group);
    document.querySelector("#toc-container > ul").style.paddingLeft = '11rem';
}


/**
 * Generates and handles modal windows or external URL redirects when SVG elements are clicked.
 * 
 * This function adds click event listeners to SVG elements (identified by `g[id="key"]`) from `child_obj`.
 * 
 * - If the `child_obj[key]['modal']` value is true:
 *   - Clicking the SVG element or corresponding mobile container (`#key-container`) opens a modal window.
 *   - The `render_modal(key)` function is triggered to load modal content.
 *   - Clicking outside the modal or on the close button hides the modal and clears the content.
 * 
 * - If `child_obj[key]['modal']` is false:
 *   - Clicking the SVG element redirects to the external URL specified in `child_obj[key]['external_url']`.
 *   - For mobile devices, a similar event is added to the container element (`#key-container`).
 * 
 * Modal close behavior:
 * - The modal is closed when the close button is clicked or when a click occurs outside the modal.
 * - Upon closing, various content containers are cleared, and the URL is changed back to the original scene URL
 * 
 * @returns {void} - Directly manipulates the DOM by attaching event listeners for modal display or external URL redirection.
 * 
 * Usage: 
 * Called in mobile helper, load_svg to actually add modal capabilities to scene element
 */
function add_modal(){
    for (let key in child_obj){
        let elem = document.querySelector('g[id="' + key + '"]');
        if (child_obj[key]['modal']){
            // let elem = document.querySelector('g[id="' + key + '"]');
            let modal = document.getElementById("myModal");
            let closeButton = document.getElementById("close");
            
            // elem.addEventListener('click', function() {
            //         modal.style.display = "block";
            //         render_modal(key );

            // });
            
            if (mobileBool){
                let itemContainer = document.querySelector(`#${key}-container`);
                itemContainer.addEventListener('click', function() {
                    modal.style.display = "block";
                    render_modal(key );

            });
            } else {
                elem.addEventListener('click', function(event) {
                    // gtag('event', 'modal_icon_click', {
                    //     'event_category': 'Button Interaction',
                    //     'event_label': 'Track Me Button',
                    //     'value': 1
                    //   });

                    modal.style.display = "block";
                    render_modal(key );

            });
            }
            
            closeButton.addEventListener('click', function() {
                    
                    modal.style.display = "none";
                    let accordion_container = document.getElementById('accordion-container');
                    accordion_container.innerHTML = '';

                    // let tagline_container = document.getElementById('tagline-container');
                    // tagline_container.innerHTML = '';


                    let myTab = document.getElementById('myTab');
                    myTab.innerHTML = '';

                    let tabContentContainer = document.getElementById("myTabContent");
                    tabContentContainer.innerHTML = '';
                    history.pushState("", document.title, window.location.pathname + window.location.search);
            });
            window.onclick = function(event) {
                if (event.target === modal) { // Check if the click is outside the modal content
                    modal.style.display = "none";
                    document.getElementById('accordion-container').innerHTML = '';
                    document.getElementById('tagline-container').innerHTML = '';
                    document.getElementById('myTab').innerHTML = '';
                    document.getElementById('myTabContent').innerHTML = '';
                    history.pushState("", document.title, window.location.pathname + window.location.search);
                }
            };
    
        } else {
            elem.addEventListener('click', function(event) {
                // gtag('event', 'modal_icon_click', {
                //     'event_category': 'Button Interaction',
                //     'event_label': 'Track Me Button',
                //     'value': 1
                //   });

                let link =  child_obj[key]['external_url'];
                window.location.href = link;

        });
        if (mobileBool){
            let itemContainer = document.querySelector(`#${key}-container`);
            itemContainer.addEventListener('click', function() {
                let link =  child_obj[key]['external_url'];
                window.location.href = link;

        });
        // let modalDialog = document.querySelector("#myModal > div");
        // modalDialog.setAttribute("style", "z-index: 9999;/* margin: 10% auto; */margin-top: 65%;max-width: 90%;margin-left: 17px;");
        }
        }
    }
}






// loadSVG(url, "svg1");
/**
 * Waits for a DOM element matching the provided selector to become available.
 * 
 * This function returns a Promise that resolves when the DOM element matching the given `selector` is found.
 * If the element is already present, it resolves immediately. If not, it uses a `MutationObserver` to detect when
 * the element is added to the DOM and then resolves the Promise.
 * 
 * @param {string} selector - The CSS selector of the DOM element to wait for.
 * @returns {Promise<Element>} - A Promise that resolves with the found DOM element.
 * 
 * Usage:
 * called within handleHashNavigation, used to wait for the rendering of the modal button. 
 */
async function waitForElement(selector) {
    return new Promise(resolve => {
        const element = document.querySelector(selector);
        if (element) {
            resolve(element);
        } else {
            const observer = new MutationObserver(() => {
                const element = document.querySelector(selector);
                if (element) {
                    observer.disconnect();
                    resolve(element);
                }
            });
            observer.observe(document.body, { childList: true, subtree: true });
        }
    });
}


/**
 * Handles hash-based URL navigation. This is for when someone goes to the link for a certain figure (.../#CASheephead/1)
 * 
 * 1. First checks if the URL has a hash, making it a figure link
 * 2. Does some string parsing stuff to clean up the URL, from which we can extract information about the scene, icon, and tab
 * 3. Updates new URL, gets necessary DOM elements through waitForElement and fires event handlers to open up figure
 * 
 * @returns {Promise<void>} - A Promise that resolves when navigation handling is complete.
 * 
 * Usage:
 * Called after init when DOMcontent loaded. 
 */
async function handleHashNavigation() {
    //maybe in here check that the scene is/is not an overview
    if (window.location.hash) {
        console.log(window.location.hash)
        let tabId = window.location.hash.substring(1);
        console.log(tabId);

        let modalName = tabId.split('/')[0];
        console.log(modalName);

        tabId = tabId.replace(/\//g, '-');
        console.log(window.location.pathname + window.location.search);
        history.pushState("", document.title, window.location.pathname + window.location.search);
        let modName;
        if (is_mobile()){
            let modModal =  modalName.replace(/_/g, ' ');
            modName = child_ids_helper[modModal] + '-container';
        } else{
            modName = modalName;
        }
        // window.location.href = window.location.href;
        let modalButton = await waitForElement(`#${modName}`);
        console.log(modalButton);

        modalButton.click();

        let tabButton = await waitForElement(`#${tabId}`);
        console.log(tabButton);
        tabButton.click();
    } else {
        console.log("nope");
    }
}



/**
 * Fetches instance details from the WordPress REST API.
 *
 * This asynchronous function retrieves data from the WordPress REST API endpoint for instances (`/wp-json/wp/v2/instance`)
 * using the current protocol and host. The results are fetched in ascending order.
 * It handles network errors and returns the data as a JSON object.
 *
 * @returns {Promise<Object[]>} - A Promise that resolves to an array of instance objects retrieved from the API.
 * 
 * @throws {Error} - Throws an error if the fetch request fails or the response is not successful (i.e., not OK).
 * 
 * Usage: called in init function to set to global variable testData, which is used to get information about current instance, section/color information
 */
async function load_instance_details() { //this should be done on the SCENE side of things; might not need this, may replace w scene postmeta call. keep for now
    const protocol = window.location.protocol;
    const host = window.location.host;
    const fetchURL = `${protocol}//${host}/wp-json/wp/v2/instance?&order=asc`;
  
    try {
        const response = await fetch(fetchURL);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching data:', error);
        throw error;
    }
}

/**
 * Initializes the application by loading instance details, setting up the scene location, 
 * defining instance-specific settings, and rendering the SVG element.
 *
 * This asynchronous function serves as the driver for the script. It performs the following tasks:
 * 1. Fetches instance details by calling `load_instance_details()` and stores the data in a global variable.
 * 2. Determines the scene location by calling `make_title()` (which also makes the title, other scene elemsnts) and stores the result in `sceneLoc`, which is also a global variable.
 * 3. Finds the instance object corresponding to the scene location and assigns it to `thisInstance`.
 * 4. Extracts the hover colors for the instance and assigns them to a global variable `colors`.
 * 5. Calls `loadSVG(url, "svg1")` to load and render an SVG based on the provided URL.
 *
 * If any errors occur during these steps, they are caught and logged to the console.
 * 
 * @async
 * @function init
 * 
 * @throws {Error} - If fetching instance details, determining the scene location, or loading the SVG fails, an error is caught and logged.
 *
 * Usage: right below; this is essentially the driver function for the entire file, as it pretty much calls every other function inside here. 
 */
async function init() {
    try {
        // testData = await load_instance_details();
        // testData =
        console.log("here is the global variable");
        // console.log(testData);
        // hover_color = "red";
        // console.log(hover_color);
        sceneLoc = make_title(); //this should be done on the SCENE side of things, maybe have make_title return scene object instead
        thisInstance = sceneLoc;
        console.log("scene location is ");
        console.log(sceneLoc);

        // thisInstance = testData.find(data => data.id === Number(sceneLoc)); //this should be done on the SCENE side of things; 
        console.log(thisInstance);
        // colors = thisInstance.instance_hover_color.split(',');
        // colors = ['red', 'yellow']
        // console.log(colors[0]);
        // console.log(colors);
        
        loadSVG(url, "svg1"); // Call load_svg with the fetched data

    } catch (error) {
        console.error('Error:', error);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    init(); 
    
    handleHashNavigation();

});