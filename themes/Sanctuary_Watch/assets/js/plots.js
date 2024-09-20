function make_plots(img){

    fetch('http://sanctuarywatch.local/wp-content/uploads/2024/09/test.json')
            .then(response => response.json())
            .then(data => {
                // Extract Year, Whales, and Fish values
                // function arguments:
                // all x-coordinate, y-coordinate things to be plotted: on WP, a slider for how many of each; reflected as list of lists
                // type of plot: lines, markers, bar graph? give them a dropdown
                // graph and axes titles

                const years = data.map(item => item.Year);
                const whales = data.map(item => item.Whales);
                const fish = data.map(item => item.Fish);

                // Create traces for Whales and Fish
                const whaleTrace = {
                    x: years,
                    y: whales,
                    mode: 'lines',
                    name: 'Whales',
                    line: {color: 'blue'}
                };

                const fishTrace = {
                    x: years,
                    y: fish,
                    mode: 'lines',
                    name: 'Fish',
                    line: {color: 'green'}
                };

                // Plot the data using Plotly
                const layout = {
                    title: 'Number of Whales and Fish from 2001 to 2023',
                    xaxis: { title: 'Year' },
                    yaxis: { title: 'Number of Whales and Fish' }
                };

                Plotly.newPlot(img, [whaleTrace, fishTrace], layout);
            })
            .catch(error => console.error('Error loading JSON data:', error));

    // Plotly.newPlot(img, data, layout)
}