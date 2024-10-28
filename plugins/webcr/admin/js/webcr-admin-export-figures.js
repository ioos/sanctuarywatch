// These functions are used within the context of the Export Figures Tool
(function( $ ) {
    'use strict';
    
    const instanceButton = document.getElementById("chooseInstance");
    instanceButton.addEventListener("click", generateFigureOptions);


    function downloadFile (){
        fileType = document.querySelector('input[name="exportFormat"]:checked').value;

        if (fileType == "document"){
            downloadRTF();
        } else {
            downloadPPTX();
        }
 
    }

    function selectAll () {
        const checkBoxStatus = document.getElementById("masterCheckBox").checked;

        // Select all checkboxes associated with Figures
        const checkboxes = document.querySelectorAll('[name^="figure"]');
        // Loop through each checkbox and check it (or uncheck it)
        checkboxes.forEach((checkbox) => {
            checkbox.checked = checkBoxStatus; 
            checkbox.disabled = checkBoxStatus;
        });
    }

    function getFormattedDate() {
        const date = new Date();
        
        // Define an array with month names
        const months = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"];
    
        // Extract the month, day, and year
        const month = months[date.getMonth()]; // Get month name from array
        const day = date.getDate();
        const year = date.getFullYear();
    
        // Return formatted date string
        return `${month} ${day}, ${year}`;
    }

    async function imageToRtf(imageUrl){
        // Fetch the image and convert to base64
        try {
            const response = await fetch(imageUrl);

            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.statusText}`);
            }

            const contentType = response.headers.get("content-type");
            let imageType;

            switch(contentType){
                case "image/png":
                    imageType = "png";
                    break;
                case "image/jpeg":
                    imageType = "jpeg";
                    break;      
                default:
                    imageType = "other";          
            }

            if (imageType != "other"){
                const blob = await response.blob();
                const arrayBuffer = await blob.arrayBuffer();
                const byteArray = new Uint8Array(arrayBuffer);
                
                // Convert image to hex string as RTF requires hex format
                let hexImage = '';
                for (let i = 0; i < byteArray.length; i++) {
                    hexImage += byteArray[i].toString(16).padStart(2, '0');
                }

                switch(imageType){
                    case "png":
                        return `{\\pict\\pngblip\\picw500\\pich500
                            ${hexImage}}\\par\\par`;
                        break;
                    case "jpeg":
                        imageType = "jpeg";
                        return `{\\pict\\jpegblip\\picw500\\pich500
                            ${hexImage}}\\par\\par`;
                        break;            
                }
            }
        } catch (error) {
                console.error("Error fetching image:", error);
          //      alert("Failed to fetch the image. Please check the console for more details.");
            }
    }

    // first pass at downloading a RTF-formatted file
    async function downloadRTF() {
        const instanceSelect = document.getElementById("location");
        const instanceValue = instanceSelect.value;
        if (instanceValue != ""){
            // Find the option with the specified value
            let instanceValueText = "";
            for (let option of instanceSelect.options) {
                if (option.value === instanceValue) {
                    instanceValueText = option.text; // Get the text associated with this option
                    break;
                }
            }

            let selectedCheckBoxes = [];
            // Select all checkboxes associated with Figures
            const checkboxes = document.querySelectorAll('[name^="figure"]');
            // Loop through each checkbox and check it (or uncheck it)

            checkboxes.forEach((checkbox) => {
                if (checkbox.checked) {
                    selectedCheckBoxes.push(checkbox.value);
                } 
            });

            if (selectedCheckBoxes.length > 0) {
                const currentDate = getFormattedDate();
                const sentence1 = "Selected figures for " + instanceValueText + ", " + currentDate + ".";

                // Create RTF content
                let rtfContent = '{\\rtf1\\ansi\\ansicpg1252\\deff0' +
                    '{\\fonttbl{\\f0 Arial;}}' +
                    '\\f0' +
                    '\\fs36' + sentence1 + '\\par\\par';

                let targetFields;
                let titleRow;
                const protocol = window.location.protocol;
                const host = window.location.host;
                let figureCaptionShort;
                let figureCaptionLong;
                let figureLocation;
                let figureImageURL;

                for (const targetCheckbox of selectedCheckBoxes) {
                    targetFields = targetCheckbox.split(";");
                    titleRow = targetFields[1] + " (Scene: " + targetFields[2] + ", Modal: " + targetFields[3] + ")";
                    rtfContent = rtfContent + '\\fs32\\pard\\par ' + titleRow + '\\par\\par';
                    const restFigureURL = `${protocol}//${host}/wp-json/wp/v2/figure?_fields=id,title,figure_path,figure_image,figure_external_url,figure_caption_short,figure_caption_long&id=${targetFields[0]}`;
                    const figureResponse = await fetch(restFigureURL);
                    const figureData = await figureResponse.json();

                    figureCaptionShort = htmlToRtfText(figureData[0]["figure_caption_short"]);
                    figureCaptionLong = htmlToRtfText(figureData[0]["figure_caption_long"]);
                    figureLocation = figureData[0]["figure_path"];

                    switch(figureLocation){
                        case "Internal":
                            figureImageURL = figureData[0]["figure_image"];
                            rtfContent = rtfContent + await imageToRtf(figureImageURL);
                            break;
                        case "External":
                            figureImageURL = figureData[0]["figure_external_url"];
                            rtfContent = rtfContent + await imageToRtf(figureImageURL);
                            break;
                        case "Interactive":
                            break;
                    }                      

                    rtfContent = rtfContent + '\\fs28 Short Caption: ' + figureCaptionShort + '\\par\\par';
                    rtfContent = rtfContent + '\\fs28 Long Caption: ' + figureCaptionLong + '\\par\\par';
                //    rtfContent = rtfContent + '\\pard\\qc\\fs24 ________________________________\\par}';
                }

                rtfContent = rtfContent + '}';
                // Create a Blob with the RTF content
                const blob = new Blob([rtfContent], { type: "application/rtf" });

                // Create a link element and trigger the download
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = "figure-export.rtf";
                link.click();
            }
        }
    }

    function htmlToRtfText(transformText){
        if (transformText == "" || transformText == null){
            transformText = "None";
        }
        // remove <span> tags
        transformText = transformText.replace(/<\/?span[^>]*>/g, "");

        // Replace opening <em> tag with RTF italic start tag \i
        transformText = transformText.replace(/<em>/g, '\\i ');

        // Replace closing </em> tag with RTF italic end tag \i0
        transformText = transformText.replace(/<\/em>/g, '\\i0 ');

        // Replace the opening <a href="URL"> tag with RTF hyperlink start format
        transformText = transformText.replace(/<a href="(.*?)">/g, '{\\field{\\*\\fldinst HYPERLINK "$1"}{\\fldrslt ');

        // Replace the closing </a> tag with RTF hyperlink end format
        transformText = transformText.replace(/<\/a>/g, '}}');

        return transformText;
    }
    async function generateFigureOptions() {

        // Get the integer value of "Location" select element
        const instanceID = document.getElementById("location").value;
    
        // If Location select element is not blank, execute code
        if (instanceID !== "") {
    
           // document.getElementsByName("submit")[0].style.display = "block";

            // Empty the target div tag of any existing content
            const divCanvas = document.getElementById("optionCanvas");
            divCanvas.innerHTML = "";
    
            // Create the target API call to call the WordPress database for information from the Scene custom content type
            const protocol = window.location.protocol;
            const host = window.location.host;
            const restSceneURL = `${protocol}//${host}/wp-json/wp/v2/scene?_fields=title,id,scene_location&orderby=title&order=asc&scene_location=${instanceID}`;
    
            try {
                // Fetch the scene data
                const sceneResponse = await fetch(restSceneURL);
                const jsonData = await sceneResponse.json();
    
                const masterCheckBox = document.createElement("input");
                masterCheckBox.type = "checkbox";
                masterCheckBox.id = "masterCheckBox";
                masterCheckBox.classList.add("masterCheckBox");
                masterCheckBox.name = "masterCheckBox";
                masterCheckBox.addEventListener("click", selectAll);
                const masterCheckBoxLabel = document.createElement("label");
                masterCheckBoxLabel.setAttribute("for", "masterCheckBox");
                masterCheckBoxLabel.innerHTML = "Select all figures";
                //sceneHeader.appendChild(document.createElement("p"));
                divCanvas.appendChild(masterCheckBox);
                divCanvas.appendChild(masterCheckBoxLabel);
                divCanvas.appendChild(document.createElement("hr"));
                // Loop through every row of jsonData
                for (const element of jsonData) {
                    const sceneID = element["id"];
                    const sceneTitle = element["title"]["rendered"];
                    const sceneHeader = document.createElement("div");
                    sceneHeader.innerHTML = `Scene: ${sceneTitle}`;
                    sceneHeader.classList.add("sceneHeader");
    
                    // Create the modal API call
                    const restModalURL = `${protocol}//${host}/wp-json/wp/v2/modal?_fields=id,title,modal_scene,icon_function&orderby=title&order=asc&modal_scene=${sceneID}&icon_function=modal`;

                    // Fetch the modal data
                    const modalResponse = await fetch(restModalURL);
                    const jsonModalData = await modalResponse.json();
    
                    // Loop through every row of jsonModalData
                    for (const modalElement of jsonModalData) {
                        const modalTitle = modalElement["title"]["rendered"];
                        const modalID = modalElement["id"];
                        const modalHeader = document.createElement("div");
                        modalHeader.innerHTML = `Modal: ${modalTitle}`;
                        modalHeader.classList.add("modalHeader");
                        // Create the modal API call
                        const restFigureURL = `${protocol}//${host}/wp-json/wp/v2/figure?_fields=id,title,figure_modal&orderby=title&order=asc&figure_modal=${modalID}`;

                        // Fetch the modal data
                        const figureResponse = await fetch(restFigureURL);
                        const jsonFigureData = await figureResponse.json();

                        // Loop through every row of jsonModalData
                        for (const figureElement of jsonFigureData) {
                            const figureTitle = figureElement["title"]["rendered"];
                           // console.log(figureTitle);
                            const figureID = figureElement["id"];
                            const figureCheckBox = document.createElement("input");
                            figureCheckBox.type = "checkbox";
                            figureCheckBox.id = figureID;
                            figureCheckBox.name = "figure" + figureID;
                            figureCheckBox.value = figureID + ";" + figureTitle + ";" + sceneTitle + ";" + modalTitle ;
                            const checkBoxLabel = document.createElement("label");
                            checkBoxLabel.setAttribute("for", figureID.toString());
                            checkBoxLabel.innerHTML = `${figureTitle}`;
                            checkBoxLabel.classList.add("checkBoxLabel");

                            const figureCheckBoxContainer = document.createElement("div");
                            figureCheckBoxContainer.classList.add("figureCheckBox");
                            figureCheckBoxContainer.appendChild(figureCheckBox);
                            figureCheckBoxContainer.appendChild(checkBoxLabel);
                            modalHeader.appendChild(figureCheckBoxContainer);
                        }
                        // Append the Modal title to the target div tag
                        sceneHeader.appendChild(modalHeader);
                    }
    
                    // Append the Scene title to the target div tag
                    divCanvas.appendChild(sceneHeader);
                }
    
                divCanvas.appendChild(document.createElement("hr")); 
                const radioGroupDescription = document.createElement("div")
                radioGroupDescription.classList.add("RadioGroup");
                radioGroupDescription.innerHTML = "Select export format:";
                divCanvas.appendChild(radioGroupDescription);                  
                const radioDocument = document.createElement("input");
                radioDocument.type = "radio";
                radioDocument.id = "document";
                radioDocument.name = "exportFormat";
                radioDocument.value = "document";
                radioDocument.checked= true;
                divCanvas.appendChild(radioDocument);     
                const radioDocumentLabel = document.createElement("label");
                radioDocumentLabel.classList.add("radioFormat");                    
                radioDocumentLabel.innerHTML = "Document";
                radioDocumentLabel.setAttribute("for", "document");
                divCanvas.appendChild(radioDocumentLabel);                      
                const radioSlide = document.createElement("input");
                radioSlide.type = "radio";
                radioSlide.id = "slide";
                radioSlide.name = "exportFormat";
                radioSlide.value = "slide";
                divCanvas.appendChild(radioSlide);   
                const radioSlideLabel = document.createElement("label");
                radioSlideLabel.classList.add("radioFormat");                    
                radioSlideLabel.innerHTML = "Slide";
                radioSlideLabel.setAttribute("for", "slide");
                divCanvas.appendChild(radioSlideLabel);     
                const submitButton = document.createElement("input");
                submitButton.name = "submit";
                submitButton.id = "submit";
                submitButton.type = "submit";
                submitButton.classList.add("button", "button-primary");
                submitButton.value = "Export Figures";
                submitButton.addEventListener("click", downloadFile); // downloadRTF);
                divCanvas.appendChild(document.createElement("p"));
                divCanvas.appendChild(submitButton);
              //  <input type="submit" name="submit" id="submit" class="button button-primary" value="Export Figures" style="display: block;"></input>

            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }
    }

})( jQuery );