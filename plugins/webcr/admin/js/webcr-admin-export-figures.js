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

    function generateFigureOptions(){

        const instanceID = document.getElementById("location").value;

        if (instanceID != "") {
            const protocol = window.location.protocol;
            const host = window.location.host;
            const restURL = protocol + "//" + host  + "/wp-json/wp/v2/scene?_fields=title,id,scene_location&orderby=title&order=asc&scene_location=" + instanceID;
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
                console.log (sceneArray);
            })
            .catch(error => console.error('Error fetching data:', error));
        }
    }


})( jQuery );