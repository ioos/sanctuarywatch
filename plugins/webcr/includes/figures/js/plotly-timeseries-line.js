/**
 * Produces a Plotly line figure for time series data.
 * Dynamically generates a Plotly chart based on the provided interactive arguments and JSON data.
 * 
 * @param {string} targetFigureElement - The ID of the target HTML element where the figure will be appended.
 * @param {string} interactive_arguments - A JSON string containing the configuration for the figure (e.g., axis titles, line colors, etc.).
 */
async function producePlotlyLineFigure(targetFigureElement, interactive_arguments){
    try {
        await loadExternalScript('https://cdn.plot.ly/plotly-3.0.0.min.js');

        // Parse the interactive arguments
        const rawField = interactive_arguments;
        const figureArguments = Object.fromEntries(JSON.parse(rawField));
        const rootURL = window.location.origin;

        // REST API call to get the uploaded JSON file path
        const figureRestCall = rootURL + "/wp-json/wp/v2/figure?_fields=uploaded_path_json";
        const response = await fetch(figureRestCall);
        const data = await response.json();
        const uploaded_path_json = data[0].uploaded_path_json;

        // Construct the URL to fetch the JSON data
        const restOfURL = "/wp-content" + uploaded_path_json.split("wp-content")[1];
        const finalURL = rootURL + restOfURL;

        // Fetch the JSON data
        const rawResponse = await fetch(finalURL);
        if (!rawResponse.ok) {
            throw new Error('Network response was not ok');
        }
        const responseJson = await rawResponse.json();
        const dataToBePlotted = responseJson.data;

        // Create a new div for the Plotly figure
        let newDiv = document.createElement('div');
        newDiv.id = "plotlyFigure";
        newDiv.classList.add("container", "figure_interactive");

        // Append the new div to the target element
        const targetElement = document.getElementById(targetFigureElement);
        targetElement.appendChild(newDiv);
        
        // Extract the number of lines to be plotted
        const numLines = figureArguments['NumberOfLines'];

        let plotlyX;
        let plotlyY;
        let columnXHeader;
        let columnYHeader;
        let targetLineColumn;
        let singleLinePlotly;
        let allLinesPlotly = [];

        // Loop through each line and prepare the data for Plotly
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
              console.log(singleLinePlotly);
              allLinesPlotly.push(singleLinePlotly);
        }

        // Get the container for the figure
        var container = document.getElementById('javascript_figure_target');

        //ADMIN SIDE GRAPH DISPLAY SETTINGS
        if (window.location.href.includes("wp-admin/post.php")) {
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
                },
                autosize: true, 
                };
            const config = {
                responsive: true  // This makes the plot resize with the browser window
                };
            Plotly.newPlot('plotlyFigure', allLinesPlotly, layout, config);

        }
        //THEME SIDE GRAPH DISPLAY SETTINGS
        else {
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
                },
                //autosize: true, 
                width: container.clientWidth, 
                height: container.clientHeight
                };
            const config = {
            responsive: true  // This makes the plot resize with the browser window
            };
            document.getElementById("plotlyFigure").style.setProperty("width", "100%", "important");
            document.getElementById("plotlyFigure").style.setProperty("max-width", "none", "important");

            Plotly.newPlot('plotlyFigure', allLinesPlotly, layout, config);
        }        

    } catch (error) {
        console.error('Error loading scripts:', error);
    }
}


/**
 * Dynamically generates input fields for configuring Plotly line chart parameters.
 * Creates fields for X and Y axis titles, as well as their low and high bounds.
 * 
 * @param {Object} jsonColumns - An object containing the JSON columns available for plotting.
 * @param {string} interactive_arguments - A JSON string containing saved interactive arguments for pre-filling fields.
 */
function plotlyLineParameterFields(jsonColumns, interactive_arguments){

  // Create a new container for the secondary graph fields
  console.log('plotlyLineParameterFields', interactive_arguments)
  let newDiv = document.createElement("div");
  newDiv.id = 'secondaryGraphFields';
  const targetElement = document.getElementById('graphGUI');

  let newRow;
  let newColumn1;
  let newColumn2;

  // Create input fields for X and Y Axis Titles
  const axisTitleArray = ["X", "Y"];
  
  axisTitleArray.forEach((axisTitle) => {
      // Create a new row for the axis title input
      newRow = document.createElement("div");
      newRow.classList.add("row", "fieldPadding");
      newColumn1 = document.createElement("div");
      newColumn1.classList.add("col-3");   
      newColumn2 = document.createElement("div");
      newColumn2.classList.add("col");
    
      // Create a label and input field for the axis title
      let labelInputAxisTitle = document.createElement("label");
      labelInputAxisTitle.for = axisTitle + "AxisTitle";
      labelInputAxisTitle.innerHTML = axisTitle + " Axis Title";
      let inputAxisTitle = document.createElement("input");
      inputAxisTitle.id = axisTitle + "AxisTitle";
      inputAxisTitle.name = "plotFields";
      inputAxisTitle.size = "70";

      // Pre-fill the input field with saved values if available
      fieldValueSaved = fillFormFieldValues(inputAxisTitle.id, interactive_arguments);
      if (fieldValueSaved != undefined){
          inputAxisTitle.value = fieldValueSaved;
      }
      // Add an event listener to log changes to the input field
      inputAxisTitle.addEventListener('change', function() {
          logFormFieldValues();
      });

      // Append the label and input field to the row
      newColumn1.appendChild(labelInputAxisTitle);
      newColumn2.appendChild(inputAxisTitle);
      newRow.append(newColumn1, newColumn2);
      newDiv.append(newRow);    

      // Create input fields for the low and high bounds of the axis
      const rangeBound =["Low", "High"];
      rangeBound.forEach((bound) => {
          // Create a new row for the bound input
          newRow = document.createElement("div");
          newRow.classList.add("row", "fieldPadding");
          newColumn1 = document.createElement("div");
          newColumn1.classList.add("col-3");   
          newColumn2 = document.createElement("div");
          newColumn2.classList.add("col");
          
          // Create a label and input field for the bound
          let labelBound = document.createElement("label");
          labelBound.for =  axisTitle + bound + "Bound";
          labelBound.innerHTML = axisTitle + " Axis, " + bound + " Bound";
          let inputBound = document.createElement("input");
          inputBound.id = axisTitle + "Axis" + bound + "Bound";
          inputBound.name = "plotFields";
          inputBound.type = "number";

          // Pre-fill the input field with saved values if available
          fieldValueSaved = fillFormFieldValues(inputBound.id, interactive_arguments);
          if (fieldValueSaved != undefined){
              inputBound.value = fieldValueSaved;
          }

          // Add an event listener to log changes to the input field
          inputBound.addEventListener('change', function() {
              logFormFieldValues();
          });

          // Append the label and input field to the row
          newColumn1.appendChild(labelBound);
          newColumn2.appendChild(inputBound);
          newRow.append(newColumn1, newColumn2);
          newDiv.append(newRow); 
      });

  });


  /**
  * Creates input fields for selecting the number of lines to be plotted and the X-axis date format.
  * Dynamically updates the UI based on user input and pre-fills fields with saved values if available.
  */

  // Create select field for number of lines to be plotted
  let labelSelectNumberLines = document.createElement("label");
  labelSelectNumberLines.for = "NumberOfLines";
  labelSelectNumberLines.innerHTML = "Number of Lines to Be Plotted";
  let selectNumberLines = document.createElement("select");
  selectNumberLines.id = "NumberOfLines";
  selectNumberLines.name = "plotFields";

  // Add event listeners to handle changes in the number of lines
  selectNumberLines.addEventListener('change', function() {
      displayLineFields(selectNumberLines.value, jsonColumns, interactive_arguments) });
  selectNumberLines.addEventListener('change', function() {
          logFormFieldValues();
      });
  
  // Populate the select field with options for 1 to 6 lines
  for (let i = 1; i < 7; i++){
      let selectNumberLinesOption = document.createElement("option");
      selectNumberLinesOption.value = i;
      selectNumberLinesOption.innerHTML = i; 
      selectNumberLines.appendChild(selectNumberLinesOption);
  }

  // Pre-fill the select field with saved values if available
  fieldValueSaved = fillFormFieldValues(selectNumberLines.id, interactive_arguments);
  if (fieldValueSaved != undefined){
      selectNumberLines.value = fieldValueSaved;
  }

  // Create a new row and columns for the number of lines field
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
  
  // Create a label and select field for the X-axis date format
  let labelSelectXAxisFormat = document.createElement("label");
  labelSelectXAxisFormat.for = "XAxisFormat";
  labelSelectXAxisFormat.innerHTML = "X Axis Date Format";
  let selectXAxisFormat = document.createElement("select");
  selectXAxisFormat.id = "XAxisFormat";
  selectXAxisFormat.name = "plotFields";

  // Add an event listener to log changes in the X-axis date format
  selectXAxisFormat.addEventListener('change', function() {
      logFormFieldValues();
  });

  // Populate the select field with predefined date formats
  const dateFormats =["YYYY", "YYYY-MM-DD"];
  dateFormats.forEach((dateFormat) => {
      let selectXAxisFormatOption = document.createElement("option");
      selectXAxisFormatOption.value = dateFormat;
      selectXAxisFormatOption.innerHTML = dateFormat; 
      selectXAxisFormat.appendChild(selectXAxisFormatOption);
  });

  // Pre-fill the select field with saved values if available
  fieldValueSaved = fillFormFieldValues(selectXAxisFormat.id, interactive_arguments);
  if (fieldValueSaved != undefined){
      selectXAxisFormat.value = fieldValueSaved;
  }

  // Create a new row and columns for the X-axis date format field
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

  // Add a horizontal rule for visual separation
  let newHR = document.createElement("hr");
  newHR.style = "margin-top:15px";
  newDiv.append(newHR);        

  // Append the new container to the target element
  targetElement.appendChild(newDiv);

  // Run the displayLineFields function to dynamically generate line-specific fields
  displayLineFields(selectNumberLines.value, jsonColumns, interactive_arguments);
}


/**
 * Generates the form fields needed for users to indicate preferences for how a figure should appear.
 * Dynamically creates fields for selecting X-axis and line-specific columns, titles, and colors.
 * 
 * @param {number} numLines - The number of lines to be plotted.
 * @param {Object} jsonColumns - An object containing the JSON columns available for plotting.
 * @param {string} interactive_arguments - A JSON string containing saved interactive arguments for pre-filling fields.
 */
function displayLineFields (numLines, jsonColumns, interactive_arguments) {
  // Remove the existing container for assigning columns to plots if it exists
  let assignColumnsToPlot = document.getElementById('assignColumnsToPlot');
  // If the element exists
  if (assignColumnsToPlot) {
      // Remove the scene window
      assignColumnsToPlot.parentNode.removeChild(assignColumnsToPlot);
  }

  // If the number of lines is greater than 0, create new fields
  if (numLines > 0) {
      let newDiv = document.createElement("div");
      newDiv.id = "assignColumnsToPlot";
      
      // Define the labels for the fields (X-axis and lines)
      let fieldLabels = [["XAxis", "X Axis Column"]];
      for (let i = 1; i <= numLines; i++){
          fieldLabels.push(["Line" + i, "Line " + i + " Column"]);
      }

      // Iterate over each field label and create corresponding fields
      fieldLabels.forEach((fieldLabel) => {
          // Create a label and dropdown for selecting the column
          let labelSelectColumn = document.createElement("label");
          labelSelectColumn.for = fieldLabel[0];
          labelSelectColumn.innerHTML = fieldLabel[1];
          let selectColumn = document.createElement("select");
          selectColumn.id = fieldLabel[0];
          selectColumn.name = "plotFields";

          // Add an event listener to log changes in the dropdown
          selectColumn.addEventListener('change', function() {
              logFormFieldValues();
          });

          // Add a default "None" option to the dropdown
          let selectColumnOption = document.createElement("option");
          selectColumnOption.value = "None";
          selectColumnOption.innerHTML = "None"; 
          selectColumn.appendChild(selectColumnOption);
          
          // Populate the dropdown with JSON column options
          Object.entries(jsonColumns).forEach(([jsonColumnsKey, jsonColumnsValue]) => {
              selectColumnOption = document.createElement("option");
              selectColumnOption.value = jsonColumnsValue;// jsonColumnsKey;
              selectColumnOption.innerHTML = jsonColumnsValue; 
              selectColumn.appendChild(selectColumnOption);
          });

          // Pre-fill the dropdown with saved values if available
          fieldValueSaved = fillFormFieldValues(selectColumn.id, interactive_arguments);
          if (fieldValueSaved != undefined){
              selectColumn.value = fieldValueSaved;
          }
          
          // Create a new row and columns for the dropdown
          let newRow = document.createElement("div");
          newRow.classList.add("row", "fieldPadding");
          
          // Add alternating background color for better readability
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

          // Add additional fields for line-specific titles and colors
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

              // Add an event listener to log changes in the input field
              inputTitle.addEventListener('change', function() {
                  logFormFieldValues();
              });

              // Pre-fill the input field with saved values if available
              fieldValueSaved = fillFormFieldValues(inputTitle.id, interactive_arguments);
              if (fieldValueSaved != undefined){
                  inputTitle.value = fieldValueSaved;
              }

              newColumn1.appendChild(labelInputTitle);
              newColumn2.appendChild(inputTitle);
              newRow.append(newColumn1, newColumn2);
              newDiv.append(newRow); 

              // Add line color field
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

              // Pre-fill the color field with saved values if available
              fieldValueSaved = fillFormFieldValues(inputColor.id, interactive_arguments);
              if (fieldValueSaved != undefined){
                  inputColor.value = fieldValueSaved;
              }

              // Add an event listener to log changes in the color field
              inputColor.addEventListener('change', function() {
                  logFormFieldValues();
              });

              newColumn1.appendChild(labelInputColor);
              newColumn2.appendChild(inputColor);
              newRow.append(newColumn1, newColumn2);
              newDiv.append(newRow);    
          }
          
          // Append the new container to the target element
          const targetElement = document.getElementById('graphGUI');
          targetElement.appendChild(newDiv);
      });
  }
}
