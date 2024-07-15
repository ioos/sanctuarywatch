console.log("THIS IS A TEST");
// console.log(child_ids);
// access echoed JSON here for use. 
//get all links from single-scene.php
let child_obj = JSON.parse(JSON.stringify(child_ids));
console.log(child_obj);
// console.log("this is the post id: ", post_id);//prob dont need this
let url1 =(JSON.stringify(svg_url));
url = url1.substring(2, url1.length - 2);
// console.log(url1)

//lol
// document.getElementById("svg1").innerHTML =`<img src="${url}" alt="">`;
viewBoxObj = {
    // 'key-climate-ocean': '-25 20 160 160',
    // 'key_drivers_and_pressures': '-25 20 160 160',
    // 'key-human-activities': '370 20 160 160',
    // 'mobile-inverts': '210 485 120 20',
    // 'infauna': '640 355 60 60',
    // 'deep-seafloor-seastars': '99 500 47 40',
    // 'ca-sheephead': '400 130 100 100',
    // 'demersal-fishes': '320 220 200 200',
    // 'biogenic-inverts': '380 460 130 130' //or 360 430 160 160

};

function make_title(){
    let title = '';
    for (let key in child_obj){
        title = child_obj[key]['scene']['post_title'];
        break;
    }
    let titleDom = document.querySelector("body > h1");
    titleDom.innerHTML = title;
}
let mobileBool = false;
//helper function for creating mobile grid for loadSVG:
function mobile_helper(svgElement, iconsArr){
    let defs = svgElement.firstElementChild;
    console.log(defs);
    //just some checks to make sure the variables are right
    // console.log(iconsArr[1].id);
    // console.log("length of arr is: ");
    // console.log(iconsArr.length);
    let numRows = Math.ceil((iconsArr.length/3));
    // console.log("num of rows in grid:");
    // console.log(numRows);
    //this is the outer fluid container that will hold all the rows/columns
    let outer_cont = document.querySelector("body > div.container-fluid");
    outer_cont.innerHTML = '';

    let idx = 0;
    for (let i = 0; i < numRows; i++){
        //each row has 3 columns, so number of rows is ceiling of number of icons/3
        let row_cont = document.createElement("div");
        row_cont.classList.add("row");
        row_cont.setAttribute("id", `row-${i}`);

        for (let j = 0; j < 3; j++){
            if (idx < iconsArr.length){
                //3 columns/row no matter what
                let cont = document.createElement("div");
                cont.classList.add("col-4");
                // let svgClone = svgElement.cloneNode(true);
                let svgClone = document.createElementNS("http://www.w3.org/2000/svg", "svg");
                svgClone.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
                svgClone.setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
                console.log(svgClone);
                // svgClone.setAttributeNS("")
                cont.appendChild(svgClone);
                    // svgElement.removeChild("cls-3");
                let currIcon = iconsArr[idx].id;
                let key = svgElement.querySelector(`#${currIcon}`).cloneNode(true);
                console.log(key);
                cont.setAttribute("id", `${currIcon}-container`);

                svgClone.append(defs);
                svgClone.append(key);
                
                let caption = document.createElement("div");
                if (child_obj[currIcon]){
                    caption.innerText = child_obj[currIcon].title;
                } else {
                    caption.innerText = "not in wp yet, have to add";
                    
                }
                
                caption.setAttribute("style", "font-size: 15px")
                cont.appendChild(caption);
                row_cont.appendChild(cont);
                setTimeout(() => {
                    let bbox = key.getBBox(); //toggle -- key.firstElementChild
                    svgClone.setAttribute('viewBox', `${bbox.x} ${bbox.y} ${bbox.width} ${bbox.height}`);
                }, 0);

                idx+=1;
            } else{
                
                continue;
            }
        }
        outer_cont.appendChild(row_cont);
    }    
   
}

// Below is the function that will be used to include SVGs within each scene
//based on link_svg from infographiq.js

async function loadSVG(url, containerId) {
    try {
        // Step 1: Fetch the SVG content
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const svgText = await response.text();

        // Step 2: Parse the SVG content
        const parser = new DOMParser();
        const svgDoc = parser.parseFromString(svgText, "image/svg+xml");
        const svgElement = svgDoc.documentElement;
        console.log(svgElement);
        svgElement.setAttribute("id", "svg-elem");

        //Append the SVG to the DOM
        const container = document.getElementById(containerId);
        console.log(container);
        // container.appendChild(svgElement);
        // console.log(svgElement);
        // checking if user device is touchscreen
        if (is_touchscreen()){
            // flicker_highlight_icons();
            console.log("touchscreen recognized");
            if (is_mobile() && (deviceDetector.device != 'tablet')){ //a phone and not a tablet; screen will be its own UI here
                console.log("mobile recognized within conditional");
                mobileBool = true;
                const iconsElement = svgElement.getElementById("icons");
                //for mobile: only leave icons, nothing else
                // const parentElement = svgElement.querySelector('g.cls-3');
                // let parentElement = svgElement.querySelector("#g");
                console.log(svgElement.lastElementChild);
                let parentElement = svgElement.lastElementChild;
                    // console.log(Array.from(parentElement.children));
                const children = Array.from(parentElement.children);
                children.forEach(child => {
                    if (child !== iconsElement) {
                            parentElement.removeChild(child);
                    }
                });
                
                let iconsArr = Array.from(iconsElement.children);
                mobile_helper(svgElement, iconsArr);

                add_modal();
                // highlight_icons();
                // flicker_highlight_icons();
                
                
            } else{ //if it gets here, device is a tablet
                container.appendChild(svgElement);
                flicker_highlight_icons();
                toggle_text();
                full_screen_button('svg1');
                table_of_contents();
                add_modal();


            }
        }
        else{ //device is a PC
            container.appendChild(svgElement);
            highlight_icons();
            table_of_contents();
            toggle_text();
            full_screen_button('svg1');
            // table_of_contents();
            add_modal();

            
        }
        // highlight_icons();
        // table_of_contents();
        // add_modal();
        make_title();
        // full_screen_button('svg1');
        // toggle_text();



    } catch (error) {
        console.error('Error fetching or parsing the SVG:', error);
    }
}


//highlight items on mouseover, remove highlight when off; 
//CHANGE HERE FOR TABLET STUFF
function highlight_icons(){
    for (let key in child_obj){
        let elem = document.querySelector('g[id="' + key + '"]');
        console.log(elem);
        elem.addEventListener('mouseover', function(){
            // console.log('mousing over: ', key); 
            elem.style.stroke = "yellow";
            elem.style.strokeWidth = "6";
        });
        elem.addEventListener('mouseout', function(){
            // console.log('mousing out: ', key); 
            elem.style.stroke = "";
            elem.style.strokeWidth = "";
        });
    }  
}
//flicker highlight on and off, for tablets
function flicker_highlight_icons() {
    for (let key in child_obj) {
        let elem = document.querySelector('g[id="' + key + '"]');
        if (elem) {
            // Add transition for smooth fading
            elem.style.transition = 'stroke-opacity 1s ease-in-out';
            
            // Initial state
            elem.style.stroke = "yellow";
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



//check if touchscreen
function is_touchscreen(){
    //check multiple things here: type of device, screen width, 
    return ( 'ontouchstart' in window ) || 
           ( navigator.maxTouchPoints > 0 ) || 
           ( navigator.msMaxTouchPoints > 0 );
    
}

//check operating system
function is_mobile(){
    return (/Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent));
        
}

//helper function from the internet; using it to check if device is a tablet or not. 
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
    accordionButton.classList.add('accordion-button');
    accordionButton.setAttribute("type", "button");
    accordionButton.setAttribute("data-bs-toggle", "collapse");
    accordionButton.setAttribute("data-bs-target", `#${collapseId}`);
    accordionButton.setAttribute("aria-expanded", "true");
    accordionButton.setAttribute("aria-controls", collapseId);
    accordionButton.innerHTML = buttonText;

    // Append Button to Header
    accordionHeader.appendChild(accordionButton);

    // Create Accordion Collapse
    let accordionCollapse = document.createElement('div');
    accordionCollapse.classList.add("accordion-collapse", "collapse", "show");
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

//create tabs here
function create_tabs(iter, tab_id, tab_label, tab_content) {
    let tab_target = `#${tab_id}-pane`;
    let tab_controls = `${tab_id}-pane`;

    let myTab = document.getElementById('myTab');
    let navItem = document.createElement("li");
    navItem.classList.add("nav-item");
    navItem.setAttribute("role", "presentation");
    
    const button = document.createElement('button');
    button.classList.add('nav-link');
    if (iter === 1) {
        button.classList.add('active');
        button.setAttribute('aria-selected', 'true');
    } else {
        button.setAttribute('aria-selected', 'false');
    }
    button.id = `${tab_id}`;
    button.setAttribute('data-bs-toggle', 'tab');
    button.setAttribute('data-bs-target', tab_target);
    button.setAttribute('type', 'button');
    button.setAttribute('role', 'tab');
    button.setAttribute('aria-controls', tab_controls);
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
    tabContentElement.setAttribute('aria-labelledby', tab_id);
    tabContentElement.setAttribute('tabindex', '0');
    tabContentElement.textContent = tab_content;
    tabContentContainer.appendChild(tabContentElement);
}


function render_modal(key){
    let id = child_obj[key]['modal_id'];
    let fetchURL = 'http://sanctuary.local/wp-json/wp/v2/modal?&order=asc'; //will have to change eventually
    fetch(fetchURL)
        .then(response => response.json())
        .then(data => {
            modal_data = data.find(modal => modal.id === id);
            console.log(modal_data); 
            //title stuff:
            let title = child_obj[key]['title'];  
            let modal_title = document.getElementById("modal-title");
            
            modal_title.innerHTML = title;

            //tagline container
            let tagline_container = document.getElementById('tagline-container');
            let modal_tagline = modal_data["modal_tagline"];
            tagline_container.innerHTML =  "<em>" + modal_tagline + "<em>";

            //generate accordion
            // Select the container where the accordion will be appended
            let accordion_container = document.getElementById('accordion-container');
            // accordion_container.innerHTML = '';
            // Create the accordion element
            let acc = document.createElement("div");
            acc.classList.add("accordion");

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
                console.log(modal_info_text);
                console.log(modal_info_url);
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
            let collapsePhotoHTML = '<div>';
            for (let i = 1; i < 7; i++){
                let info_field = "modal_photo" + i;
                let info_text = "modal_photo_text" + i;
                let info_url = "modal_photo_url" + i;

                let modal_info_text = modal_data[info_field][info_text];
                let modal_info_url = modal_data[info_field][info_url];
                if ((modal_info_text == '') && (modal_info_url == '')){
                    continue;
                }
                console.log(modal_info_text);
                console.log(modal_info_url);
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


            accordion_container.appendChild(acc);
            // allkeyobj[key] = true;

            //for tabs jere:
            let num_tabs = Number(modal_data["modal_tab_number"]);
            for (let i =1; i <= num_tabs; i ++){
                let tab_key = "modal_tab_title" + i;
                let tab_title = modal_data[tab_key];
                create_tabs(i, tab_key, tab_title, tab_title);
            }
            
            



        })
    .catch(error => console.error('Error fetching data:', error));
    
}

function full_screen_button(svgId){
    // let toc_container = document.querySelector("#toc-container");
    // let button = document.createElement("button");
    // // <button style="margin-bottom: 5px; font-size: large;" class="btn btn-info fa fa-arrows-alt btn-block" id="top-button"> Full Screen</button>
    // button.setAttribute("style", "margin-botton: 5px; font-size: large");
    // button.setAttribute("id", "top-button");
    // button.setAttribute('class', `btn btn-info fa fa-arrows-alt btn-block`);
    // toc_container.prepend(button);
    // button.innerHTML = "Full Screen";


    if ((document.fullscreenEnabled || document.webkitFullscreenEnabled)){ 
        let toc_container = document.querySelector("#toc-container");
        let button = document.createElement("button");
        
        // Button attributes
        button.setAttribute("style", "margin-bottom: 5px; font-size: large; z-index: 1");
        button.setAttribute("id", "top-button");
        button.setAttribute('class', 'btn btn-info fa fa-arrows-alt btn-block');
        button.innerHTML = "Full Screen";
        
        // let row = document.createElement("div");
        let row = document.createElement("div");

        row.classList.add("row");
        row.setAttribute("id", "buttonRow");
        // let col = document.createElement("div");
        // col.classList.add("col");
        // col.appendChild(button);
        row.appendChild(button);

        toc_container.prepend(row);
        
        // Fullscreen change event for SVG
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
        
        
        // Open Fullscreen Function
        function openFullScreen() {
          var elem = document.getElementById(svgId);
          if (elem.requestFullscreen) {
            elem.requestFullscreen();
          } else if (elem.webkitRequestFullscreen) { /* Safari */
            elem.webkitRequestFullscreen();
          }
          let modal = document.getElementById("myModal");
            elem.prepend(modal);
        }

        

        
        // Button click event
        button.addEventListener('click', function() {
          openFullScreen();
          // add_modal(); // Ensure add_modal() is defined and functional
        });
        
    }

}
function toggle_text(){
    let toc_container = document.querySelector("#toc-container");

    let button = document.createElement("label");
    button.setAttribute("class", "switch");

    // Create a checkbox input element
    let checkbox = document.createElement("input");
    checkbox.setAttribute("type", "checkbox");
    checkbox.setAttribute("id", "tocWrapper");

    // Create a span element for the slider
    let slider = document.createElement("span");
    slider.setAttribute("class", "slider round");

    // Append the checkbox and slider to the label (button)
    button.appendChild(checkbox);
    button.appendChild(slider);
    // button.innerHTML = "Toggle Image Text";

    let row = document.createElement("div");
    row.classList.add("row");
    row.setAttribute("id", "switchRow");
    // let col = document.createElement("div");
    let col1 = document.createElement("div");
    col1.classList.add("col");
    col1.appendChild(button);

    let col2 = document.createElement("div");
    col2.classList.add("col");
    let toggleText = document.createElement("h5");
    toggleText.innerHTML = "Toggle Text in Image: "
    col2.appendChild(toggleText);

    row.appendChild(col2);
    row.appendChild(col1);
    

    // row.innerText = "Toggle image text:      ";
    // row.appendChild(button);

    toc_container.prepend(row);

    checkbox.addEventListener('change', function() {
        let svgText = document.querySelector("#text");
        if (this.checked) {
            svgText.setAttribute("display", "none");
            // Add your logic for when the toggle switch is ON
        } else {
            svgText.setAttribute("display", "");
            // Add your logic for when the toggle switch is OFF
        }
    });

    
}
// full_screen_button('svg-elem');

//generates table of contents; modal table of contents open modal window, others go to external URLs
function table_of_contents(){
    let elem = document.getElementById("toc1");
    for (let key in child_obj){
        let item = document.createElement("li");
        
        let title = child_obj[key]['title'];  
        let link = document.createElement("a");
        let modal = child_obj[key]['modal'];
        if (modal) {
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

                let tagline_container = document.getElementById('tagline-container');
                tagline_container.innerHTML = '';

        });
        }
        
        else{
            link.href = child_obj[key]['external_url'];
            console.log(link.href);
            link.innerHTML = title;
            item.appendChild(link);
        }
        let svg_elem = document.querySelector('g[id="' + key + '"]');
        // console.log(elem);
        //CHANGE HERE FOR TABLET STUFF

        item.addEventListener('mouseover', function(){
            // console.log('mousing over: ', key); 
            svg_elem.style.stroke = "yellow";
            svg_elem.style.strokeWidth = "6";
        });
        item.addEventListener('mouseout', function(){
            // console.log('mousing out: ', key); 
            svg_elem.style.stroke = "";
            svg_elem.style.strokeWidth = "";
        });
        
        
        elem.appendChild(item);
        console.log(elem);
    }
        
    
}


//generates modal window when SVG element is clicked. 
function add_modal(){
    for (let key in child_obj){
        let elem = document.querySelector('g[id="' + key + '"]');
        if (child_obj[key]['modal']){
            // let elem = document.querySelector('g[id="' + key + '"]');
            let modal = document.getElementById("myModal");
            let closeButton = document.getElementById("close");
            

            elem.addEventListener('click', function() {
                    modal.style.display = "block";
                    render_modal(key );

            });
            
            if (mobileBool){
                let itemContainer = document.querySelector(`#${key}-container`);
                itemContainer.addEventListener('click', function() {
                    modal.style.display = "block";
                    render_modal(key );

            });
            }
            
            closeButton.addEventListener('click', function() {
                    
                    modal.style.display = "none";
                    let accordion_container = document.getElementById('accordion-container');
                    accordion_container.innerHTML = '';

                    let tagline_container = document.getElementById('tagline-container');
                    tagline_container.innerHTML = '';


                    let myTab = document.getElementById('myTab');
                    myTab.innerHTML = '';

                    let tabContentContainer = document.getElementById("myTabContent");
                    tabContentContainer.innerHTML = '';
            });
        } else {
            elem.addEventListener('click', function() {
                let link =  child_obj[key]['external_url'];
                window.location.href = link;

        });
        if (mobileBool){
            let itemContainer = document.querySelector(`#${key}-container`);
            itemContainer.addEventListener('click', function() {
                let link =  child_obj[key]['external_url'];
                window.location.href = link;

        });
        }
        }
    }
}







loadSVG(url, "svg1");







