// These functions only fire upon editing or creating a post of Instance custom content type

'use strict';

// Makes title text red if it ends with an asterisk in "exopite-sof-title" elements. Also adds a line giving the meaning of red text at top of form.
document.addEventListener('DOMContentLoaded', redText);

displayLegacyContentField();

// should the "legacy content url" field be shown?
function displayLegacyContentField () {
    const legacyContent = document.getElementsByName("instance_legacy_content")[0].value;
    if (legacyContent == "no"){
        document.getElementsByName("instance_legacy_content_url")[0].parentElement.parentElement.style.display = "none";
        document.getElementsByName("instance_legacy_content_url")[0].value = "";          
    } else {
        document.getElementsByName("instance_legacy_content_url")[0].parentElement.parentElement.style.display = "block";
    }
}

document.querySelector('select[name="instance_legacy_content"]').addEventListener("change", displayLegacyContentField );
    