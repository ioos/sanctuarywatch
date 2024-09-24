class Plot {
    constructor(img, fetchLink, xAxis, yAxis, colors) {
        this.img = img;
        this.plotData = null;  
        this.xAxis = xAxis;
        this.yAxis = yAxis;
        this.colors = colors;
        
        this.dataPromise = new Promise((resolve, reject) => {  
            fetch(fetchLink)
                .then(response => response.json())
                .then(data => {
                    this.plotData = data;
                    resolve(this.plotData);  
                })
                .catch(error => {
                    console.error('Error loading JSON data:', error);
                    reject(error);  
                });
        });

        this.actions = {
            lines: this.makeLines,
            scatter: this.makeScatter,
        };
    }

    timeSeries(data, plotType){
        console.log(data);
        let traces = [];
        let xTrace = data.map(item => item[this.xAxis[0]]);  // Access the key from xAxis
        for (let i = 0; i < this.yAxis.length; i++){
            let temp = {
                x: xTrace,
                y: data.map(item => item[this.yAxis[i]]),
                mode: plotType,
                name: this.yAxis[i], 
                line: {color: colors[i]}
            };
            traces.push(temp);
        }
        console.log(xTrace);
        console.log(traces);
        //dummy stuff for now
        const layout = {
            title: 'Number of Whales and Fish from 2001 to 2023',
            xaxis: { title: 'Year' },
            yaxis: { title: 'Number of Whales and Fish' }
        };
        Plotly.newPlot(this.img, traces, layout);
    }

    async getPlotData() {
        try {
            const data = await this.dataPromise;  
            return data;
        } catch (error) {
            console.error('Error accessing plotData:', error);
        }
    }

    async makeLines() {
        const data = await this.getPlotData();  
        if (data) {
            console.log('Plotting lines with data:', data);
            this.timeSeries(data, 'lines');
        } else {
            console.error('Data not available yet!');
        }
    }

    async makeScatter() {
        const data = await this.getPlotData();  
        if (data) {
            console.log('Plotting scatter with data:', data);
            this.timeSeries(data, 'markers');
        } else {
            console.error('Data not available yet!');
        }
    }

    async execute(action) {
        if (this.actions[action]) {
            await this.actions[action].call(this); // Wait for action to complete if async
        } else {
            console.log("Invalid action");
        }
    }
}

//usage, ideally, the parameters that we pass to initialize the object are extracted from WP meta
x = ['Year']; //have to make sure this is in the data
y = ['Whales', 'Fish']; //have to make sure this is in the data
colors = ['blue', 'green'];
let plotInstance = new Plot('plot', 'test.json', x, y, colors);
plotInstance.execute('lines');