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
    } catch (error) {
        console.error('Error fetching or parsing the SVG:', error);
    }
}


//highlight items on mouseover, remove highlight when off; TODO: add more stuff to event listeners 
function highlight_icons(){
    for (let key in child_obj){
        let elem = document.querySelector('g[id="' + key + '"]');
        console.log(elem);
        elem.addEventListener('mouseover', function(){
            console.log('mousing over: ', key); 
            elem.style.stroke = "yellow";
            elem.style.strokeWidth = "6";
        });
        elem.addEventListener('mouseout', function(){
            console.log('mousing out: ', key); 
            elem.style.stroke = "";
            elem.style.strokeWidth = "";
        });
            
    }  
}
 



function table_of_contents(){
    let elem = document.getElementById("toc1");
    for (let key in child_obj){
        let item = document.createElement("li");
        
        let title = child_obj[key]['title'];  
        //newLi.innerHTML = 
        let link = document.createElement("a");
        let modal = child_obj[key]['modal'];
        if (modal){
            //two things: 
            //on hover over link,
            link.href = "https://mail.google.com/mail/u/0/?tab=rm&ogbl"; //temporary, want to make it modal popup here
            // link.classList.add("hidden-link");
            link.innerHTML = title;
            item.appendChild(link);
        } else{
            link.href = child_obj[key]['external_url'];
            link.innerHTML = title;
            item.appendChild(link);
        }
        let svg_elem = document.querySelector('g[id="' + key + '"]');
        // console.log(elem);
        item.addEventListener('mouseover', function(){
            console.log('mousing over: ', key); 
            svg_elem.style.stroke = "yellow";
            svg_elem.style.strokeWidth = "6";
        });
        item.addEventListener('mouseout', function(){
            console.log('mousing out: ', key); 
            svg_elem.style.stroke = "";
            svg_elem.style.strokeWidth = "";
        });
        elem.appendChild(item);
        
        console.log(elem);
    }
        
    
}
//idea for function: HTML alr exists for each modal (w all the information at least)
// find way to inject modal into IFra
function add_modal(){

}

loadSVG(url, "svg1");



