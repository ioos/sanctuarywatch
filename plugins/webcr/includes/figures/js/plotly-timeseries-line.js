

let plotlyScriptPromise = null;

function loadPlotlyScript() {
    if (window.Plotly) return Promise.resolve();

    // Reuse the same Promise if already started
    if (plotlyScriptPromise) return plotlyScriptPromise;

    plotlyScriptPromise = new Promise((resolve, reject) => {
        const existingScript = document.querySelector('script[src="https://cdn.plot.ly/plotly-3.0.0.min.js"]');
        if (existingScript) {
            existingScript.onload = () => {
                if (window.Plotly) resolve();
                else reject(new Error("Plotly failed to initialize."));
            };
            existingScript.onerror = reject;
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://cdn.plot.ly/plotly-3.0.0.min.js';
        script.onload = () => {
            if (window.Plotly) resolve();
            else reject(new Error("Plotly failed to initialize."));
        };
        script.onerror = reject;
        document.head.appendChild(script);
    });

    return plotlyScriptPromise;
}


function waitForElementById(id, timeout = 1000) {
    return new Promise((resolve, reject) => {
        const intervalTime = 50;
        let elapsedTime = 0;

        const interval = setInterval(() => {
            const element = document.getElementById(id);
            if (element) {
                clearInterval(interval);
                resolve(element);
            }
            elapsedTime += intervalTime;
            if (elapsedTime >= timeout) {
                clearInterval(interval);
                reject(new Error(`Element with id ${id} not found after ${timeout}ms`));
            }
        }, intervalTime);
    });
}

function computeStandardDeviation(arr) {
    const n = arr.length;
    const mean = arr.reduce((a, b) => a + b, 0) / n;
    const variance = arr.reduce((acc, val) => acc + (val - mean) ** 2, 0) / n;
    return Math.sqrt(variance);
}

function computePercentile(arr, percentile) {
    const sorted = [...arr].sort((a, b) => a - b);
    const index = (percentile / 100) * (sorted.length - 1);
    const lower = Math.floor(index);
    const upper = Math.ceil(index);
    if (lower === upper) return sorted[lower];
    return sorted[lower] + (index - lower) * (sorted[upper] - sorted[lower]);
}

async function producePlotlyLineFigure(targetFigureElement, interactive_arguments, postID){
    try {
        await loadPlotlyScript(); // ensures Plotly is ready

        const rawField = interactive_arguments;
        console.log(rawField);
        const figureArguments = Object.fromEntries(JSON.parse(rawField));
        const rootURL = window.location.origin;

        //Rest call to get uploaded_path_json
        if (postID == null) {
            // ADMIN SIDE POST ID GRAB
            figureID = document.getElementsByName("post_ID")[0].value;
            //console.log("figureID ADMIN:", figureID);
        }
        if (postID != null) {
            // THEME SIDE POST ID GRAB
            figureID = postID;
            //console.log("figureID THEME:", figureID);
        }

        // in fetch_tab_info in script.js, await render_tab_info & await new Promise were added to give each run of producePlotlyLineFigure a chance to finish running before the next one kicked off
        // producePlotlyLineFigure used to fail here because the script was running before the previous iteration finished. 
        const figureRestCall = `${rootURL}/wp-json/wp/v2/figure/${figureID}?_fields=uploaded_path_json`;
        const response = await fetch(figureRestCall);

        const data = await response.json();
        const uploaded_path_json = data.uploaded_path_json;

        const restOfURL = "/wp-content" + uploaded_path_json.split("wp-content")[1];
        const finalURL = rootURL + restOfURL;
        
        const rawResponse = await fetch(finalURL);
        if (!rawResponse.ok) {
            throw new Error('Network response was not ok');
        }
        
        const responseJson = await rawResponse.json();
        const dataToBePlotted = responseJson.data;

        let newDiv = document.createElement('div');
        const plotlyDivID = `plotlyFigure${figureID}`;
        newDiv.id = plotlyDivID
        newDiv.classList.add("container", `figure_interactive${figureID}`);

        const targetElementparts = targetFigureElement.split("_");
        const targetElementpostID = targetElementparts[targetElementparts.length - 1];

        if (figureID == targetElementpostID) {

            //console.log(`Figure ID ${figureID} matches target element post ID ${targetElementpostID}`) ;            
            // const targetElement = document.getElementById(targetFigureElement);
            const targetElement = await waitForElementById(targetFigureElement);
            targetElement.appendChild(newDiv);
            
            const numLines = figureArguments['NumberOfLines'];

            let plotlyX;
            let plotlyY;
            let columnXHeader;
            let columnYHeader;
            let targetLineColumn;
            let singleLinePlotly;
            let allLinesPlotly = [];
            let errorVisible = false;
            let sdVisible = false;
            let percentileVisible = false;
            let meanVisible = false;

            // Plotly figure production logic
            for (let i = 1; i <= figureArguments['NumberOfLines']; i++) {
                const targetLineColumn = 'Line' + i;
                const columnXHeader = figureArguments['XAxis'];
                const columnYHeader = figureArguments[targetLineColumn];

                const plotlyX = dataToBePlotted[columnXHeader];
                const plotlyY = dataToBePlotted[columnYHeader];

                const stdDev = computeStandardDeviation(plotlyY);

                const showSD = figureArguments[targetLineColumn + 'StdDev'];
                const showMean = figureArguments[targetLineColumn + 'Mean'];
                const showPercentiles = figureArguments[targetLineColumn + 'Percentiles'];
                const showError = figureArguments[targetLineColumn + 'ErrorBars'];

                //Standard error bars
                if (showError === 'on') {
                    var errorBarY = {
                        type: 'data',
                        array: new Array(plotlyY.length).fill(stdDev),
                        visible: true,
                        color: figureArguments[targetLineColumn + 'Color'],
                        thickness: 1.5,
                        width: 8
                    };       
                }
                if (showError != 'on') {
                    var errorBarY = {};
                }

                // Main line with or w/o error bars
                const singleLinePlotly = {
                    x: plotlyX,
                    y: plotlyY,
                    mode: 'lines+markers',
                    type: 'scatter',
                    name: 'Data',
                    showlegend: false,
                    marker: {
                        color: figureArguments[targetLineColumn + 'Color']
                    },
                    name: figureArguments[targetLineColumn + 'Title'],
                    error_y: errorBarY,
                    hovertemplate:
                        figureArguments['XAxisTitle'] + ': %{x}<br>' +
                        figureArguments['YAxisTitle'] + ': %{y}'
                };
                allLinesPlotly.push(singleLinePlotly);

                //Standard Deviation shaded area
                if (showSD == 'on') {
                    const upperY = plotlyY.map(y => y + stdDev);
                    const lowerY = plotlyY.map(y => y - stdDev);
                    const stdFill = {
                        x: [...plotlyX, ...plotlyX.slice().reverse()],
                        y: [...upperY, ...lowerY.slice().reverse()],
                        fill: 'toself',
                        fillcolor: figureArguments[targetLineColumn + 'Color'] + '33',
                        line: { color: 'transparent' },
                        name: `${figureArguments[targetLineColumn + 'Title']} ±1 SD`,
                        type: 'scatter',
                        hoverinfo: 'skip',
                        showlegend: true,
                        visible: true
                    };
                    allLinesPlotly.push(stdFill);
                }
                
                //Percentiles and Mean lines
                if (showPercentiles === 'on' || showMean === 'on') {
                    const p10 = computePercentile(plotlyY, 10);
                    const p90 = computePercentile(plotlyY, 90);
                    const mean = plotlyY.reduce((a, b) => a + b, 0) / plotlyY.length;
                    const xMin = Math.min(...plotlyX);
                    const xMax = Math.max(...plotlyX);

                    if (showPercentiles === 'on') {
                        allLinesPlotly.push({
                            x: [xMin, xMax],
                            y: [p10, p10],
                            mode: 'lines',
                            line: { dash: 'dash', color: 'gray' },
                            name: '10th Percentile (Bottom)',
                            type: 'scatter',
                            visible: true
                        });
                        allLinesPlotly.push({
                            x: [xMin, xMax],
                            y: [p90, p90],
                            mode: 'lines',
                            line: { dash: 'dash', color: 'gray' },
                            name: '90th Percentile (Top)',
                            type: 'scatter',
                            visible: true
                        });
                    }

                    if (showMean === 'on') {
                        allLinesPlotly.push({
                            x: [xMin, xMax],
                            y: [mean, mean],
                            mode: 'lines',
                            line: { dash: 'solid', color: 'red' },
                            name: 'Mean',
                            type: 'scatter',
                            visible: true
                        });
                    }
                }


                //Functions triggered from button in the graph toolbar
                if (showSD != 'on') {
                    // Standard deviation shaded area for buttons in graph toolbar
                    const upperY = plotlyY.map(y => y + stdDev);
                    const lowerY = plotlyY.map(y => y - stdDev);

                    const stdFill = {
                        x: [...plotlyX, ...plotlyX.slice().reverse()],
                        y: [...upperY, ...lowerY.slice().reverse()],
                        fill: 'toself',
                        fillcolor: figureArguments[targetLineColumn + 'Color'] + '33',
                        line: { color: 'transparent' },
                        name: `${figureArguments[targetLineColumn + 'Title']} ±1 SD`,
                        type: 'scatter',
                        hoverinfo: 'skip',
                        showlegend: true,
                        visible: false
                    };
                    allLinesPlotly.push(stdFill);
                }

                if (showPercentiles != 'on' || showMean != 'on') {
                    // // Add percentile lines or mean line for buttons in graph toolbar
                    const p10 = computePercentile(plotlyY, 10);
                    const p90 = computePercentile(plotlyY, 90);
                    const mean = plotlyY.reduce((a, b) => a + b, 0) / plotlyY.length;
                    const xMin = Math.min(...plotlyX);
                    const xMax = Math.max(...plotlyX);

                    allLinesPlotly.push({
                        x: [xMin, xMax],
                        y: [p10, p10],
                        mode: 'lines',
                        line: { dash: 'dash', color: 'gray' },
                        name: '10th Percentile (Bottom)',
                        type: 'scatter',
                        visible: false
                    });

                    allLinesPlotly.push({
                        x: [xMin, xMax],
                        y: [p90, p90],
                        mode: 'lines',
                        line: { dash: 'dash', color: 'gray' },
                        name: '90th Percentile (Top)',
                        type: 'scatter',
                        visible: false
                    });

                    allLinesPlotly.push({
                        x: [xMin, xMax],
                        y: [mean, mean],
                        mode: 'lines',
                        line: { dash: 'solid', color: 'red' },
                        name: 'Mean',
                        type: 'scatter',
                        visible: false
                    });
                }
            }


            //ADMIN SIDE GRAPH DISPLAY SETTINGS
            if (window.location.href.includes("wp-admin/post.php")) {

                var layout = {}
                
                await Plotly.newPlot(plotlyDivID, allLinesPlotly, layout, config);

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
                    legend: {
                    orientation: 'h',       // horizontal layout
                    y: 1.1,                 // position legend above the plot
                    x: 0.5,                 // center the legend
                    xanchor: 'center',
                    yanchor: 'bottom'
                    },
                    autosize: true,
                    //margin: { t: 30, b: 50, l: 50, r: 30 },
                    //width: container.clientWidth, 
                    //height: container.clientHeight,
                    cliponaxis: true
                    };
                
                // const extractSvgPath_mean = require(`${rootURL}wp-content/plugins/webcr/includes/figures/js/custom_icons`);
                // const pathData_mean = extractSvgPath_mean('icon_mean-01.svg');
                // var icon_Mean = {
                //     //width: 500,
                //     //height: 600,
                //     path: pathData_mean,
                // }
                const config = {
                responsive: true,  // This makes the plot resize with the browser window
                renderer: 'svg',
                displayModeBar: true,
                // modeBarButtons: [
                //     'zoom2d', 'select2d', 'lasso2d', 'autoScale2d', 'resetScale2d',
                //     'hoverClosestCartesian', 'hoverCompareCartesian', 'toImage'
                // ],
                
                modeBarButtonsToAdd: [
                    {
                        name: 'Standard Error Bars',
                        icon: Plotly.Icons.autoscale,
                        click: function(gd) {
                            errorVisible = !errorVisible;
                            const indices = gd.data
                                .map((trace, i) => trace.error_y ? i : null)
                                .filter(i => i !== null);

                            Plotly.restyle(gd, { 'error_y.visible': errorVisible }, indices);
                        }
                    },
                    {
                        name: 'Standard Deviation',
                        icon: Plotly.Icons.compare,
                        click: function(gd) {
                            sdVisible = !sdVisible;
                            const indices = gd.data
                                .map((trace, i) => trace.fill === 'toself' ? i : null)
                                .filter(i => i !== null);

                            Plotly.restyle(gd, { visible: sdVisible }, indices);
                        }
                    },
                    {
                        name: 'Mean',
                        icon: Plotly.Icons.line,
                        click: function(gd) {
                            meanVisible = !meanVisible;
                            const indices = gd.data
                                .map((trace, i) => ['Mean'].includes(trace.name) ? i : null)
                                .filter(i => i !== null);

                            Plotly.restyle(gd, { visible: meanVisible }, indices);
                        }
                    },
                    {
                        name: 'Percentiles',
                        icon: Plotly.Icons.line,
                        click: function(gd) {
                            percentileVisible = !percentileVisible;
                            const indices = gd.data
                                .map((trace, i) => ['90th Percentile (Top)', '10th Percentile (Bottom)'].includes(trace.name) ? i : null)
                                .filter(i => i !== null);

                            Plotly.restyle(gd, { visible: percentileVisible }, indices);
                        }
                    }
                ]
                };

                const plotDiv = document.getElementById(plotlyDivID);         
                plotDiv.style.setProperty("width", "100%", "important");
                plotDiv.style.setProperty("max-width", "none", "important");
                
                await Plotly.newPlot(plotlyDivID, allLinesPlotly, layout, config);

                // Constrain inner .svg-container to match parent
                const svgContainer = plotDiv?.querySelector('.svg-container');
                if (svgContainer) {
                    svgContainer.style.width = '100%';
                    svgContainer.style.maxWidth = '100%';
                    svgContainer.style.boxSizing = 'border-box'; // prevent overflow
                    svgContainer.style.overflow = 'hidden';
                }

            }
        } else {}
    } catch (error) {
        console.error('Error loading scripts:', error);
    }
}

function plotlyLineParameterFields(jsonColumns, interactive_arguments){

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
      fieldValueSaved = fillFormFieldValues(inputAxisTitle.id, interactive_arguments);
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
          fieldValueSaved = fillFormFieldValues(inputBound.id, interactive_arguments);
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
      displayLineFields(selectNumberLines.value, jsonColumns, interactive_arguments) });
  selectNumberLines.addEventListener('change', function() {
          logFormFieldValues();
      });

  for (let i = 1; i < 7; i++){
      let selectNumberLinesOption = document.createElement("option");
      selectNumberLinesOption.value = i;
      selectNumberLinesOption.innerHTML = i; 
      selectNumberLines.appendChild(selectNumberLinesOption);
  }
  fieldValueSaved = fillFormFieldValues(selectNumberLines.id, interactive_arguments);
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
  fieldValueSaved = fillFormFieldValues(selectXAxisFormat.id, interactive_arguments);
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
  displayLineFields(selectNumberLines.value, jsonColumns, interactive_arguments);
}


// generate the form fields needed for users to indicate preferences for how a figure should appear 
function displayLineFields (numLines, jsonColumns, interactive_arguments) {
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
          fieldValueSaved = fillFormFieldValues(selectColumn.id, interactive_arguments);
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
              fieldValueSaved = fillFormFieldValues(inputTitle.id, interactive_arguments);
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
              fieldValueSaved = fillFormFieldValues(inputColor.id, interactive_arguments);
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

              // Add checkboxes for error bars, standard deviation, mean, and percentiles
              const features = ["ErrorBars", "StdDev", "Mean", "Percentiles"];
              features.forEach(feature => {
                newRow = document.createElement("div");
                newRow.classList.add("row", "fieldPadding");
                if (fieldLabelNumber % 2 != 0 ){
                    newRow.classList.add("row", "fieldBackgroundColor");
                }
                newColumn1 = document.createElement("div");
                newColumn1.classList.add("col-3");
                newColumn2 = document.createElement("div");
                newColumn2.classList.add("col");

                let label = document.createElement("label");
                label.for = fieldLabel[0] + feature;
                label.innerHTML = `${feature} Visible?`;
                let checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.id = fieldLabel[0] + feature;
                checkbox.name = "plotFields";

                fieldValueSaved = fillFormFieldValues(checkbox.id, interactive_arguments);

                if (checkbox.id != '' && checkbox.value != 'no') { //prevent [ "", "no" ] data field error.

                    if (fieldValueSaved === 'on'){
                        checkbox.value = fieldValueSaved;
                        checkbox.checked = true;
                    }
                    if (fieldValueSaved === ""){
                        checkbox.value = fieldValueSaved;
                        checkbox.checked = false;
                    }
                    if (fieldValueSaved === undefined){
                        checkbox.value = "";
                        checkbox.checked = false;
                    }

                    checkbox.addEventListener('change', function() {
                        checkbox.value = checkbox.checked ? 'on' : "";  // Store "on" if checked, "" if not
                        logFormFieldValues();
                    });
                }

                newColumn1.appendChild(label);
                newColumn2.appendChild(checkbox);
                newRow.append(newColumn1, newColumn2);
                newDiv.append(newRow);
              });
          }

          const targetElement = document.getElementById('graphGUI');
          targetElement.appendChild(newDiv);
      });
  }
}
