// These functions only fire upon editing or creating a post of Scene custom content type
(function( $ ) {
    'use strict';

    displayCorrectImageField ();

    function figureInstanceChange(){
        console.log("hello");
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

    // reset icons on scene change
    function figureSceneChange(){
        const protocol = window.location.protocol;
        const host = window.location.host;
        const figureScene = document.getElementsByName("figure_scene")[0].value;

        //FIX: the REST API for modal is retrieving all records even when icon_function and modal_scene are set for some reason
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

    // reset tabs on icon change
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


    // Should the image be an external URL or an internal URL? Show the relevant fields either way
    function displayCorrectImageField () {
        const imageType = document.getElementsByName("figure_path")[0].value;
        // Select the container with data-depend-id="figure_image"
        let figureImageContainer = document.querySelector('[data-depend-id="figure_image"]');

        // Select the nested container with class "exopite-sof-image-preview"
        let imagePreviewContainer = figureImageContainer.querySelector('.exopite-sof-image-preview');
        // Select the img tag within the class "exopite-sof-image-preview"
        let imagePreviewImg = imagePreviewContainer.querySelector('img');

        imagePreviewImg.src
        'http://nov9.local/wp-admin/post-new.php?post_type=figure'
        imagePreviewImg.src
        'http://nov9.local/wp-content/uploads/2023/11/Svg_example3.svg'

        switch (imageType) {
            case "Internal":
                document.getElementsByName("figure_image")[0].parentElement.parentElement.parentElement.style.display = "block";
                document.getElementsByName("figure_external_url")[0].parentElement.parentElement.style.display = "none";
                document.getElementsByName("figure_external_url")[0].value = "";
                // Add the "hidden" class to the nested container
                if (imagePreviewImg.src.includes("uploads")) {
                    imagePreviewContainer.classList.remove('hidden');
                }
                break;
            case "External":
                document.getElementsByName("figure_image")[0].parentElement.parentElement.parentElement.style.display = "none";
                document.getElementsByName("figure_image")[0].value = "";

                // Add the "hidden" class to the nested container
                imagePreviewContainer.classList.add('hidden');
                imagePreviewImg.src ="";

                document.getElementsByName("figure_external_url")[0].parentElement.parentElement.style.display = "block";
                break;            
        } 
    }

    $( "select[name='figure_path']" ).change(displayCorrectImageField);
    $( "select[name='figure_modal']" ).change(figureIconChange);
    $( "select[name='figure_scene']" ).change(figureSceneChange);
    $( "select[name='location']" ).change(figureInstanceChange);

    
})( jQuery );