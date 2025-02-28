//utility functions used in lots of places
function loadExternalScript(url) {
    return new Promise((resolve, reject) => {
      // Check if script is already loaded
      if (document.querySelector(`script[src="${url}"]`)) {
          resolve();
          return;
      }
  
      const script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = url;
      script.async = true;
  
      script.onload = () => {
          resolve();
      };
  
      script.onerror = () => {
          reject(new Error(`Failed to load script: ${url}`));
      };
  
      document.head.appendChild(script);
    });
  }
  
//log values for fields associated with javascript figure parameters to the field "figure interactive arguments"
function logFormFieldValues() {
    const allFields = document.getElementsByName("plotFields");
    let fieldValues = [];
    allFields.forEach((uniqueField) => {
        fieldValues.push([uniqueField.id, uniqueField.value]);
    });
    document.getElementsByName("figure_interactive_arguments")[0].value = JSON.stringify(fieldValues); 
}

//fill in values for fields associated with javascript figure parameters from the field "figure interactive arguments"
function fillFormFieldValues(elementID){
    const interactiveFields = document.getElementsByName("figure_interactive_arguments")[0].value;
    if (interactiveFields != ""  && interactiveFields != null) {
        const resultJSON = Object.fromEntries(JSON.parse(interactiveFields));

        if (resultJSON[elementID] != undefined && resultJSON[elementID] != ""){
            return resultJSON[elementID];
        }
    }
}