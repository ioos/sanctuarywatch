// These functions only fire upon editing or creating a post of Scene custom content type
(function( $ ) {
    'use strict';

    displayCorrectImageField ();

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
})( jQuery );