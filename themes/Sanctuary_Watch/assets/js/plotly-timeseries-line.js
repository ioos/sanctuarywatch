// Code for plotting time series data with a plotly line

async function producePlotlyLineFigure(targetFigureElement, interactive_arguments){
    try {
        await loadExternalScript('https://cdn.plot.ly/plotly-3.0.0.min.js');

        const rawField = interactive_arguments;
        const figureArguments = Object.fromEntries(JSON.parse(rawField));
        const rootURL = window.location.origin;

        //Rest call to get uploaded_path_json
        const figureRestCall = rootURL + "/wp-json/wp/v2/figure?_fields=uploaded_path_json";
        const response = await fetch(figureRestCall);
        const data = await response.json();
        const uploaded_path_json = data[0].uploaded_path_json;

        const restOfURL = "/wp-content" + uploaded_path_json.split("wp-content")[1];
        const finalURL = rootURL + restOfURL;

        const rawResponse = await fetch(finalURL);
        if (!rawResponse.ok) {
            throw new Error('Network response was not ok');
        }
        const responseJson = await rawResponse.json();
        const dataToBePlotted = responseJson.data;

        let newDiv = document.createElement('div');
        newDiv.id = "plotlyFigure";
        newDiv.classList.add("container", "figure_interactive");

        const targetElement = document.getElementById(targetFigureElement);
        targetElement.appendChild(newDiv);
        
        const numLines = figureArguments['NumberOfLines'];

        let plotlyX;
        let plotlyY;
        let columnXHeader;
        let columnYHeader;
        let targetLineColumn;
        let singleLinePlotly;
        let allLinesPlotly = [];

        for (let i = 1; i <= numLines; i++){
            targetLineColumn = "Line" + i;
            columnXHeader = figureArguments['XAxis'];

            plotlyX = dataToBePlotted[columnXHeader];
            columnYHeader = figureArguments[targetLineColumn];
            plotlyY = dataToBePlotted[columnYHeader];
            singleLinePlotly = {
                x: plotlyX,
                y: plotlyY,
                mode: 'lines+markers',
                type: 'scatter',
                marker: {
                    color: figureArguments[targetLineColumn + "Color"]
                },
                name: figureArguments[targetLineColumn + "Title"],
                hovertemplate: 
                figureArguments['XAxisTitle'] + ': %{x}<br>' +  // Custom label for x-axis
                figureArguments['YAxisTitle'] + ': %{y}' // Custom label for y-axis
              };
              //console.log(singleLinePlotly);
              allLinesPlotly.push(singleLinePlotly);
        }
          
          var layout = {
            xaxis: {
              title: {
                text: figureArguments['XAxisTitle']
              },
              linecolor: 'black', 
              linewidth: 1,
              range: [figureArguments['XAxisLowBound'], figureArguments['XAxisHighBound']]                      
            },
            yaxis: {
              title: {
                text: figureArguments['YAxisTitle']
              },
              linecolor: 'black', 
              linewidth: 1,
              range: [figureArguments['YAxisLowBound'], figureArguments['YAxisHighBound']]     
            }
          };
          const config = {
            responsive: true  // This makes the plot resize with the browser window
          };

          Plotly.newPlot('plotlyFigure', allLinesPlotly, layout, config);

    } catch (error) {
        console.error('Error loading scripts:', error);
    }
}

function plotlyLineParameterFields(jsonColumns){
  let newDiv = document.createElement("div");
  newDiv.id = 'secondaryGraphFields';
  const targetElement = document.getElementById('graphGUI');

  let newRow;
  let newColumn1;
  let newColumn2;

  // Create input fields for X and Y Axis Titles
  const axisTitleArray = ["X", "Y"];

  axisTitleArray.forEach((axisTitle) => {
      newRow = document.createElement("div");
      newRow.classList.add("row", "fieldPadding");
      newColumn1 = document.createElement("div");
      newColumn1.classList.add("col-3");   
      newColumn2 = document.createElement("div");
      newColumn2.classList.add("col");

      let labelInputAxisTitle = document.createElement("label");
      labelInputAxisTitle.for = axisTitle + "AxisTitle";
      labelInputAxisTitle.innerHTML = axisTitle + " Axis Title";
      let inputAxisTitle = document.createElement("input");
      inputAxisTitle.id = axisTitle + "AxisTitle";
      inputAxisTitle.name = "plotFields";
      inputAxisTitle.size = "70";
      fieldValueSaved = fillFormFieldValues(inputAxisTitle.id);
      if (fieldValueSaved != undefined){
          inputAxisTitle.value = fieldValueSaved;
      }
      inputAxisTitle.addEventListener('change', function() {
          logFormFieldValues();
      });
      newColumn1.appendChild(labelInputAxisTitle);
      newColumn2.appendChild(inputAxisTitle);
      newRow.append(newColumn1, newColumn2);
      newDiv.append(newRow);    

      const rangeBound =["Low", "High"];
      rangeBound.forEach((bound) => {
          newRow = document.createElement("div");
          newRow.classList.add("row", "fieldPadding");
          newColumn1 = document.createElement("div");
          newColumn1.classList.add("col-3");   
          newColumn2 = document.createElement("div");
          newColumn2.classList.add("col");

          let labelBound = document.createElement("label");
          labelBound.for =  axisTitle + bound + "Bound";
          labelBound.innerHTML = axisTitle + " Axis, " + bound + " Bound";
          let inputBound = document.createElement("input");
          inputBound.id = axisTitle + "Axis" + bound + "Bound";
          inputBound.name = "plotFields";
          inputBound.type = "number";
          fieldValueSaved = fillFormFieldValues(inputBound.id);
          if (fieldValueSaved != undefined){
              inputBound.value = fieldValueSaved;
          }
          inputBound.addEventListener('change', function() {
              logFormFieldValues();
          });
          newColumn1.appendChild(labelBound);
          newColumn2.appendChild(inputBound);
          newRow.append(newColumn1, newColumn2);
          newDiv.append(newRow); 
      });

  });

  // Create select field for number of lines to be plotted 
  let labelSelectNumberLines = document.createElement("label");
  labelSelectNumberLines.for = "NumberOfLines";
  labelSelectNumberLines.innerHTML = "Number of Lines to Be Plotted";
  let selectNumberLines = document.createElement("select");
  selectNumberLines.id = "NumberOfLines";
  selectNumberLines.name = "plotFields";
  selectNumberLines.addEventListener('change', function() {
      displayLineFields(selectNumberLines.value, jsonColumns) });
  selectNumberLines.addEventListener('change', function() {
          logFormFieldValues();
      });

  for (let i = 1; i < 7; i++){
      let selectNumberLinesOption = document.createElement("option");
      selectNumberLinesOption.value = i;
      selectNumberLinesOption.innerHTML = i; 
      selectNumberLines.appendChild(selectNumberLinesOption);
  }
  fieldValueSaved = fillFormFieldValues(selectNumberLines.id);
  if (fieldValueSaved != undefined){
      selectNumberLines.value = fieldValueSaved;
  }
  newRow = document.createElement("div");
  newRow.classList.add("row", "fieldPadding");
  newColumn1 = document.createElement("div");
  newColumn1.classList.add("col-3");   
  newColumn2 = document.createElement("div");
  newColumn2.classList.add("col");

  newColumn1.appendChild(labelSelectNumberLines);
  newColumn2.appendChild(selectNumberLines);
  newRow.append(newColumn1, newColumn2);
  newDiv.append(newRow);

  let labelSelectXAxisFormat = document.createElement("label");
  labelSelectXAxisFormat.for = "XAxisFormat";
  labelSelectXAxisFormat.innerHTML = "X Axis Date Format";
  let selectXAxisFormat = document.createElement("select");
  selectXAxisFormat.id = "XAxisFormat";
  selectXAxisFormat.name = "plotFields";
  selectXAxisFormat.addEventListener('change', function() {
      logFormFieldValues();
  });

  const dateFormats =["YYYY", "YYYY-MM-DD"];

  dateFormats.forEach((dateFormat) => {
      let selectXAxisFormatOption = document.createElement("option");
      selectXAxisFormatOption.value = dateFormat;
      selectXAxisFormatOption.innerHTML = dateFormat; 
      selectXAxisFormat.appendChild(selectXAxisFormatOption);
  });
  fieldValueSaved = fillFormFieldValues(selectXAxisFormat.id);
  if (fieldValueSaved != undefined){
      selectXAxisFormat.value = fieldValueSaved;
  }

  newRow = document.createElement("div");
  newRow.classList.add("row", "fieldPadding");
  newColumn1 = document.createElement("div");
  newColumn1.classList.add("col-3");   
  newColumn2 = document.createElement("div");
  newColumn2.classList.add("col");

  newColumn1.appendChild(labelSelectXAxisFormat);
  newColumn2.appendChild(selectXAxisFormat);
  newRow.append(newColumn1, newColumn2);
  newDiv.append(newRow);

  let newHR = document.createElement("hr");
  newHR.style = "margin-top:15px";
  newDiv.append(newHR);        

  targetElement.appendChild(newDiv);

  // Run display line fields
  displayLineFields(selectNumberLines.value, jsonColumns);
}


// generate the form fields needed for users to indicate preferences for how a figure should appear 
function displayLineFields (numLines, jsonColumns) {
  let assignColumnsToPlot = document.getElementById('assignColumnsToPlot');
  // If the element exists
  if (assignColumnsToPlot) {
      // Remove the scene window
      assignColumnsToPlot.parentNode.removeChild(assignColumnsToPlot);
  }

  if (numLines > 0) {
      let newDiv = document.createElement("div");
      newDiv.id = "assignColumnsToPlot";

      let fieldLabels = [["XAxis", "X Axis Column"]];
      for (let i = 1; i <= numLines; i++){
          fieldLabels.push(["Line" + i, "Line " + i + " Column"]);
      }

      fieldLabels.forEach((fieldLabel) => {
          let labelSelectColumn = document.createElement("label");
          labelSelectColumn.for = fieldLabel[0];
          labelSelectColumn.innerHTML = fieldLabel[1];
          let selectColumn = document.createElement("select");
          selectColumn.id = fieldLabel[0];
          selectColumn.name = "plotFields";
          selectColumn.addEventListener('change', function() {
              logFormFieldValues();
          });

          let selectColumnOption = document.createElement("option");
          selectColumnOption.value = "None";
          selectColumnOption.innerHTML = "None"; 
          selectColumn.appendChild(selectColumnOption);

          Object.entries(jsonColumns).forEach(([jsonColumnsKey, jsonColumnsValue]) => {
              selectColumnOption = document.createElement("option");
              selectColumnOption.value = jsonColumnsValue;// jsonColumnsKey;
              selectColumnOption.innerHTML = jsonColumnsValue; 
              selectColumn.appendChild(selectColumnOption);
          });
          fieldValueSaved = fillFormFieldValues(selectColumn.id);
          if (fieldValueSaved != undefined){
              selectColumn.value = fieldValueSaved;
          }

          let newRow = document.createElement("div");
          newRow.classList.add("row", "fieldPadding");

          if (fieldLabel[0] != "XAxis"){      
              fieldLabelNumber = parseInt(fieldLabel[0].slice(-1));
              if (fieldLabelNumber % 2 != 0 ){
                  newRow.classList.add("row", "fieldBackgroundColor");
              }
          }

          let newColumn1 = document.createElement("div");
          newColumn1.classList.add("col-3");   
          let newColumn2 = document.createElement("div");
          newColumn2.classList.add("col");

          newColumn1.appendChild(labelSelectColumn);
          newColumn2.appendChild(selectColumn);
          newRow.append(newColumn1, newColumn2);
          newDiv.append(newRow);

          if (fieldLabel[0] != "XAxis"){
              // Add line label field
              newRow = document.createElement("div");
              newRow.classList.add("row", "fieldPadding");

              if (fieldLabelNumber % 2 != 0 ){
                  newRow.classList.add("row", "fieldBackgroundColor");
              }

              newColumn1 = document.createElement("div");
              newColumn1.classList.add("col-3");   
              newColumn2 = document.createElement("div");
              newColumn2.classList.add("col");

              let labelInputTitle = document.createElement("label");
              labelInputTitle.for = fieldLabel[0] + "Title";
              labelInputTitle.innerHTML = fieldLabel[1] + " Title";
              let inputTitle = document.createElement("input");
              inputTitle.id = fieldLabel[0] + "Title";
              inputTitle.size = "70";
              inputTitle.name = "plotFields";
              inputTitle.addEventListener('change', function() {
                  logFormFieldValues();
              });
              fieldValueSaved = fillFormFieldValues(inputTitle.id);
              if (fieldValueSaved != undefined){
                  inputTitle.value = fieldValueSaved;
              }

              newColumn1.appendChild(labelInputTitle);
              newColumn2.appendChild(inputTitle);
              newRow.append(newColumn1, newColumn2);
              newDiv.append(newRow); 

              // Add color field
              newRow = document.createElement("div");
              newRow.classList.add("row", "fieldPadding");
              if (fieldLabelNumber % 2 != 0 ){
                  newRow.classList.add("row", "fieldBackgroundColor");
              }
              newColumn1 = document.createElement("div");
              newColumn1.classList.add("col-3");   
              newColumn2 = document.createElement("div");
              newColumn2.classList.add("col");

              let labelInputColor = document.createElement("label");
              labelInputColor.for = fieldLabel[0] + "Color";
              labelInputColor.innerHTML = fieldLabel[1] + " Color";
              let inputColor = document.createElement("input");
              inputColor.id = fieldLabel[0] + "Color";
              inputColor.name = "plotFields";
              inputColor.type = "color";
              fieldValueSaved = fillFormFieldValues(inputColor.id);
              if (fieldValueSaved != undefined){
                  inputColor.value = fieldValueSaved;
              }
              inputColor.addEventListener('change', function() {
                  logFormFieldValues();
              });

              newColumn1.appendChild(labelInputColor);
              newColumn2.appendChild(inputColor);
              newRow.append(newColumn1, newColumn2);
              newDiv.append(newRow);    
          }

          const targetElement = document.getElementById('graphGUI');
          targetElement.appendChild(newDiv);
      });
  }
}
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
    //document.getElementsByName("figure_interactive_arguments")[0].value = JSON.stringify(fieldValues);
    interactive_arguments[0].value = JSON.stringify(fieldValues); 
}

//fill in values for fields associated with javascript figure parameters from the field "figure interactive arguments"
function fillFormFieldValues(elementID){
    const interactiveFields = interactive_arguments[0].value; //document.getElementsByName("figure_interactive_arguments")[0].value;

    if (interactiveFields != ""  && interactiveFields != null) {
        const resultJSON = Object.fromEntries(JSON.parse(interactiveFields));

        if (resultJSON[elementID] != undefined && resultJSON[elementID] != ""){
            return resultJSON[elementID];
        }
    }
}
