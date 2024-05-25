(function ($) {
    "use strict";
    var primarycolor = getComputedStyle(document.body).getPropertyValue('--primarycolor');

    if ($('#morris-area-chart2').length > 0)
    {
        Morris.Area({
            element: 'morris-area-chart2',
            data: [{
                    period: '2010',
                    SiteA: 0,
                    SiteB: 0,
                }, {
                    period: '2011',
                    SiteA: 130,
                    SiteB: 100,
                }, {
                    period: '2012',
                    SiteA: 80,
                    SiteB: 60,
                }, {
                    period: '2013',
                    SiteA: 70,
                    SiteB: 200,
                }, {
                    period: '2014',
                    SiteA: 180,
                    SiteB: 150,
                }, {
                    period: '2015',
                    SiteA: 105,
                    SiteB: 90,
                },
                {
                    period: '2016',
                    SiteA: 250,
                    SiteB: 150,
                }],
            xkey: 'period',
            ykeys: ['SiteA', 'SiteB'],
            labels: ['Site A', 'Site B'],
            pointSize: 0,
            pointStrokeColors: [primarycolor, '#1e3d73'],
            behaveLikeLine: true,
            gridLineColor: '#e0e0e0',
            lineWidth: 0,
            smooth: false,
            hideHover: 'auto',
            lineColors: [primarycolor, '#1e3d73'],
            resize: true

        });
    }
    if ($('#morris-area-chart').length > 0)
    {
        /// Morris Line chart
        Morris.Area({
            element: 'morris-area-chart',
            data: [{
                    period: '2010',
                    iphone: 50,
                    ipad: 80,
                    itouch: 20
                }, {
                    period: '2011',
                    iphone: 130,
                    ipad: 100,
                    itouch: 80
                }, {
                    period: '2012',
                    iphone: 80,
                    ipad: 60,
                    itouch: 70
                }, {
                    period: '2013',
                    iphone: 70,
                    ipad: 200,
                    itouch: 140
                }, {
                    period: '2014',
                    iphone: 180,
                    ipad: 150,
                    itouch: 140
                }, {
                    period: '2015',
                    iphone: 105,
                    ipad: 100,
                    itouch: 80
                },
                {
                    period: '2016',
                    iphone: 250,
                    ipad: 150,
                    itouch: 200
                }],
            xkey: 'period',
            ykeys: ['iphone', 'ipad', 'itouch'],
            labels: ['iPhone', 'iPad', 'iPod Touch'],
            pointSize: 3,
            fillOpacity: 0,
            pointStrokeColors: [primarycolor, '#6881d6', '#16297b'],
            behaveLikeLine: true,
            gridLineColor: '#e0e0e0',
            lineWidth: 1,
            hideHover: 'auto',
            lineColors: [primarycolor, '#6881d6', '#16297b'],
            resize: true

        });
    }
    if ($('#extra-area-chart').length > 0)
    {
        // Extra chart
        Morris.Area({
            element: 'extra-area-chart',
            data: [{
                    period: '2010',
                    iphone: 0,
                    ipad: 0,
                    itouch: 0
                }, {
                    period: '2011',
                    iphone: 50,
                    ipad: 15,
                    itouch: 5
                }, {
                    period: '2012',
                    iphone: 20,
                    ipad: 50,
                    itouch: 65
                }, {
                    period: '2013',
                    iphone: 60,
                    ipad: 12,
                    itouch: 7
                }, {
                    period: '2014',
                    iphone: 30,
                    ipad: 20,
                    itouch: 120
                }, {
                    period: '2015',
                    iphone: 25,
                    ipad: 80,
                    itouch: 40
                }, {
                    period: '2016',
                    iphone: 10,
                    ipad: 10,
                    itouch: 10
                }
            ],
            lineColors: [primarycolor, '#1e3d73'],
            xkey: 'period',
            ykeys: ['iphone', 'itouch'],
            labels: ['Site A', 'Site B'],
            pointStrokeColors: [primarycolor, '#1e3d73'],
            pointSize: 0,
            lineWidth: 0,
            resize: true,
            behaveLikeLine: true,
            gridLineColor: '#e0e0e0',
            hideHover: 'true'
        });
    }
    if ($('#hero-bar').length > 0)
    {
        $.post("graph_data.php",{type: 'graph'}, function(data, status){
            // Parse the JSON data received from the server
            data = JSON.parse(data);

            // Extract data for the Morris chart from the received data
            var chartData = [];
            data.categories.forEach(function(category) {
                chartData.push({
                    category: category.name,
                    products: category.products
                });
            });

            // Use Morris.Bar to create the graph with the received data
            Morris.Bar({
                element: 'hero-bar',
                data: chartData, // Use the extracted data here
                xkey: 'category',
                ykeys: ['products'],
                labels: ['Products'],
                barRatio: 0.4,
                xLabelAngle: 35,
                hideHover: 'auto',
                barColors: [primarycolor] // Set the bar color to primarycolor
            });
        });
    }
    if ($('#donut-graph').length > 0)
    {
        $.post("graph_data.php", { type: 'donut' }, function(data, status) {
    // Parse the JSON response from the server
    var responseData = JSON.parse(data);
    
    // Extract the categories data from the response
    var categoriesData = responseData.categories;

    // Prepare the data array for the Morris Donut chart
    var donutChartData = [];
    categoriesData.forEach(function(category) {
        donutChartData.push({
            value: category.total_sales,
            label: category.category_name
        });
    });

    // Render the Morris Donut chart with the dynamic data
    Morris.Donut({
        element: 'donut-graph',
        data: donutChartData,
        backgroundColor: primarycolor,
        labelColor: '#060',
        colors: [
            primarycolor,
            '#25b8d6',
            '#2397af',
            '#6bbdce',
            // Add more colors as needed
        ],
        formatter: function(x) {
            return "E"+x;
        }
    });
});


    }

    if ($('#reload-graph').length > 0)
    {
        var nReloads = 0;
        function data(offset) {
            var ret = [];
            for (var x = 0; x <= 360; x += 10) {
                var v = (offset + x) % 360;
                ret.push({
                    x: x,
                    y: Math.sin(Math.PI * v / 180).toFixed(4),
                    z: Math.cos(Math.PI * v / 180).toFixed(4)
                });
            }
            return ret;
        }
        var graph = Morris.Line({
            element: 'reload-graph',
            data: data(0),
            lineColors: [primarycolor, '#0230cb'],
            xkey: 'x',
            ykeys: ['y', 'z'],
            labels: ['sin()', 'cos()'],
            parseTime: false,
            ymin: -1.0,
            ymax: 1.0,
            hideHover: true
        });
        function update() {
            nReloads++;
            graph.setData(data(5 * nReloads));
        }
        setInterval(update, 100);
    }


})(jQuery);
