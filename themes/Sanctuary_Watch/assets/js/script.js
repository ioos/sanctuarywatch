console.log("THIS IS A TEST");
// console.log(child_ids);
// access echoed JSON here for use. 
let child_obj = JSON.parse(JSON.stringify(child_ids));
console.log(child_obj);
console.log("this is the post id: ", post_id);
let url1 =(JSON.stringify(svg_url));
url = url1.substring(2, url1.length - 2);
console.log(url)


document.getElementById("svg1").innerHTML =`<img src="${url}" alt="">`;