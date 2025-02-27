// Code for plotting time series data with a plotly line

import { loadExternalScript } from 'wp-content/plugins/webcr/includes/figure/js/utility.js'; 

async function producePlotlyLineFigure(targetFigureElement){
    try {
        await loadExternalScript('https://cdn.plot.ly/plotly-3.0.0.min.js');

        const rawField = document.getElementsByName("figure_interactive_arguments")[0].value;
        const figureArguments = Object.fromEntries(JSON.parse(rawField));
        const rootURL = window.location.origin;
        const restOfURL = document.getElementsByName("figure_temp_filepath")[0].value;
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
             // console.log(singleLinePlotly);
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


