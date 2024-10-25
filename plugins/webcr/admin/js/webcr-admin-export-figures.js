// These functions are used within the context of the Export Figures Tool
(function( $ ) {
    'use strict';
    const testButton = document.getElementById("testbutton");
    testButton.addEventListener("click", downloadRTF);
    
    const instanceButton = document.getElementById("chooseInstance");
    instanceButton.addEventListener("click", generateFigureOptions);

    // first pass at downloading a RTF-formatted file
    function downloadRTF() {
        // Define dummy text for the paragraphs
        const paragraph1 = "This is the first dummy paragraph of text.";
        const paragraph2 = "This is the second dummy paragraph of text.";

        // Create RTF content
        const rtfContent = '{\\rtf1\\ansi\\deff0' +
            '{\\fonttbl{\\f0 Arial;}}' +
            '\\f0\\fs24' +
            '\\pard\\li0\\fi0\\ql' + paragraph1 + '\\par' +
            '\\pard\\li0\\fi0\\ql' + paragraph2 + '\\par' +
        '}';

        // Create a Blob with the RTF content
        const blob = new Blob([rtfContent], { type: "application/rtf" });

        // Create a link element and trigger the download
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "dummy-text.rtf";
        link.click();
    }

    async function generateFigureOptions() {

        // Get the integer value of "Location" select element
        const instanceID = document.getElementById("location").value;
    
        // If Location select element is not blank, execute code
        if (instanceID !== "") {
    
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
    
                // Loop through every row of jsonData
                for (const element of jsonData) {
                    const sceneID = element["id"];
                    const sceneTitle = element["title"]["rendered"];
                    const sceneHeader = document.createElement("h6");
                    sceneHeader.innerHTML = `Scene: ${sceneTitle}`;
    
                    // Create the modal API call
                    const restModalURL = `${protocol}//${host}/wp-json/wp/v2/modal?_fields=id,title,modal_scene,icon_function&orderby=title&order=asc&modal_scene=${sceneID}&icon_function=modal`;
    
                    // Fetch the modal data
                    const modalResponse = await fetch(restModalURL);
                    const jsonModalData = await modalResponse.json();
    
                    // Loop through every row of jsonModalData
                    for (const modalElement of jsonModalData) {
                        const modalTitle = modalElement["title"]["rendered"];
                        const modalHeader = document.createElement("p");
                        modalHeader.innerHTML = `&nbsp;&nbsp;&nbsp;&nbsp;Modal: ${modalTitle}`;
    
                        // Append the Modal title to the target div tag
                        sceneHeader.appendChild(modalHeader);
                    }
    
                    // Append the Scene title to the target div tag
                    divCanvas.appendChild(sceneHeader);
                }
    
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }
    }


})( jQuery );