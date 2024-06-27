console.log("THIS IS A TEST");
// console.log(child_ids);
// access echoed JSON here for use. 
//get all links from single-scene.php
let child_obj = JSON.parse(JSON.stringify(child_ids));
console.log(child_obj);
console.log("this is the post id: ", post_id);//prob dont need this
let url1 =(JSON.stringify(svg_url));
url = url1.substring(2, url1.length - 2);
console.log(url)

//lol
// document.getElementById("svg1").innerHTML =`<img src="${url}" alt="">`;


// Below is the function that will be used to include SVGs within each scene
//based on link_svg from infographiq.js
function link_svg(child_obj, url){
    //stuff to actually build SVG
    const svg = document.createElement('li');

    //add child 
    document.getElementById('svg1').appendChild(svg);
}

link_svg(child_obj, url);