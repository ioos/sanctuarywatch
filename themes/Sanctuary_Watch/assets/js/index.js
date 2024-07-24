console.log("here is the post id");
console.log(post_id);

// let test = document.querySelector("body > div.container-fluid.main-container");
// test.innerHTML = '';


async function getInstanceInfo() {
    const protocol = window.location.protocol;
    const host = window.location.host;
    const fetchURL = protocol + "//" + host + "/wp-json/wp/v2/instance?&order=asc";
  
    try {
      const response = await fetch(fetchURL);
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      const data = await response.json();
      return data;
    } catch (error) {
      console.error('Error fetching data:', error);
      throw error; // Re-throw the error if needed
    }
  }

  let testData;
  (async () => {
    try {
      testData = await getInstanceInfo();
      console.log(testData);

      let elem = document.querySelector("#webcrs---ecosystem-tracking-tools-for-condition-reporting > div");
      let list = document.createElement("ul");
      for (let idx in testData){
        let child = testData[idx];
        let listItem = document.createElement('li');
        let link = document.createElement('a');

        link.setAttribute('href', child.link);
        link.setAttribute('id', child.instance_slug);
        link.innerText = child.instance_short_title;

        listItem.appendChild(link);
        list.appendChild(listItem);
      }
      elem.appendChild(list);
    } catch (error) {
      console.error('Error:', error);
    }
  })();
  


 

