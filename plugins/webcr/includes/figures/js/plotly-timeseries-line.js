

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


function extractSVGPaths(svgString) {
  // Parse the SVG string into a DOM
  const parser = new DOMParser();
  const svgDoc = parser.parseFromString(svgString, 'image/svg+xml');
  // Select all <path> elements
  const paths = svgDoc.querySelectorAll('path');
  // Return an array of all 'd' attributes
  return Array.from(paths).map(path => path.getAttribute('d'));
}

function iconImportSVGPath(svgFilePath) {
    fetch(svgFilePath)
        .then(response => response.text())
        .then(svgText => {
            const dArray = extractSVGPaths(svgText);
            //console.log(dArray); // Array of all path 'd' strings
            return dArray;
        });
}

function injectOverlays(plotDiv, layout, mainDataTraces, figureArguments) {
    if (!plotDiv || !layout || !layout.yaxis || !layout.yaxis.range) {
        console.warn("[Overlay] Missing layout or y-axis range");
        return;
    }

    layout.xaxis = layout.xaxis || {};
    layout.xaxis.type = 'date';

    layout.yaxis = layout.yaxis || {};
    layout.yaxis.type = 'linear';

    const [yMin, yMax] = layout.yaxis.range || [0, 1];
    const overlays = [];

    for (let i = 1; i <= Number(figureArguments['NumberOfLines']); i++) {
        const base = 'Line' + i;
        const showLegend = figureArguments[base + 'Legend'] === 'on';

        // === Evaluation Period ===
        if (figureArguments[base + 'EvaluationPeriod'] === 'on') {
            let start = figureArguments[base + 'EvaluationPeriodStartDate'];
            let end = figureArguments[base + 'EvaluationPeriodEndDate'];
            console.log(`[Overlay] Processing evaluation period for ${base}:`, start, end);       

            const fillColor = (figureArguments[base + 'EvaluationPeriodFillColor'] || '#999') + '15';

            overlays.push({
                x: [start, end, end, start],
                y: [yMax, yMax, yMin, yMin],
                fill: 'toself',
                fillcolor: fillColor,
                type: 'scatter',
                mode: 'lines',
                line: { color: fillColor, width: 0 },
                hoverinfo: 'skip',
                name: `${figureArguments[base + 'Title']} Evaluation`,
                showlegend: showLegend,
                yaxis: 'y',
                xaxis: 'x'
            });
        }

        // === Event Markers ===
        for (let m = 1; m <= 2; m++) {
            if (figureArguments[base + `EventMarker${m}`] === 'on') {
                let date = figureArguments[base + `EventMarker${m}EventDate`];

                const label = figureArguments[base + `EventMarker${m}EventText`] || `Event ${m}`;
                const color = figureArguments[base + `EventMarker${m}EventColor`] || '#000';

                overlays.push({
                    x: [date, date],
                    y: [yMin, yMax],
                    type: 'scatter',
                    mode: 'lines',
                    line: { color, width: 2 },
                    name: label,
                    showlegend: showLegend,
                    yaxis: 'y',
                    xaxis: 'x',
                    hoverinfo: label,
                });

                console.log(`[Overlay] Added event marker ${m} for ${base}:`, date);
            }
        }
    }
    Plotly.react(plotDiv, [...mainDataTraces, ...overlays], layout);
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
    if (arr.length === 0) return undefined;
    if (arr.length === 1) return arr[0];
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
        //console.log(rawField);
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
            let shapesForLayout = [];

            // Used for graph button functionality NOT USED CURRENTLY
            // let errorVisible = false;
            // let sdVisible = false;
            // let percentileVisible = false;
            // let meanVisible = false;

            // Plotly figure production logic
            for (let i = 1; i <= figureArguments['NumberOfLines']; i++) {
                const targetLineColumn = 'Line' + i;
                const columnXHeader = figureArguments['XAxis'];
                const columnYHeader = figureArguments[targetLineColumn];

                const plotlyX = dataToBePlotted[columnXHeader];
                const plotlyY = dataToBePlotted[columnYHeader];

                const stdDev = computeStandardDeviation(plotlyY);


                //Shows the legend if it is set to 'on' in the figure arguments
                const showLegend = figureArguments[targetLineColumn + 'Legend'];
                if (showLegend === 'on') {
                    var showLegendBool = true;     
                } else {
                    var showLegendBool = false;     
                }


                // //Evaluation period on the graph
                // const showEvalPeriod= figureArguments[targetLineColumn + 'EvaluationPeriod'];
                // if (showEvalPeriod === 'on') {
                //     const evalStartDate = figureArguments[targetLineColumn + 'EvaluationPeriodStartDate'];
                //     const evalEndDate = figureArguments[targetLineColumn + 'EvaluationPeriodEndDate'];
                //     // const yMin = Math.min(...plotlyY.filter(item => item !== ""));   // Or use your data's yMin
                //     // const yMax = Math.max(...plotlyY.filter(item => item !== "")); // Or use your data's yMax
                //     const yMinEval = computePercentile(plotlyY, 10);
                //     const yMaxEval = computePercentile(plotlyY, 90);
                //     const periodHighlight = {
                //     x: [evalStartDate, evalEndDate, evalEndDate, evalStartDate], // Four points
                //     y: [yMaxEval, yMaxEval, yMinEval, yMinEval],                                // Four points
                //     fill: 'toself',
                //     fillcolor: figureArguments[targetLineColumn + 'EvaluationPeriodFillColor'] + '15',
                //     line: { color: 'transparent' },
                //     name: `${figureArguments[targetLineColumn + 'Title']} Evaluation Period`,
                //     type: 'scatter',
                //     hoverinfo: 'skip',
                //     showlegend: showLegendBool,
                //     visible: true,
                //     mode: 'none' // Important: do not show lines or markers
                //     };
                //     allLinesPlotly.push(periodHighlight);
                // } else {}

                // //Event Marker 1 on the graph
                // const showEventMarker1 = figureArguments[targetLineColumn + 'EventMarker1'];
                // if (showEventMarker1 === 'on') {
                //     const EventDate = figureArguments[targetLineColumn + 'EventMarker1EventDate'];
                //     const EventText = figureArguments[targetLineColumn + 'EventMarker1EventText'];
                //     const EventColor = figureArguments[targetLineColumn + 'EventMarker1EventColor'];
                //     const yMinEvent = computePercentile(plotlyY, 10);
                //     const yMaxEvent = computePercentile(plotlyY, 90);
                //     const verticalLineTrace = {
                //         x: [EventDate, EventDate],
                //         y: [yMinEvent, yMaxEvent],
                //         mode: 'lines',
                //         line: { color: EventColor, width: 2, dash: 'solid' },
                //         name: EventText,
                //         showlegend: showLegendBool,
                //         visible: true
                //     };
                //     allLinesPlotly.push(verticalLineTrace);
                // } else {}

                // //Event Marker 2 on the graph
                // const showEventMarker2 = figureArguments[targetLineColumn + 'EventMarker2'];
                // if (showEventMarker2 === 'on') {
                //     const EventDate = figureArguments[targetLineColumn + 'EventMarker2EventDate'];
                //     const EventText = figureArguments[targetLineColumn + 'EventMarker2EventText'];
                //     const EventColor = figureArguments[targetLineColumn + 'EventMarker2EventColor'];
                //     const yMinEvent = computePercentile(plotlyY, 10);
                //     const yMaxEvent = computePercentile(plotlyY, 90);
                //     const verticalLineTrace = {
                //         x: [EventDate, EventDate],
                //         y: [yMinEvent, yMaxEvent],
                //         mode: 'lines',
                //         line: { color: EventColor, width: 2, dash: 'solid' },
                //         name: EventText,
                //         showlegend: showLegendBool,
                //         visible: true
                //     };
                //     allLinesPlotly.push(verticalLineTrace);
                // } else {}
            

                //Show Standard error bars
                const showError = figureArguments[targetLineColumn + 'ErrorBars'];
                const showError_InputValuesOpt = figureArguments[targetLineColumn + 'ErrorBarsInputValues'];
                if (showError === 'on') {
                    //Error bars using Standard Deviation based on dataset Y-axis values (Auto Calculated)
                    if (showError_InputValuesOpt === 'auto') {
                        var errorBarY = {
                            type: 'data',
                            array: new Array(plotlyY.length).fill(stdDev),
                            visible: true,
                            color: figureArguments[targetLineColumn + 'ErrorBarsColor'],
                            thickness: 1.5,
                            width: 8
                        };   
                    }
                    //Error bars (values imported from spreadsheet per point in dataset)
                    //Do we want high and low bounds here?
                    if (showError_InputValuesOpt != 'auto') {
                        const showError_InputValue = dataToBePlotted[showError_InputValuesOpt].filter(item => item !== "");
                        var errorBarY = {
                            type: 'data',
                            array: showError_InputValue.map(val => parseFloat(val)), // Convert to number if needed
                            visible: true,
                            color: figureArguments[targetLineColumn + 'ErrorBarsColor'],
                            thickness: 1.5,
                            width: 8
                        }; 
                    }          
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
                    name: `${figureArguments[targetLineColumn + 'Title']}`,
                    showlegend: showLegendBool,
                    marker: {
                        color: figureArguments[targetLineColumn + 'Color']
                    },
                    error_y: errorBarY,
                    hovertemplate:
                        figureArguments['XAxisTitle'] + ': %{x}<br>' +
                        figureArguments['YAxisTitle'] + ': %{y}'
                };
                allLinesPlotly.push(singleLinePlotly);

                // //Show Standard Deviation Filled/Shaded Area
                // const showSD = figureArguments[targetLineColumn + 'StdDev'];
                // const showSD_InputValuesOpt = figureArguments[targetLineColumn + 'StdDevInputValues'];
                // //Standard Deviation of dataset based on dataset Y-axis values (AutoCalculated)
                // if (showSD == 'on' && showSD_InputValuesOpt === 'auto') {
                //     const upperY = plotlyY.filter(item => item !== "").map(y => y + stdDev);
                //     const lowerY = plotlyY.filter(item => item !== "").map(y => y - stdDev);
                //     const filteredX = plotlyX.filter(item => item !== "");
                //     const stdFill = {
                //         x: [...filteredX, ...filteredX.slice().reverse()],
                //         y: [...upperY, ...lowerY.slice().reverse()],
                //         fill: 'toself',
                //         fillcolor: figureArguments[targetLineColumn + 'Color'] + '33',
                //         line: { color: 'transparent' },
                //         name: `${figureArguments[targetLineColumn + 'Title']} ±1 SD`,
                //         type: 'scatter',
                //         hoverinfo: 'skip',
                //         showlegend: showLegendBool,
                //         visible: true
                //     };
                //     allLinesPlotly.push(stdFill);
                // }
                // //Standard Deviation (values imported from spreadsheet per point in dataset)
                // //Do we want high and low bounds here?
                // if (showSD == 'on' && showSD_InputValuesOpt != 'auto') {
                //     const stdSingleValue = dataToBePlotted[showSD_InputValuesOpt].filter(item => item !== "").reduce((a, b) => a + b, 0) / dataToBePlotted[showSD_InputValuesOpt].length;
                //     const upperY = plotlyY.filter(item => item !== "").map(y => y + stdSingleValue);
                //     const lowerY = plotlyY.filter(item => item !== "").map(y => y - stdSingleValue);
                //     const filteredX = plotlyX.filter(item => item !== "");
                //     const stdFill = {
                //         x: [...filteredX, ...filteredX.slice().reverse()],
                //         y: [...upperY.filter(item => item !== ""), ...lowerY.filter(item => item !== "").slice().reverse()],
                //         fill: 'toself',
                //         fillcolor: figureArguments[targetLineColumn + 'Color'] + '33',
                //         line: { color: 'transparent' },
                //         name: `${figureArguments[targetLineColumn + 'Title']} ±1 SD`,
                //         type: 'scatter',
                //         hoverinfo: 'skip',
                //         showlegend: showLegendBool,
                //         visible: true
                //     };
                //     allLinesPlotly.push(stdFill);
                // }

                //Show Standard Deviation Filled/Shaded Area
                const showSD = figureArguments[targetLineColumn + 'StdDev'];
                const showSD_InputValuesOpt = figureArguments[targetLineColumn + 'StdDevInputValues'];
                //Standard Deviation of dataset based on dataset Y-axis values (AutoCalculated)
                if (showSD == 'on' && showSD_InputValuesOpt === 'auto') {
                    const mean = plotlyY.reduce((a, b) => a + b, 0) / plotlyY.length;
                    const upperY = plotlyY.filter(item => item !== "").map(y => mean + stdDev);
                    const lowerY = plotlyY.filter(item => item !== "").map(y => mean - stdDev);
                    const filteredX = plotlyX.filter(item => item !== "");
                    const stdFill = {
                        x: [...filteredX, ...filteredX.slice().reverse()],
                        y: [...upperY, ...lowerY.slice().reverse()],
                        fill: 'toself',
                        fillcolor: figureArguments[targetLineColumn + 'StdDevColor'] + '27',
                        line: { color: 'transparent' },
                        name: `${figureArguments[targetLineColumn + 'Title']} Mean ±1 SD`,
                        type: 'scatter',
                        hoverinfo: 'skip',
                        showlegend: showLegendBool,
                        visible: true
                    };
                    allLinesPlotly.push(stdFill);
                }
                //Standard Deviation (values imported from spreadsheet per point in dataset)
                //Do we want high and low bounds here?
                if (showSD == 'on' && showSD_InputValuesOpt != 'auto') {
                    const stdSingleValue = dataToBePlotted[showSD_InputValuesOpt].filter(item => item !== "").reduce((a, b) => a + b, 0) / dataToBePlotted[showSD_InputValuesOpt].length;
                    const mean = plotlyY.reduce((a, b) => a + b, 0) / plotlyY.length;
                    const upperY = plotlyY.filter(item => item !== "").map(y => mean + stdSingleValue);
                    const lowerY = plotlyY.filter(item => item !== "").map(y => mean - stdSingleValue);
                    const filteredX = plotlyX.filter(item => item !== "");
                    const stdFill = {
                        x: [...filteredX, ...filteredX.slice().reverse()],
                        y: [...upperY.filter(item => item !== ""), ...lowerY.filter(item => item !== "").slice().reverse()],
                        fill: 'toself',
                        fillcolor: figureArguments[targetLineColumn + 'StdDevColor'] + '27',
                        line: { color: 'transparent' },
                        name: `${figureArguments[targetLineColumn + 'Title']} Mean ±1 SD`,
                        type: 'scatter',
                        hoverinfo: 'skip',
                        showlegend: showLegendBool,
                        visible: true
                    };
                    allLinesPlotly.push(stdFill);
                }
                
                //Percentiles and Mean lines
                const showPercentiles = figureArguments[targetLineColumn + 'Percentiles'];
                const showMean = figureArguments[targetLineColumn + 'Mean'];
                const showMean_ValuesOpt = figureArguments[targetLineColumn + 'MeanField'];
                if (showPercentiles === 'on' || showMean === 'on') {

                    //Calculate Percentiles (Auto Calculated) based on dataset Y-axis values
                    //Do we want to be able to set high and low bounds per point here? (That wouldn't make sense to me)
                    const p10 = computePercentile(plotlyY, 10);
                    const p90 = computePercentile(plotlyY, 90);
                    const filteredX = plotlyX.filter(item => item !== "");
                    const xMinPercentile = Math.min(...filteredX);
                    const xMaxPercentile = Math.max(...filteredX);
                    if (showPercentiles === 'on') {
                        allLinesPlotly.push({
                            x: [xMinPercentile, xMaxPercentile],
                            y: [p10, p10],
                            mode: 'lines',
                            line: { dash: 'dot', color: figureArguments[targetLineColumn + 'Color'] + '60'},
                            name: `${figureArguments[targetLineColumn + 'Title']} 10th Percentile (Bottom)`,
                            type: 'scatter',
                            visible: true,
                            showlegend: false
                        });
                        allLinesPlotly.push({
                            x: [xMinPercentile, xMaxPercentile],
                            y: [p90, p90],
                            mode: 'lines',
                            line: { dash: 'dot', color: figureArguments[targetLineColumn + 'Color'] + '60'},
                            name: `${figureArguments[targetLineColumn + 'Title']} 10th & 90th Percentile`,
                            type: 'scatter',
                            visible: true,
                            showlegend: showLegendBool
                        });
                    }

                    // Calculate mean

                    //Calculate mean (Auto Calculated) based on dataset Y-axis values
                    if (showMean_ValuesOpt === 'auto' && showMean === 'on') {
                        const mean = plotlyY.reduce((a, b) => a + b, 0) / plotlyY.length;
                        const filteredX = plotlyX.filter(item => item !== "");
                        const xMin = Math.min(...filteredX);
                        const xMax = Math.max(...filteredX);
                        allLinesPlotly.push({
                            x: [xMin, xMax],
                            y: [mean, mean],
                            mode: 'lines',
                            line: { dash: 'dash', color: figureArguments[targetLineColumn + 'Color'] + '60'},
                            name: `${figureArguments[targetLineColumn + 'Title']} Mean`,
                            type: 'scatter',
                            visible: true,
                            showlegend: showLegendBool
                        });
                    }
                    //Get mean from the spreadsheet (values imported from spreadsheet per point in dataset)
                    if (showMean_ValuesOpt != 'auto' && showMean === 'on') {
                        const ExistingMeanValue = dataToBePlotted[showMean_ValuesOpt].filter(item => item !== "");
                        const mean = ExistingMeanValue.reduce((a, b) => a + b, 0) / ExistingMeanValue.length;
                        const filteredX = plotlyX.filter(item => item !== "");
                        const xMin = Math.min(...filteredX);
                        const xMax = Math.max(...filteredX);
                        allLinesPlotly.push({
                            x: [xMin, xMax],
                            y: [mean, mean],
                            mode: 'lines',
                            line: { dash: 'dash', color: figureArguments[targetLineColumn + 'Color'] + '60'},
                            name: `${figureArguments[targetLineColumn + 'Title']} Mean`,
                            type: 'scatter',
                            visible: true,
                            showlegend: showLegendBool
                        });
                    }
                }

                //NOT CURRENTLY USED
                //Functions triggered from button in the graph toolbar
                // if (showSD != 'on') {
                //     // Standard deviation shaded area for buttons in graph toolbar
                //     const upperY = plotlyY.map(y => y + stdDev);
                //     const lowerY = plotlyY.map(y => y - stdDev);

                //     const stdFill = {
                //         x: [...plotlyX, ...plotlyX.slice().reverse()],
                //         y: [...upperY, ...lowerY.slice().reverse()],
                //         fill: 'toself',
                //         fillcolor: figureArguments[targetLineColumn + 'Color'] + '33',
                //         line: { color: 'transparent' },
                //         name: `${figureArguments[targetLineColumn + 'Title']} ±1 SD`,
                //         type: 'scatter',
                //         hoverinfo: 'skip',
                //         showlegend: showLegendBool,
                //         visible: false
                //     };
                //     allLinesPlotly.push(stdFill);
                // }

                // if (showPercentiles != 'on' || showMean != 'on') {
                //     // // Add percentile lines or mean line for buttons in graph toolbar
                //     const p10 = computePercentile(plotlyY, 10);
                //     const p90 = computePercentile(plotlyY, 90);
                //     const mean = plotlyY.reduce((a, b) => a + b, 0) / plotlyY.length;
                //     const xMin = Math.min(...plotlyX);
                //     const xMax = Math.max(...plotlyX);

                //     allLinesPlotly.push({
                //         x: [xMin, xMax],
                //         y: [p10, p10],
                //         mode: 'lines',
                //         line: { dash: 'dash', color: figureArguments[targetLineColumn + 'Color'] },
                //         name: `${figureArguments[targetLineColumn + 'Title']} 10th Percentile (Bottom)`,
                //         type: 'scatter',
                //         visible: false,
                //         showlegend: showLegendBool
                //     });

                //     allLinesPlotly.push({
                //         x: [xMin, xMax],
                //         y: [p90, p90],
                //         mode: 'lines',
                //         line: { dash: 'dash', color: figureArguments[targetLineColumn + 'Color'] },
                //         name: `${figureArguments[targetLineColumn + 'Title']} 90th Percentile (Top)`,
                //         type: 'scatter',
                //         visible: false,
                //         showlegend: showLegendBool
                //     });

                //     allLinesPlotly.push({
                //         x: [xMin, xMax],
                //         y: [mean, mean],
                //         mode: 'lines',
                //         line: { dash: 'solid', color: figureArguments[targetLineColumn + 'Color'] },
                //         name: `${figureArguments[targetLineColumn + 'Title']} Mean`,
                //         type: 'scatter',
                //         visible: false,
                //         showlegend: showLegendBool
                //     });
                // }
            }


            //ADMIN SIDE GRAPH DISPLAY SETTINGS
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
                margin: { t: -30, b: -30, l: -30, r: -30 },
                //width: container.clientWidth, 
                //height: container.clientHeight,
                cliponaxis: true
                };
            

            const config = {
            responsive: true,  // This makes the plot resize with the browser window
            renderer: 'svg',
            displayModeBar: true,
            displaylogo: false,
            modeBarButtonsToRemove: [
                'zoom2d', 'lasso2d', 'autoScale2d',
                'hoverClosestCartesian', 'hoverCompareCartesian' //'toImage', 'resetScale2d', 'select2d'
            ],
            
            modeBarButtonsToAdd: [
                // {   
                //     name: 'Standard Error Bars',
                //     icon: Plotly.Icons.autoscale,
                //     click: function(gd) {
                //         errorVisible = !errorVisible;
                //         const indices = gd.data
                //             .map((trace, i) => trace.error_y ? i : null)
                //             .filter(i => i !== null);

                //         Plotly.restyle(gd, { 'error_y.visible': errorVisible }, indices);
                //     }
                // },
                
                // {
                //     name: 'Standard Deviation',
                //     icon: Plotly.Icons.compare,
                //     click: function(gd) {
                //         sdVisible = !sdVisible;
                //         const indices = gd.data
                //             .map((trace, i) => trace.fill === 'toself' ? i : null)
                //             .filter(i => i !== null);

                //         Plotly.restyle(gd, { visible: sdVisible }, indices);
                //     }
                // },
                // {
                //     name: 'Mean',
                //     icon: Plotly.Icons.line,
                //     click: function(gd) {
                //         meanVisible = !meanVisible;
                //         const indices = gd.data
                //             .map((trace, i) => ['Mean'].includes(trace.name) ? i : null)
                //             .filter(i => i !== null);

                //         Plotly.restyle(gd, { visible: meanVisible }, indices);
                //     }
                // },
                // {
                //     name: 'Percentiles',
                //     icon: Plotly.Icons.line,
                //     click: function(gd) {
                //         percentileVisible = !percentileVisible;
                //         const indices = gd.data
                //             .map((trace, i) => ['90th Percentile (Top)', '10th Percentile (Bottom)'].includes(trace.name) ? i : null)
                //             .filter(i => i !== null);

                //         Plotly.restyle(gd, { visible: percentileVisible }, indices);
                //     }
                // }
            ]
            };

            // Set up the plotlyDiv (The div the the plot will be rendered in)
            const plotDiv = document.getElementById(plotlyDivID);         
            plotDiv.style.setProperty("width", "100%", "important");
            plotDiv.style.setProperty("max-width", "none", "important");

                            
            // Create the plot with all lines
            //await Plotly.newPlot(plotlyDivID, allLinesPlotly, layout, config);
            Plotly.newPlot(plotDiv, allLinesPlotly, layout, config).then(() => {
                // After the plot is created, inject overlays if any, this is here because you can only get overlays that span the entire yaxis after the graph has been rendered.
                //You need the specific values for the entire yaxis
                injectOverlays(plotDiv, layout, allLinesPlotly, figureArguments);
            });

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

              

            //Add checkboxes for error bars, standard deviation, mean, and percentiles
            const features = ["Legend", "ErrorBars", "StdDev", "Mean", "Percentiles", "EvaluationPeriod", "EventMarker1", "EventMarker2"];
            const featureNames = ["Graph Legend", "Error Bars", "Standard Deviation Fill", "Mean Line", "90th & 10th Percentile Lines (Auto Calculated)", "Evaluation Period", "Event Marker 1", "Event Marker 2"];
            for (let i = 0; i < features.length; i++) {
                const feature = features[i];
                const featureName = featureNames[i];

                let newRow = document.createElement("div");
                newRow.classList.add("row", "fieldPadding");
                if (fieldLabelNumber % 2 != 0) {
                    newRow.classList.add("row", "fieldBackgroundColor");
                }

                let newColumn1 = document.createElement("div");
                newColumn1.classList.add("col-3");
                let newColumn2 = document.createElement("div");
                newColumn2.classList.add("col");

                let label = document.createElement("label");
                label.for = fieldLabel[0] + feature;
                label.innerHTML = `${featureName} Visible?`;
                let checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.id = fieldLabel[0] + feature;
                checkbox.name = "plotFields";

                let fieldValueSaved = fillFormFieldValues(checkbox.id, interactive_arguments);
                checkbox.value = fieldValueSaved === 'on' ? 'on' : "";
                checkbox.checked = fieldValueSaved === 'on';

                newColumn1.appendChild(label);
                newColumn2.appendChild(checkbox);
                newRow.append(newColumn1, newColumn2);
                newDiv.append(newRow);

                // === Add dropdowns for feature-specific data ===
                if (["Mean", "ErrorBars", "StdDev", "EvaluationPeriod", "EventMarker1", "EventMarker2"].includes(feature)) {
                    const dropdownContainer = document.createElement("div");
                    dropdownContainer.classList.add("row", "fieldPadding");
                    if (fieldLabelNumber % 2 != 0) {
                        dropdownContainer.classList.add("row", "fieldBackgroundColor");
                    }

                    const dropdownLabelCol = document.createElement("div");
                    dropdownLabelCol.classList.add("col-3");
                    const dropdownInputCol = document.createElement("div");
                    dropdownInputCol.classList.add("col");

                    function createDropdown(labelText, selectId) {
                        const label = document.createElement("label");
                        label.innerHTML = labelText;
                        const select = document.createElement("select");
                        select.id = selectId;
                        select.name = "plotFields";

                        if (feature === "Mean" || feature === "ErrorBars" || feature === "StdDev") {
                            const autoOpt = document.createElement("option");
                            autoOpt.value = "auto";
                            autoOpt.innerHTML = "Auto Calculate";
                            select.appendChild(autoOpt);

                        for (let col of Object.values(jsonColumns)) {
                            const opt = document.createElement("option");
                            opt.value = col;
                            opt.innerHTML = col;
                            select.appendChild(opt);
                        }

                        const saved = fillFormFieldValues(select.id, interactive_arguments);
                        if (saved) select.value = saved;

                        select.addEventListener("change", logFormFieldValues);
                        return { label, select };
                        }

                    }

                    function createDatefield(labelText, inputId) {
                        const label = document.createElement("label");
                        label.textContent = labelText;
                        label.htmlFor = inputId; // Link label to input

                        const input = document.createElement("input"); // Correct element
                        input.type = "date";
                        input.id = inputId;
                        input.name = "plotFields";

                        const saved = fillFormFieldValues(input.id, interactive_arguments);
                        if (saved) input.value = saved;

                        input.addEventListener("change", logFormFieldValues);
                        return { label, input };
                    }

                    function createTextfield(labelText, inputId) {
                        const label = document.createElement("label");
                        label.textContent = labelText;
                        label.htmlFor = inputId; // Link label to input

                        const input = document.createElement("input"); // Correct element
                        input.type = "text";
                        input.id = inputId;
                        input.name = "plotFields";
                        input.style.width = "200px";


                        const saved = fillFormFieldValues(input.id, interactive_arguments);
                        if (saved) input.value = saved;

                        input.addEventListener("change", logFormFieldValues);
                        return { label, input };
                    }

                    function createColorfield(labelText, inputId) {
                        const label = document.createElement("label");
                        label.textContent = labelText;
                        label.htmlFor = inputId; // Link label to input

                        const input = document.createElement("input"); // Correct element
                        input.type = "color";
                        input.id = inputId;
                        input.name = "plotFields";

                        const saved = fillFormFieldValues(input.id, interactive_arguments);
                        if (saved) input.value = saved;

                        input.addEventListener("change", logFormFieldValues);
                        return { label, input };
                    }

                    const controls = [];

                    if (feature === "Mean") {
                        const { label, select } = createDropdown("Mean Source Column", fieldLabel[0] + feature + "Field");
                        controls.push(label, select);
                    }

                    if (feature === "ErrorBars" || feature === "StdDev") {
                        const { label: labelValues, select: selectValues } = createDropdown(`${featureName} Input Column Values`, fieldLabel[0] + feature + "InputValues");
                        const { label: labelColor, input: ColorValue } = createColorfield(`Color`, fieldLabel[0] + feature + "Color");
                        controls.push(labelValues, document.createElement('br'), selectValues, document.createElement('br'), labelColor, document.createElement('br'), ColorValue);
                    }

                    if (feature === "EvaluationPeriod") {
                        const { label: labelStartDate, input: StartDateValues } = createDatefield(`Start Date`, fieldLabel[0] + feature + "StartDate");
                        const { label: labelEndDate, input: EndDateValues } = createDatefield('End Date', fieldLabel[0] + feature + "EndDate");
                        const { label: labelColor, input: ColorValue } = createColorfield(`Fill Color`, fieldLabel[0] + feature + "FillColor");
                        controls.push(labelStartDate, document.createElement('br'), StartDateValues, document.createElement('br'), labelEndDate, document.createElement('br'), EndDateValues, document.createElement('br'), labelColor, document.createElement('br'), ColorValue);
                    }

                    if (feature === "EventMarker1") {
                        const { label: labelEventDate1, input: EventDateValue1 } = createDatefield(`Event Date`, fieldLabel[0] + feature + "EventDate");
                        const { label: labelEventText1, input: EventTextValue1 } = createTextfield(`Display Text`, fieldLabel[0] + feature + "EventText");
                        const { label: labelEventColor1, input: EventColorValue1 } = createColorfield(`Line Color`, fieldLabel[0] + feature + "EventColor");
                        controls.push(labelEventDate1, document.createElement('br'), EventDateValue1, document.createElement('br'), labelEventText1, document.createElement('br'), EventTextValue1, document.createElement('br'), labelEventColor1, document.createElement('br'), EventColorValue1);
                    }

                    if (feature === "EventMarker2") {
                        const { label: labelEventDate2, input: EventDateValue2 } = createDatefield(`Event Date`, fieldLabel[0] + feature + "EventDate");
                        const { label: labelEventText2, input: EventTextValue2 } = createTextfield(`Display Text`, fieldLabel[0] + feature + "EventText");
                        const { label: labelEventColor2, input: EventColorValue2 } = createColorfield(`Line Color`, fieldLabel[0] + feature + "EventColor");
                        controls.push(labelEventDate2, document.createElement('br'),EventDateValue2, document.createElement('br'), labelEventText2, document.createElement('br'), EventTextValue2,  document.createElement('br'), labelEventColor2, document.createElement('br'), EventColorValue2);
                    }



                    // Initially hide the dropdown container
                    dropdownContainer.style.display = checkbox.checked ? "flex" : "none";

                    controls.forEach(control => dropdownInputCol.appendChild(control));
                    dropdownContainer.append(dropdownLabelCol, dropdownInputCol);
                    newDiv.append(dropdownContainer);

                    // Toggle visibility dynamically
                    checkbox.addEventListener('change', function () {
                        checkbox.value = checkbox.checked ? 'on' : "";
                        dropdownContainer.style.display = checkbox.checked ? "flex" : "none";
                        logFormFieldValues();
                    });
                } else {
                    checkbox.addEventListener('change', function () {
                        checkbox.value = checkbox.checked ? 'on' : "";
                        logFormFieldValues();
                    });
                }
            }
            
          }
        

          const targetElement = document.getElementById('graphGUI');
          targetElement.appendChild(newDiv);
      });
  }
}
