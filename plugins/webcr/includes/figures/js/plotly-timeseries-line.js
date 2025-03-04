/**
 * @file This file contains functions for creating and managing interactive
 * time-series line plots using the Plotly library. These functions display the plot but also get user input for function parameters.
 * @version 1.0.0
 */

/**
 * Produces a time-series line plot using Plotly.
 *
 * @async
 * @function producePlotlyLineFigure
 * @param {string} targetFigureElement - The ID of the HTML element where the plot will be inserted.
 * @param {string} jsonFilePath - The relative path to the JSON data file.
 * @param {FigureArguments} figureArguments - An object containing the figure's arguments.
 * @throws {Error} Throws an error if there is a network issue fetching the data, or if data is formatted improperly.
 * @example
 * producePlotlyLineFigure('myFigureContainer', '/data/timeseries.json', {
 *   NumberOfLines: 2,
 *   XAxis: 'Date',
 *   XAxisTitle: 'Date',
 *   YAxisTitle: 'Value',
 *   XAxisLowBound: 0,
 *   XAxisHighBound: 100,
 *   YAxisLowBound: 0,
 *   YAxisHighBound: 20,
 *   Line1: 'Line1Data',
 *   Line1Title: 'Line 1',
 *   Line1Color: '#0000FF',
 *   Line2: 'Line2Data',
 *   Line2Title: 'Line 2',
 *   Line2Color: '#FF0000',
 * });
 */
async function producePlotlyLineFigure(targetFigureElement, jsonFilePath, figureArguments){
    try {
        await loadExternalScript('https://cdn.plot.ly/plotly-3.0.0.min.js');
        const rootURL = window.location.origin;
        const finalURL = rootURL + jsonFilePath;
        const rawResponse = await fetch(finalURL);
        if (!rawResponse.ok) {
            throw new Error('Network response was not ok');
        }
        const responseJson = await rawResponse.json();
        const dataToBePlotted = responseJson.data;

        let newDiv = document.createElement('div');
        newDiv.id = "plotlyFigure";
        newDiv.classList.add("container", "figure_interactive");
console.log(targetFigureElement);
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

/**
 * Creates the form fields that allow users to define parameters for a Plotly line graph.
 *
 * @function plotlyLineParameterFields
 * @param {Object} jsonColumns - An object containing the column names from the json file that is used to create the plotly figure.
 * @example
 * plotlyLineParameterFields(["Year", "AverageTemp"]);
 */
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
      displayLineFields(selectNumberLines.value) });
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

/**
 * Code used within the Wordpress figure admin side of the house to generate form fields to assign data columns to lines on the graph.
 * @function displayLineFields
 * @param {number} numLines - The number of lines to display fields for.
 * @param {Object} jsonColumns - An object containing the column names from the json file that is used to create the plotly figure.
 */
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
