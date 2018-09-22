<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Global Health Analytics</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="author" content="Zheng Bing Bing" />
		<link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/workforce.css">
		<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
        <script src="http://labratrevenge.com/d3-tip/javascripts/d3.tip.v0.6.3.js"></script>
        <link rel="shortcut icon" href="images/favicon.ico">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link href='http://fonts.googleapis.com/css?family=Milonga' rel='stylesheet' type='text/css'>

</head>

<body>
<?php $country = $_REQUEST['country']; ?>
    <div class="container2"> 
    <!--Navigation -->  
    <div class ="toppic">
    <img alt="title" src="images/toppic.png" />
    </div>
	<div class="navigation">
    <div class="nav">
	<ul>
  	<li ><a href="index.php">Home</a></li>
  	<li class="active"><a href="workforce.php">Workforce</a></li>
    <li><a href="disease.php">Disease</a></li>
    <li><a href="pupmed.html">PubMed</a></li>
    <li><a href="network.html">Network Diagram</a></li>
    <li><a href="ebola3.html">Ebola</a></li>
	<li><a href="rshiny.html">R Shiny</a></li>
    <li><a href="aboutus.html">About</a></li>
	</ul> 
    </div>
    </div>
    </div>

        <div id="content">
	    <div id="Ctitle">
    <h2><?php echo $country?></h2>
    </div>
        <div id="chart0">

        </div>
    <div id="chart1">
        <h4 id="title">Life Expectancy By Age</h4>
    </div>

    <div id="chart2">
        <div class="outer">
        <h4 id="title">World Life Expectancy</h4>
     <div id="inner">
    </div>
    </div>
    </div>        
      <div id="chart3">  
        <h4 id="title">Line Chart Life Expectancy</h4>
    </div>
        <div id="chart4">
        <h4 id="title">Health Workers and Health Facilities</h4>
	</div>    
          
    </div>
    </body>
    <script src="http://d3js.org/d3.v3.js"></script>
    <script src="http://labratrevenge.com/d3-tip/javascripts/d3.tip.v0.6.3.js"></script>
<script>
    var countryName = "<?php Print($country); ?>";
//chart0
    d3.json("/workforce/healthData.php?country="+countryName, function (error, data) {

        function tabulate(data, columns) {
            var table = d3.select('#chart0').append('table')
            var thead = table.append('thead')
            var tbody = table.append('tbody');

            // append the header row
            thead.append('tr')
              .selectAll('th')
              .data(columns).enter()
              .append('th')
                .text(function (column) { return column; });

            // create a row for each object in the data
            var rows = tbody.selectAll('tr')
              .data(data)
              .enter()
              .append('tr');

            // create a cell in each row for each column
            var cells = rows.selectAll('td')
              .data(function (row) {
                  return columns.map(function (column) {
                      return { column: column, value: row[column] };
                  });
              })
              .enter()
              .append('td')
                .text(function (d) { return d.value; });

            return table;
        }

        // render the table(s)
        tabulate(data, ['countryName', 'Population', 'Health_Facilities', 'Land_Area', 'Service_Availability', 'Population_Density', 'Average_People_Per_Health_Facility', 'World_Rank']); // 2 column table

    });
    //chart 1
var margin = { top: 30, right: 70, bottom: 30, left: 50 },
        width = 400 - margin.left - margin.right,
        height = 270 - margin.top - margin.bottom;

var tip = d3.tip()
  .attr('class', 'd3-tip')
  .offset([-10, 0])
  .html(function(d) {
    return "<p class='hover'>Male</p> </br> <strong>Age:</strong> <span style='color:white'>" + d.age + "</br><strong>Life Expectancy:</strong> <span style='color:white'>" + d.lifeexpectancymale + "%" + "</span></br><strong>World Rank:</strong> <span style='color:white'>" + d.worldrankmale;
  });

var tip1 = d3.tip()
  .attr('class', 'd3-tip')
  .offset([-10, 0])
  .html(function(d) {
    return "<p class='hover'>Female</p> <strong>Age:</strong> <span style='color:white'>" + d.age + "</br><strong>Life Expectancy:</strong> <span style='color:white'>" + d.lifeexpectancyfemale + "%" + "</span></br><strong>World Rank:</strong> <span style='color:white'>" + d.worldrankfemale;
  });
  
    var x = d3.scale.linear()
        .range([0, width]);

    var y = d3.scale.linear()
        .range([height, 0]);

    var xAxis = d3.svg.axis()
        .scale(x)
        .orient("bottom").ticks(5);

    var yAxis = d3.svg.axis()
        .scale(y)
        .orient("left").ticks(5);

    var line = d3.svg.line()
        .x(function (d) { return x(d.age); })
        .y(function (d) { return y(d.lifeexpectancymale); });

    var line2 = d3.svg.line()
   	 	.x(function (d) { return x(d.age); })
    	.y(function (d) { return y(d.lifeexpectancyfemale); });
		
    var svg = d3.select("#chart1").append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
      .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	svg.call(tip).call(tip1);
   
	
    d3.json("/workforce/ageData.php?country="+countryName, function (error, data) {
        data.forEach(function (d)
        {
   		    d.age = +d.age;
            d.lifeexpectancymale  = +d.lifeexpectancymale;
            d.lifeexpectancyfemale = +d.lifeexpectancyfemale;
        });

		x.domain(d3.extent(data, function (d) {
  		  return d.age;
		}));
		y.domain([d3.min(data, function (d) { return(d.lifeexpectancymale < d.lifeexpectancyfemale) ? d.lifeexpectancymale : d.lifeexpectancyfemale;}), d3.max(data, function (d) { return(d.lifeexpectancymale < d.lifeexpectancyfemale) ? d.lifeexpectancymale : d.lifexpectancyfemale}) + 10  ]);

        svg.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + height + ")")
            .call(xAxis);

        svg.append("text")      // text label for the x axis
          .attr("x", 155)
          .attr("y", 235)
          .style("text-anchor", "middle")
          .text("Age");

        svg.append("g")
            .attr("class", "y axis")
            .call(yAxis)
          .append("text")
            .attr("transform", "rotate(-90)")
            .attr("y", 6)
            .attr("dy", ".71em")
            .style("text-anchor", "end")
            .text("Life Expectancy By Age(%)");

		svg.append("path")
    		.datum(data)
   			.attr("class", "line")
   			.style("stroke", "red")
   		    .attr("d", line(data));
	
		svg.append("path")
   		    .datum(data)
    		.attr("class", "line")
			.style("stroke", "black")
    		.attr("d", line2(data));
		
		svg.selectAll('.yaxis1')
    		.data(data)
    		.enter()
    		.append('circle')
    		.attr('class', 'yaxis1')
   			.attr('cx', function (datum) {
    		return x(datum.age)
			})
    		.attr('cy', function (datum) {
    		return y(datum.lifeexpectancymale)
			})
    		.attr('r', 3)
    		.attr('fill', 'red')
    		.on('mouseover', tip.show)
    		.on('mouseout', tip.hide);

			svg.selectAll('.yaxis2')
    		.data(data)
    		.enter()
    		.append('circle')
    		.attr('class', 'yaxis2')
    		.attr('cx', function (datum) {
    		return x(datum.age)
			})
    		.attr('cy', function (datum) {
    		return y(datum.lifeexpectancyfemale)
			})
    		.attr('r', 3)
    		.attr('fill', 'black')
    		.on('mouseover', tip1.show)
    		.on('mouseout', tip1.hide);
    });

// chart 3
    var margin1 = { top: 30, right: 40, bottom: 2000, left: 50 },
        width1 = 600 - margin1.left - margin1.right,
        height1 = 270 - margin1.top - margin1.bottom;

    var tip3 = d3.tip()
      .attr('class', 'd3-tip')
      .offset([-10, 0])
      .html(function (d) {
          return "<p class='hover'>Male</p> <strong>Year:</strong> <span style='color:white'>" + d.year + "</br><strong>Male:</strong> <span style='color:white'>" + d.male + "%" + "</span></br><strong>World Rank:</strong> <span style='color:white'>" + d.rankmale;
      });

    var tip4 = d3.tip()
      .attr('class', 'd3-tip')
      .offset([-10, 0])
      .html(function (d) {
          return "<p class='hover'>Female</p> <strong>Year:</strong> <span style='color:white'>" + d.year + "</br><strong>Female:</strong> <span style='color:white'>" + d.female + "%" + "</span></br><strong>World Rank:</strong> <span style='color:white'>" + d.rankfemale;
      });

    var tip2 = d3.tip()
      .attr('class', 'd3-tip')
      .offset([-10, 0])
      .html(function (d) {
          return "<p class='hover'>Both Gender</p> <strong>Year:</strong> <span style='color:white'>" + d.year + "</br><strong>Both Gender:</strong> <span style='color:white'>" + d.both + "%" + "</span></br><strong>World Rank:</strong> <span style='color:white'>" + d.rankall;
      });
	  
    var xsec = d3.scale.linear()
        .range([0, width]);	  

    var ysec = d3.scale.linear()
        .range([height, 0]);
 		   	  
    var xAxissec = d3.svg.axis()
        .scale(xsec)
        .orient("bottom").ticks(5);

    var yAxissec = d3.svg.axis()
        .scale(ysec)
        .orient("left").ticks(5);
					  
    var line4 = d3.svg.line()
        .x(function (d) { return xsec(d.year); })
        .y(function (d) { return ysec(d.male); });

    var line5 = d3.svg.line()
   	 	.x(function (d) { return xsec(d.year); })
    	.y(function (d) { return ysec(d.female); });

    var line3 = d3.svg.line()
   	 	.x(function (d) { return xsec(d.year); })
    	.y(function (d) { return ysec(d.bothgender); });

    var svg1 = d3.select("#chart3").append("svg")
        .attr("width", width1 + margin1.left + margin1.right)
        .attr("height", height1 + margin1.top + margin1.bottom)
      .append("g")
        .attr("transform", "translate(" + margin1.left + "," + margin1.top + ")");

    svg1.call(tip3).call(tip4).call(tip2);

    d3.json("/workforce/historyData.php?country="+countryName, function (error, data) {
        data.forEach(function (d) {
            d.year = +d.year;
            d.male = +d.male;
            d.female = +d.female;
            d.bothgender = +d.bothgender;
        });

        xsec.domain(d3.extent(data, function (d) {
            return d.year;
        }));
        ysec.domain([d3.min(data, function (d) { return(d.male < d.female) ? d.male : d.female;}), d3.max(data, function (d) { return(d.male < d.female) ? d.female : d.male})]);

        svg1.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0,"+ height + ")")
            .call(xAxissec);

        svg1.append("text")      // text label for the x axis
          .attr("x", 155)
          .attr("y", 235)
          .style("text-anchor", "middle")
          .text("Year");

        svg1.append("g")
            .attr("class", "y axis")
            .call(yAxissec)
          .append("text")
            .attr("transform", "rotate(-90)")
            .attr("y", 6)
            .attr("dy", ".71em")
            .style("text-anchor", "end")
            .text("Life Expectancy History (%)");

        svg1.append("path")
    		.datum(data)
   			.attr("class", "line")
   			.style("stroke", "red")
   		    .attr("d", line4(data));

        svg1.append("path")
   		    .datum(data)
    		.attr("class", "line")
			.style("stroke", "black")
    		.attr("d", line5(data));

        svg1.append("path")
   			.datum(data)
   		    .attr("class", "line")
			.style("stroke", "blue")
    		.attr("d", line3(data));

        svg1.selectAll('.yaxis1')
    		.data(data)
    		.enter()
    		.append('circle')
    		.attr('class', 'yaxis1')
   			.attr('cx', function (datum) {
   			    return xsec(datum.year)
   			})
    		.attr('cy', function (datum) {
    		    return ysec(datum.male)
    		})
    		.attr('r', 3)
    		.attr('fill', 'red')
    		.on('mouseover', tip3.show)
    		.on('mouseout', tip3.hide);

        svg1.selectAll('.yaxis2')
        .data(data)
        .enter()
        .append('circle')
        .attr('class', 'yaxis2')
        .attr('cx', function (datum) {
            return xsec(datum.year)
        })
        .attr('cy', function (datum) {
            return ysec(datum.female)
        })
        .attr('r', 3)
        .attr('fill', 'black')
        .on('mouseover', tip4.show)
        .on('mouseout', tip4.hide);

        svg1.selectAll('.yaxis3')
        .data(data)
        .enter()
        .append('circle')
        .attr('class', 'yaxis3')
        .attr('cx', function (datum) {
            return xsec(datum.year)
        })
        .attr('cy', function (datum) {
            return ysec(datum.bothgender)
        })
        .attr('r', 3)
        .attr('fill', 'blue')
        .on('mouseover', tip2.show)
        .on('mouseout', tip2.hide);
    });


// chart 2
    var data = d3.json("/workforce/worldlifeData.php?country="+countryName, renderChart);

    function renderChart(data) {

        var valueLabelWidth = 40; // space reserved for value labels (right)
        var barHeight = 20; // height of one bar
        var barLabelWidth = 100; // space reserved for bar labels
        var barLabelPadding = 5; // padding between bar and bar labels (left)
        var gridLabelHeight = 18; // space reserved for gridline labels
        var gridChartOffset = 3; // space between start of grid and first bar
        var maxBarWidth = 420; // width of the bar with the max value
        var tip5 = d3.tip()
         .attr('class', 'd3-tip')
         .offset([-10, 0])
         .html(function (d) {
             return "<strong>Life Expectancy:</strong> <span style='color:white'>" + d.worldlifeexpectancy + "% " + "</span>";
         })


        // accessor functions 
        var barLabel = function (d) { return d['countryName']; };
        var barValue = function (d) { return parseFloat(d['worldlifeexpectancy']); };

        // scales
        var yScale = d3.scale.ordinal().domain(d3.range(0, data.length)).rangeBands([0, data.length * barHeight]);
        var y = function (d, i) { return yScale(i); };
        var yText = function (d, i) { return y(d, i) + yScale.rangeBand() / 2; };
        var x = d3.scale.linear().domain([0, d3.max(data, barValue)]).range([0, maxBarWidth]);
        // svg container element
        var chart = d3.select('#inner').append("svg")
          .attr('width', maxBarWidth + barLabelWidth + valueLabelWidth)
          .attr('height', gridLabelHeight + gridChartOffset + data.length * barHeight);

        chart.call(tip5);


        // grid line labels
        var gridContainer = chart.append('g')
          .attr('transform', 'translate(' + barLabelWidth + ',' + gridLabelHeight + ')');
        gridContainer.selectAll("tet").data(x.ticks(10)).enter().append("tet")
          .attr("x", x)
          .attr("dy", -3)
          .attr("text-anchor", "middle")
          .text(String);
        // vertical grid lines
        gridContainer.selectAll("ine").data(x.ticks(10)).enter().append("ine")
          .attr("x1", x)
          .attr("x2", x)
          .attr("y1", 0)
          .attr("y2", yScale.rangeExtent()[1] + gridChartOffset)
          .style("stroke", "#ccc");
        // bar labels
        var labelsContainer = chart.append('g')
          .attr('transform', 'translate(' + (barLabelWidth - barLabelPadding) + ',' + (gridLabelHeight + gridChartOffset) + ')');
        labelsContainer.selectAll('text').data(data).enter().append('text')
          .attr('y', yText)
          .attr('stroke', 'none')
          .attr('fill', 'black')
          .attr("dy", ".35em") // vertical-align: middle
          .attr('text-anchor', 'end')
          .text(barLabel);
        // bars
        var barsContainer = chart.append('g')
          .attr('transform', 'translate(' + barLabelWidth + ',' + (gridLabelHeight + gridChartOffset) + ')');
        barsContainer.selectAll("rect").data(data).enter().append("rect")
          .attr('y', y)
          .attr('height', yScale.rangeBand())
          .attr('width', function (d) { return x(barValue(d)); })
          .attr('stroke', 'white')
          .attr('fill', 'skyblue')


        .on('mouseover', tip5.show)
              .on('mouseout', tip5.hide);
        // bar value labels
        barsContainer.selectAll("text").data(data).enter().append("text")
          .attr("x", function (d) { return x(barValue(d)); })
          .attr("y", yText)
          .attr("dx", 3) // padding-left
          .attr("dy", ".35em") // vertical-align: middle
          .attr("text-anchor", "start") // text-align: right
          .attr("fill", "black")
          .attr("stroke", "none")
          .text(function (d) { return d3.round(barValue(d), 2); });
        // start line
        barsContainer.append("line")
          .attr("y1", -gridChartOffset)
          .attr("y2", yScale.rangeExtent()[1] + gridChartOffset)
          .style("stroke", "#000");
    }

// chart 4

var margin2 = {top: 40, right: 80, bottom: 80, left: 150},
    width2 = 350 - margin.left - margin.right,
    height2 = 250 - margin.top - margin.bottom;

var xbarchart = d3.scale.ordinal()
    .rangeRoundBands([0, width], .1);

var ybarchart = d3.scale.linear().domain([100,500]).range([height, 0]),
ybarchart1 = d3.scale.linear().domain([500,1000]).range([height, 0]);

var xAxisbarchart = d3.svg.axis()
    .scale(xbarchart)
    .orient("bottom");


// create left yAxis
var yAxisLeft = d3.svg.axis().scale(ybarchart).ticks(5).orient("left");
// create right yAxis
var yAxisRight = d3.svg.axis().scale(ybarchart1).orient("right");

var tip6 = d3.tip()
  .attr('class', 'd3-tip')
  .offset([-10, 0])
  .html(function(d) {
    return "<strong>Health Workers:</strong> <span style='color:white'>" + d.totalhealthworkers + "</span>";
  })
  
var tip7 = d3.tip()
  .attr('class', 'd3-tip')
  .offset([-10, 0])
  .html(function(d) {
    return "<strong>Health Facilities:</strong> <span style='color:white'>" + d.totalhealthfacilities + "</span>";
  })  

var svg2 = d3.select("#chart4").append("svg")
    .attr("width", width2 + margin2.left + margin2.right)
    .attr("height", height2 + margin2.top + margin2.bottom)
  .append("g")
    .attr("class", "graph")
    .attr("transform", "translate(" + margin2.left + "," + margin2.top + ")")

svg2.call(tip6).call(tip7);

d3.json("/workforce/workforceData.php?country="+countryName, function(error, data) {
  xbarchart.domain(data.map(function(d) { return d.countryName; }));
  ybarchart.domain([0, d3.max(data, function (d) { return d.totalhealthworkers; })]);
  ybarchart1.domain([0, d3.max(data, function (d) { return d.totalhealthfacilities; })]);
  
  svg2.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxisbarchart);

  svg2.append("g")
	  .attr("class", "y axis axisLeft")
	  .attr("transform", "translate(0,0)")
	  .call(yAxisLeft)
	.append("text")
	  .attr("y", 35)
	  .attr("dy", "-2em")
       .attr("transform", "rotate(-90)")
	  .style("text-anchor", "end")
	  .text("Total health workers per 100,000 people");
	
  svg2.append("g")
	  .attr("class", "y axis axisRight")
	  .attr("transform", "translate(" + (width) + ",0)")
	  .call(yAxisRight)
	.append("text")
	  .attr("y", 15)
       .attr("x", -20)
       .attr("transform", "rotate(-90)")
	  .attr("dy", "-2em")
	  .attr("dx", "2em")
	  .style("text-anchor", "end")
	  .text("Total health facilities per 100,000 people");

  bars = svg2.selectAll(".bar").data(data).enter();

  bars.append("rect")
      .attr("class", "bar1")
      .attr("x", function (d) { return xbarchart(d.countryName); })
      .attr("width", xbarchart.rangeBand() / 3)
      .attr("y", function (d) { return ybarchart(d.totalhealthworkers); })
	  .attr("height", function (d, i, j) { return height - ybarchart(d.totalhealthworkers); })
          .on('mouseover', tip6.show)
          .on('mouseout', tip6.hide)
		  
  bars.append("rect")
      .attr("class", "bar2")
      .attr("x", function(d) { return xbarchart(d.countryName) + xbarchart.rangeBand()/2; })
      .attr("width", xbarchart.rangeBand() / 3)
      .attr("y", function(d) { return ybarchart1(d.totalhealthfacilities); })
	  .attr("height", function (d, i, j) { return height - ybarchart1(d.totalhealthfacilities); })
	      .on('mouseover', tip7.show)
          .on('mouseout', tip7.hide)
});

function type(d) {
    d.totalhealthworkers = +d.totalhealthworkers;
    d.totalhealthfacilities = +d.totalhealthfacilities;
  return d;
}
				


	
</script>

</script>

	
    </body>
    
</body>
</html>
