console.log("THIS IS A TEST");
// console.log(child_ids);
// access echoed JSON here for use. 
//get all links from single-scene.php
let child_obj = JSON.parse(JSON.stringify(child_ids));
console.log(child_obj);
// console.log("this is the post id: ", post_id);//prob dont need this
let url1 =(JSON.stringify(svg_url));
url = url1.substring(2, url1.length - 2);
// console.log(url)

//lol
// document.getElementById("svg1").innerHTML =`<img src="${url}" alt="">`;


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

        // Step 3: Move the SVG upwards
        // const translateY = -10; // Adjust this value to move the image up
        // const existingTransform = svgElement.getAttribute("transform") || "";
        // const newTransform = `translate(0, ${translateY}) ${existingTransform}`;
        // svgElement.setAttribute("transform", newTransform.trim());

        // Step 4: Append the SVG to the DOM
        const container = document.getElementById(containerId);
        container.appendChild(svgElement);
        // console.log(svgElement);
        highlight_icons();
        table_of_contents();
        add_modal();
    } catch (error) {
        console.error('Error fetching or parsing the SVG:', error);
    }
}

//TODO: lot of redundant code within below 3 functions, a working start; might be a good idea to clean up at some point


//highlight items on mouseover, remove highlight when off; 
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
                let title = child_obj[key]['title'];  
                let modal_title = document.getElementById("modal-title");
                modal_title.innerHTML = title;
            });
        }
        
        else{
            link.href = child_obj[key]['external_url'];
            link.innerHTML = title;
            item.appendChild(link);
        }
        let svg_elem = document.querySelector('g[id="' + key + '"]');
        // console.log(elem);
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
        if (child_obj[key]['modal']){
            let elem = document.querySelector('g[id="' + key + '"]');
            let modal = document.getElementById("myModal");
            let closeButton = document.getElementById("close");
            

            elem.addEventListener('click', function() {
                    modal.style.display = "block";
                    let title = child_obj[key]['title'];  
                    let modal_title = document.getElementById("modal-title");
                    modal_title.innerHTML = title;
            });
            
            closeButton.addEventListener('click', function() {
                    modal.style.display = "none";
            });
        }
    }
}

loadSVG(url, "svg1");



