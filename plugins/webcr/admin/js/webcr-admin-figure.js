/**
 * @file This file contains JavaScript functions that manage the interactive
 * elements and behaviors specific to the "Figure" custom post type editor
 * within the WordPress admin panel.
 * @version 1.0.0
 */
'use strict';

/**
 * @global {Object} jsonColumns - An object that stores the column names from the JSON data file.
 * @global {string} fieldValueSaved - A string that holds a previously saved field value.
 */
let jsonColumns;
let fieldValueSaved;

/**
 * Fetches and updates the available scenes based on the selected instance.
 * Populates the "figure_scene" select field with scenes associated with the chosen instance.
 * Also clears and populates the "figure_modal" and "figure_tab" fields.
 *
 * @async
 * @function figureInstanceChange
 * @throws {Error} Throws an error if there's a network problem fetching data.
 * @listens change
 * @example
 * // When the user changes the instance selection:
 * document.getElementsByName("location")[0].addEventListener('change', figureInstanceChange);
 */
function figureInstanceChange(){
    const protocol = window.location.protocol;
    const host = window.location.host;
    const figureInstance = document.getElementsByName("location")[0].value;
    const restScene = protocol + "//" + host  + "/wp-json/wp/v2/scene?_fields=id,title&orderby=title&order=asc&scene_location="+figureInstance;

    fetch(restScene)
    .then(response => response.json())
    .then(data => {
        let figureScene = document.getElementsByName("figure_scene")[0];
        figureScene.value ="";
        figureScene.innerHTML ="";
        let optionScene1 = document.createElement('option');
        optionScene1.text = "Scenes";
        optionScene1.value = "";
        figureScene.add(optionScene1);
    
        data.forEach(targetRow => {
                let optionScene = document.createElement('option');
                optionScene.value = targetRow['id'];
                optionScene.text = targetRow['title']['rendered'];
                figureScene.appendChild(optionScene);
        });

        let figureModal = document.getElementsByName("figure_modal")[0];
        figureModal.value ="";
        figureModal.innerHTML ="";
        let optionModal = document.createElement('option');
        optionModal.text = "Icons";
        optionModal.value = "";
        figureModal.add(optionModal);

        let figureTab = document.getElementsByName("figure_tab")[0];
        figureTab.value ="";
        figureTab.innerHTML ="";
        let optionTab = document.createElement('option');
        optionTab.text = "Tabs";
        optionTab.value = "";
        figureTab.add(optionTab);
    })
    .catch((err) => {console.error(err)});
}

/**
 * Fetches and updates the available icons based on the selected scene.
 * Populates the "figure_modal" select field with icons associated with the chosen scene.
 * Also clears and populates the "figure_tab" field.
 *
 * @async
 * @function figureSceneChange
 * @throws {Error} Throws an error if there's a network problem fetching data.
 * @listens change
 * @example
 * // When the user changes the scene selection:
 * document.getElementsByName("figure_scene")[0].addEventListener('change', figureSceneChange);
 */
function figureSceneChange(){
    const protocol = window.location.protocol;
    const host = window.location.host;
    const figureScene = document.getElementsByName("figure_scene")[0].value;

    //FIX: the REST API for modal is retrieving all records even when icon_function and modal_scene are set for some reason 
    // CHECK - THIS IS FIXED I THINK?
    const restModal = protocol + "//" + host  + "/wp-json/wp/v2/modal?_fields=id,title,modal_scene,icon_function&orderby=title&order=asc";
    fetch(restModal)
    .then(response => response.json())
    .then(data => {
        let figureModal = document.getElementsByName("figure_modal")[0];
        figureModal.value ="";
        figureModal.innerHTML ="";
        let optionIcon1 = document.createElement('option');
        optionIcon1.text = "Icons";
        optionIcon1.value = "";
        figureModal.add(optionIcon1);
    
        data.forEach(targetRow => {
            if (targetRow['icon_function']=="Modal" && targetRow['modal_scene']==figureScene ){

                let optionIcon = document.createElement('option');
                optionIcon.value = targetRow['id'];
                optionIcon.text = targetRow['title']['rendered'];
                figureModal.appendChild(optionIcon);
            }
        });
        let figureTab = document.getElementsByName("figure_tab")[0];
        figureTab.value ="";
        figureTab.innerHTML ="";
        let optionTab = document.createElement('option');
        optionTab.text = "Tabs";
        optionTab.value = "";
        figureTab.add(optionTab);
    })
    .catch((err) => {console.error(err)});
}

/**
 * Fetches and updates the available tabs based on the selected icon.
 * Populates the "figure_tab" select field with tabs associated with the chosen icon.
 *
 * @async
 * @function figureIconChange
 * @throws {Error} Throws an error if there's a network problem fetching data.
 * @listens change
 * @example
 * // When the user changes the icon selection:
 * document.getElementsByName("figure_modal")[0].addEventListener('change', figureIconChange);
 */
function figureIconChange(){

    const figureModal = document.getElementsByName("figure_modal")[0].value;
    
    const protocol = window.location.protocol;
    const host = window.location.host;

    const restModal = protocol + "//" + host  + "/wp-json/wp/v2/modal/" + figureModal;

    fetch(restModal)
        .then(response => response.json())
        .then(data => {

            let figureTab = document.getElementsByName("figure_tab")[0];
            figureTab.value ="";
            figureTab.innerHTML ="";
            let optionTab = document.createElement('option');
            optionTab.text = "Tabs";
            optionTab.value = "";
            figureTab.add(optionTab);
        
            if (figureModal != ""){
                let targetField ="";
                for (let i = 1; i < 7; i++){
                    targetField = "modal_tab_title" + i;
                    if (data[targetField]!= ""){
                        let optionTitleTab = document.createElement('option');
                        optionTitleTab.text = data[targetField];
                        optionTitleTab.value = data[targetField];
                        figureTab.appendChild(optionTitleTab);
                    }
                }
            }

        })
        .catch((err) => {console.error(err)});
}

/**
 * Displays the correct fields for the selected figure type (Internal, External, Interactive, Code).
 * Hides or shows fields based on the value of the "figure_path" field.
 * This function manages the visibility of various field groups to ensure that only relevant fields are shown.
 * @function displayCorrectImageField
 * @listens change
 * @example
 * //When the figure type selection is changed:
 * document.getElementsByName("figure_path")[0].addEventListener('change', displayCorrectImageField);
 */
function displayCorrectImageField () {
    const imageType = document.getElementsByName("figure_path")[0].value;
    // Select the container with data-depend-id="figure_image"
    let figureImageContainer = document.querySelector('[data-depend-id="figure_image"]');

    // Select the nested container with class "exopite-sof-image-preview"
    let imagePreviewContainer = figureImageContainer.querySelector('.exopite-sof-image-preview');
    // Select the img tag within the class "exopite-sof-image-preview"
    let imagePreviewImg = imagePreviewContainer.querySelector('img');

    let figureJsonContainer = document.querySelector('[data-depend-id="figure_json"]');

    // Select the nested container with class "exopite-sof-image-preview"
    let jsonPreviewContainer = figureJsonContainer.querySelector('.exopite-sof-image-preview');
    // Select the img tag within the class "exopite-sof-image-preview"
    let jsonPreviewImg = jsonPreviewContainer.querySelector('img');

    // Select the nested container with class "exopite-sof-field-upload"
    let codeContainer= document.querySelector('.exopite-sof-field-ace_editor');

    // Select the nested container with class ".exopite-sof-btn.figure_preview"
    let figurePreviewElement = document.querySelector('.exopite-sof-btn.figure_preview'); // Add an ID or a unique class
    
    // Select the nested container with class ".exopite-sof-btn.code_preview"
    let codePreviewElement = document.querySelector('.exopite-sof-btn.code_preview'); // Add an ID or a unique class

    switch (imageType) {
        case "Internal":
            //Show the fields we want to see
            document.getElementsByName("figure_image")[0].parentElement.parentElement.parentElement.style.display = "block";
            //Choose the preview field we want to see
            if (codePreviewElement) {
                codePreviewElement.parentElement.parentElement.style.display = "none"; // Hide the element
            }
            if (figurePreviewElement) {
                figurePreviewElement.parentElement.parentElement.style.display = "block"; // Show the element
            }
            //Hide the fields we do not want to see
            document.getElementsByName("figure_json")[0].parentElement.parentElement.parentElement.style.display = "none";
            document.getElementsByName("figure_json")[0].value = "";
            document.getElementsByName("figure_external_alt")[0].parentElement.parentElement.style.display = "none";
            document.getElementsByName("figure_external_alt")[0].value = "";
            document.getElementsByName("figure_external_url")[0].parentElement.parentElement.style.display = "none";
            document.getElementsByName("figure_external_url")[0].value = "";
            document.getElementsByName("figure_json_arguments")[0].parentElement.parentElement.style.display = "none";
            document.getElementsByName("figure_json_arguments")[0].value = "";
            document.getElementsByName("figure_json")[0].parentElement.parentElement.style.display = "none";
            document.getElementsByName("figure_json")[0].value = "";
            document.getElementsByName("figure_temp_filepath")[0].parentElement.parentElement.style.display = "none";
            document.querySelector('.figure_temp_javascript').parentElement.parentElement.style.display = "none";

            codeContainer.style.display = "none";
            break;

        case "External":
            //Show the fields we want to see
            document.getElementsByName("figure_external_alt")[0].parentElement.parentElement.style.display = "block";
            document.getElementsByName("figure_external_url")[0].parentElement.parentElement.style.display = "block";
            //Choose the preview field we want to see
            if (figurePreviewElement) {
                figurePreviewElement.parentElement.parentElement.style.display = "block"; // Show the element
            }
            if (codePreviewElement) {
                codePreviewElement.parentElement.parentElement.style.display = "none"; // Hide the element
            } 
            //Hide the fields we do not want to see
            document.getElementsByName("figure_json")[0].parentElement.parentElement.parentElement.style.display = "none";
            document.getElementsByName("figure_json")[0].value = "";
            document.getElementsByName("figure_image")[0].parentElement.parentElement.parentElement.style.display = "none";
            document.getElementsByName("figure_image")[0].value = "";
            document.getElementsByName("figure_json_arguments")[0].parentElement.parentElement.style.display = "none";
            document.getElementsByName("figure_json_arguments")[0].value = "";
            document.getElementsByName("figure_temp_filepath")[0].parentElement.parentElement.style.display = "none";
            document.querySelector('.figure_temp_javascript').parentElement.parentElement.style.display = "none";
            codeContainer.style.display = "none";
            break;               

        case "Interactive":
            //Hide the fields we do not want to see and show the fields we want to see
            codeContainer.style.display = "none";
            document.getElementsByName("figure_external_alt")[0].parentElement.parentElement.style.display = "none";
            document.getElementsByName("figure_external_alt")[0].value = "";
            document.getElementsByName("figure_external_url")[0].parentElement.parentElement.style.display = "none";
            document.getElementsByName("figure_external_url")[0].value = "";
            document.getElementsByName("figure_json")[0].parentElement.parentElement.parentElement.style.display = "none";
            document.getElementsByName("figure_json")[0].value = "";
            document.getElementsByName("figure_image")[0].parentElement.parentElement.parentElement.style.display = "none";
            document.getElementsByName("figure_image")[0].value = "";
            document.getElementsByName("figure_json_arguments")[0].parentElement.parentElement.style.display = "none";
            document.getElementsByName("figure_json_arguments")[0].value = "";
            document.getElementsByName("figure_temp_filepath")[0].parentElement.parentElement.style.display = "block";
            document.querySelector('.figure_temp_javascript').parentElement.parentElement.style.display = "block";
            break;

        case "Code":
            //Show the fields we want to see
            if (codeContainer) {
                codeContainer.style.display = "block";
            }

            //Hide the fields we do not want to see
            document.getElementsByName("figure_json")[0].parentElement.parentElement.parentElement.style.display = "none";
            document.getElementsByName("figure_image")[0].parentElement.parentElement.parentElement.style.display = "none";
            document.getElementsByName("figure_external_url")[0].parentElement.parentElement.style.display = "none";
            document.getElementsByName("figure_external_alt")[0].parentElement.parentElement.style.display = "none";
            document.getElementsByName("figure_json_arguments")[0].parentElement.parentElement.style.display = "none"; 
            document.getElementsByName("figure_temp_filepath")[0].parentElement.parentElement.style.display = "none";
            document.querySelector('.figure_temp_javascript').parentElement.parentElement.style.display = "none"; 
            break;
    } 
}

/**
 * Hides the parent element containing "figure_interactive_arguments" field on load.
 * This field is intended to hold JSON data and is typically not meant for direct
 * user interaction.
 */
document.getElementsByName("figure_interactive_arguments")[0].parentElement.parentElement.style.display="none";

/**
 * Run the displayCorrectImageField function when the page loads
 */
displayCorrectImageField ();

/**
 * Attach functions to elements on the page 
 */
document.getElementsByName("figure_path")[0].addEventListener('change', displayCorrectImageField);
document.getElementsByName("figure_modal")[0].addEventListener('change', figureIconChange);
document.getElementsByName("figure_scene")[0].addEventListener('change', figureSceneChange);
document.getElementsByName("location")[0].addEventListener('change', figureInstanceChange);
document.querySelector('[data-depend-id="figure_temp_javascript"]').addEventListener('click', loadJson);

/**
 * Loads JSON data from the specified file and populates the form fields
 * for creating an interactive plot. This function fetches the JSON data,
 * extracts the column names, and dynamically creates a form for users to
 * configure the plot parameters.
 *
 * @async
 * @function loadJson
 * @throws {Error} Throws an error if the network response is not OK or if there is an issue parsing the JSON data.
 * @listens click
 * @example
 * // When the user clicks on the "Run" button:
 * document.querySelector('[data-depend-id="figure_temp_javascript"]').addEventListener('click', loadJson);
 */
async function loadJson() {
    const rootURL = window.location.origin;
    const restOfURL = document.getElementsByName("figure_temp_filepath")[0].value;
    const finalURL = rootURL + restOfURL;
    try {
        const response = await fetch(finalURL);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();

        jsonColumns = Object.fromEntries(
            Object.keys(data.data).map((key, index) => [index, key])); 

        const lengthJsonColumns = (Object.entries(jsonColumns).length);
        if (lengthJsonColumns > 1){
            var graphGUI = document.getElementById('graphGUI');
            if (graphGUI) {
                // Remove the scene window
                graphGUI.parentNode.removeChild(graphGUI);
            }
            const targetElement = document.querySelector('.figure_temp_javascript').parentElement.parentElement;
            let newDiv = document.createElement('div');
            newDiv.id = "graphGUI";
            newDiv.classList.add("container", "graphGUI");

            let labelGraphType = document.createElement("label");
            labelGraphType.for = "graphType";
            labelGraphType.innerHTML = "Graph Type";
            let selectGraphType = document.createElement("select");
            selectGraphType.id = "graphType";
            selectGraphType.name = "plotFields";
            let graphType1 = document.createElement("option");
            graphType1.value = "None";
            graphType1.innerHTML = "None";
            let graphType2 = document.createElement("option");
            graphType2.value = "Plotly bar graph";
            graphType2.innerHTML = "Plotly bar graph";
            let graphType3 = document.createElement("option");
            graphType3.value = "Plotly line graph (time series)";
            graphType3.innerHTML = "Plotly line graph (time series)"; 
            selectGraphType.appendChild(graphType1);
            selectGraphType.appendChild(graphType2);    
            selectGraphType.appendChild(graphType3);   

            fieldValueSaved = fillFormFieldValues(selectGraphType.id);
            if (fieldValueSaved != undefined){
                selectGraphType.value = fieldValueSaved;
            }
            
            selectGraphType.addEventListener('change', function() {
                secondaryGraphFields(this.value);
            });
            selectGraphType.addEventListener('change', function() {
                logFormFieldValues();
            });

            let newRow = document.createElement("div");
            newRow.classList.add("row", "fieldPadding");
            let newColumn1 = document.createElement("div");
            newColumn1.classList.add("col-3");   
            let newColumn2 = document.createElement("div");
            newColumn2.classList.add("col");

            newColumn1.appendChild(labelGraphType);
            newColumn2.appendChild(selectGraphType);
            newRow.append(newColumn1, newColumn2);
            newDiv.append(newRow);

            targetElement.appendChild(newDiv);
            if (fieldValueSaved != undefined){
                secondaryGraphFields(selectGraphType.value);
            }
        }
    } catch (error) {
        console.error('Error loading JSON:', error);
    }
}

/**
 * Clears previous graph fields and creates the appropriate form fields, based on the selected graph type.
 *
 * @function secondaryGraphFields
 * @param {string} graphType - The selected graph type ("None", "Plotly bar graph", or "Plotly line graph (time series)").
 * @listens change
 * @example
 * // when the user changes the graph type:
 * document.getElementById("graphType").addEventListener('change', secondaryGraphFields);
 */
function secondaryGraphFields(graphType){

    var secondaryGraphDiv = document.getElementById('secondaryGraphFields');
    // If the element exists
    if (secondaryGraphDiv) {
        // Remove the scene window
        secondaryGraphDiv.parentNode.removeChild(secondaryGraphDiv);
    }

    switch(graphType){
        case "None":
            clearPreviousGraphFields();
            break;
        case "Plotly bar graph":
            clearPreviousGraphFields();
            break;
        case "Plotly line graph (time series)":
            clearPreviousGraphFields();
            plotlyLineParameterFields(jsonColumns);
            break;
    }
}

/**
 * Clears out, if they exist, the prior form fields used for indicating figure preferences
 * @function clearPreviousGraphFields
 */
function clearPreviousGraphFields (){
    let assignColumnsToPlot = document.getElementById('assignColumnsToPlot');
    // If the element exists
    if (assignColumnsToPlot) {
        // Remove the scene window
        assignColumnsToPlot.parentNode.removeChild(assignColumnsToPlot);
    }
}

/**
 * Attaches a function to the the figure preview button. This function creates a preview of the figure below the figure preview button.
 * @function clearPreviousGraphFields
 */
document.querySelector('[data-depend-id="figure_preview"]').addEventListener('click', function() {
    // Let's remove the preview window if it already exists
    var previewWindow = document.getElementById('preview_window');
    // If the element exists
    if (previewWindow) {
        // Remove the scene window
        previewWindow.parentNode.removeChild(previewWindow);
    }

    // Find element
    const firstFigurePreview = document.querySelector('.figure_preview');

    // Find the second parent element
    const secondParent = firstFigurePreview.parentElement.parentElement;

    // Create a new div element
    let newDiv = document.createElement('div');
    newDiv.id = "preview_window";
    newDiv.classList.add("container", "figure_preview");

    const scienceUrl = document.getElementsByName("figure_science_info[figure_science_link_url]")[0].value;
    const dataUrl = document.getElementsByName("figure_data_info[figure_data_link_url]")[0].value;

    if (scienceUrl !="" || dataUrl != ""){
        let firstRow = document.createElement("div");
        firstRow.classList.add("grayFigureRow");

        if (scienceUrl !=""){
            let scienceA = document.createElement("a");
            scienceA.classList.add("grayFigureRowLinks");
            scienceA.href = document.getElementsByName("figure_science_info[figure_science_link_url]")[0].value;
            scienceA.target="_blank";
            let dataIcon = document.createElement("i");
            dataIcon.classList.add("fa-solid", "fa-clipboard-list", "grayFigureRowIcon");
            let urlText = document.createElement("span");
            urlText.classList.add("grayFigureRowText");
            urlText.innerHTML = document.getElementsByName("figure_science_info[figure_science_link_text]")[0].value;
            scienceA.appendChild(dataIcon);
            scienceA.appendChild(urlText);
            firstRow.appendChild(scienceA);
        // firstRow.appendChild(urlText);
        }

        if (dataUrl !=""){
            let dataA = document.createElement("a");
            dataA.classList.add("grayFigureRowLinks");//, "grayFigureRowRightLink");
            dataA.href = document.getElementsByName("figure_data_info[figure_data_link_url]")[0].value;
            dataA.target="_blank";
            let dataIcon = document.createElement("i");
            dataIcon.classList.add("fa-solid", "fa-database", "grayFigureRowIcon");
            let urlText = document.createElement("span");
            urlText.classList.add("grayFigureRowText");
            urlText.innerHTML = document.getElementsByName("figure_data_info[figure_data_link_text]")[0].value;
            dataA.appendChild(dataIcon);
            dataA.appendChild(urlText);
            firstRow.appendChild(dataA);
        // firstRow.appendChild(urlText);
        }

        newDiv.appendChild(firstRow);
    } 

    let imageRow = document.createElement("div");
    imageRow.classList.add("imageRow");
    let figureImage = document.createElement("img");
    figureImage.classList.add("figureImage");

    const figurePath = document.getElementsByName("figure_path")[0].value;
    let figureSrc;

    let interactiveImage = false;
    switch(figurePath){
        case "Internal":
            figureSrc = document.getElementsByName("figure_image")[0].value;
            if (figureSrc != ""){
            figureImage.src = figureSrc;
            } else {imageRow.textContent = "No figure image."}
            break;
        case "External":
            figureSrc = document.getElementsByName("figure_external_url")[0].value;
            if (figureSrc != ""){
            figureImage.src = figureSrc;
            } else {imageRow.textContent = "No figure image."}
            break;         
        case "Interactive":
                imageRow.id = "javascript_figure_target"
                interactiveImage = true;
            break;
    }

    const containerWidth = document.querySelector('[data-depend-id="figure_preview"]').parentElement.parentElement.parentElement.clientWidth;

    if (containerWidth < 800){
        figureImage.style.width = (containerWidth-88) + "px";
    }
    imageRow.appendChild(figureImage);
    newDiv.appendChild(imageRow);

    let captionRow = document.createElement("div");
    captionRow.classList.add("captionRow");

    // Get the short caption
    let shortCaption = document.getElementById('figure_caption_short').value;  
    
    // Get the long caption
    let longCaption = document.getElementById('figure_caption_long').value;  

    let shortCaptionElementContent = document.createElement("p");
    shortCaptionElementContent.innerHTML = shortCaption;
    shortCaptionElementContent.classList.add("captionOptions");
    captionRow.appendChild(shortCaptionElementContent);
    let longCaptionElement = document.createElement("details");

    let longCaptionElementSummary = document.createElement("summary");
    longCaptionElementSummary.textContent = "Click here for more details.";
    let longCaptionElementContent = document.createElement("p");
    longCaptionElementContent.classList.add("captionOptions");
    longCaptionElementContent.innerHTML = longCaption;
    longCaptionElement.appendChild(longCaptionElementSummary);
    longCaptionElement.appendChild(longCaptionElementContent);
    captionRow.appendChild(longCaptionElement);
    newDiv.appendChild(captionRow);

    secondParent.appendChild(newDiv);
    if (interactiveImage == true){
        const plotlyTargetElement = "javascript_figure_target";
        const jsonFilePath = document.getElementsByName("figure_temp_filepath")[0].value;
        const rawField = document.getElementsByName("figure_interactive_arguments")[0].value;
        const figureArguments = Object.fromEntries(JSON.parse(rawField));
        producePlotlyLineFigure(plotlyTargetElement, jsonFilePath, figureArguments);
    }
});


// Code for making Run Code button do something
//    const previewCodeButton = document.querySelector(".code_preview");

  //  previewCodeButton.addEventListener("click", displayCode);

function displayCode () {
    // Remove existing preview div if present
    let previewWindow = document.getElementById("code_preview_window");
    if (previewWindow) {
        previewWindow.parentNode.removeChild(previewWindow);
    }

    // Create a new div to display the embed code
    const previewDiv = document.createElement("div");
    previewDiv.id = "code_preview_window";
    previewDiv.style.width = "100%";
    previewDiv.style.minHeight = "300px";
    previewDiv.style.padding = "10px";
    previewDiv.style.backgroundColor = "#ffffff";
    previewDiv.style.overflow = "auto";
    // Center the content using Flexbox
    previewDiv.style.display = "flex";
    previewDiv.style.justifyContent = "center"; // Centers horizontally
    previewDiv.style.alignItems = "center"; // Centers vertically (if height is greater than content)

    // Get the embed code from the figure_code field
    const embedCode = document.getElementsByName("figure_code")[0]?.value || "No code available. Set the 'Figure Type' to 'Code' and paste your code into the HTML/JavaScript Code Code text area.";

    try {
        // Parse the embed code and extract <script> tags
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = embedCode;

        // Move <script> tags to the head and inject the rest into the preview div
        const scripts = tempDiv.querySelectorAll("script");
        scripts.forEach((script) => {
            const newScript = document.createElement("script");
            newScript.type = script.type || "text/javascript";
            if (script.src) {
                newScript.src = script.src; // External script
            } else {
                newScript.textContent = script.textContent; // Inline script
            }
            document.head.appendChild(newScript); // Add to <head>
            script.remove(); // Remove the script tag from tempDiv
        });

        // Inject remaining HTML into the preview div
        previewDiv.innerHTML = tempDiv.innerHTML;

        // Append the preview div below the button
        previewCodeButton.insertAdjacentElement("afterend", previewDiv);

    } catch (error) {
        console.error("Failed to inject embed code:", error);
        previewDiv.textContent = "Failed to load embed code. Please check your input.";
        previewCodeButton.insertAdjacentElement("afterend", previewDiv);
    }
}














