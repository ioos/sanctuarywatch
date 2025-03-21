
'use strict';

// The following code changes the number of visible about boxes depending on the value of "Number of About Boxes"
displayAboutBoxes();

function displayAboutBoxes (){
    const numAboutBoxes = document.getElementsByName("numberAboutBoxes")[0].value;
    let target_element = "";
    for (let i = 10; i > numAboutBoxes; i--){
        target_element =  "aboutBoxMain" + i;
        document.getElementById(target_element).closest('.exopite-sof-field-fieldset').style.display="none";
    }

    for (let i = 1; i <= numAboutBoxes; i++){
        target_element =  "aboutBoxMain" + i;
        document.getElementById(target_element).closest('.exopite-sof-field-fieldset').style.display="block";
    }
}

let aboutInfoRangeElement = document.querySelector(".range[data-depend-id='numberAboutBoxes']");
aboutInfoRangeElement.addEventListener("change", displayAboutBoxes );
