<!DOCTYPE html>
<meta charset="utf-8">
<title>D3 World Map</title>
<style>
.country:hover{
  fill: #52adcc;
  stroke: #fff;
  stroke-width: 1.5px;
}
.text{
  font-size:10px;
  text-transform:capitalize;
}
#container {
  margin:10px 10%;
  border:2px solid #000;
  border-radius: 5px;
  height:100%;
  overflow:hidden;
  background: #F0F8FF;
}
.hidden { 
  display: none; 
}
div.tooltip {
  color: #222; 
  background: #fff; 
  padding: .5em; 
  text-shadow: #f5f5f5 0 1px 0;
  border-radius: 2px; 
  box-shadow: 0px 0px 2px 0px #a6a6a6; 
  opacity: 0.9; 
  position: absolute;
}
.graticule {
  fill: none;
  stroke: #bbb;
  stroke-width: .5px;
  stroke-opacity: .5;
}
.equator {
  stroke: #ccc;
  stroke-width: 1px;
}
.country {
  fill: #cccccc;
  border: 10px yellow;
}
h1
{
        text-align: center;
}

</style>
</head>
<body>

  <h1>World Map</h1>
  <div id="container"></div>

<script src="Javascripts/d3.min.js"></script>
<script src="Javascripts/topojson.v1.min.js"></script>
<script src="Javascripts/datamaps.world.js"></script>
<script>
d3.select(window).on("resize", throttle);
    //var myPages = { "United States": "United States.html", "Canada": "Canada.html", "Singapore": "Singapore.html" }
var myPages = {}
var zoom = d3.behavior.zoom()   
    .scaleExtent([1, 9])
    .on("zoom", move);


var width = document.getElementById('container').offsetWidth;
var height = width / 2;

var topo,projection,path,svg,g;

var graticule = d3.geo.graticule();

var tooltip = d3.select("#container").append("div").attr("class", "tooltip hidden");

setup(width,height);

function setup(width,height){
  projection = d3.geo.mercator()
    .translate([(width/2), (height/2)])
    .scale( width / 2 / Math.PI);

  path = d3.geo.path().projection(projection);

  svg = d3.select("#container").append("svg")
      .attr("width", width)
      .attr("height", height)
      .call(zoom)
      .on("click", click)
      .append("g");

  g = svg.append("g");

}

d3.json("Json/world-topo-min.json", function(error, world) {

  var countries = topojson.feature(world, world.objects.countries).features;

  topo = countries;
  draw(topo);

});

function draw(topo) {

  svg.append("path")
     .datum(graticule)
     .attr("class", "graticule")
     .attr("d", path);


  g.append("path")
   .datum({type: "LineString", coordinates: [[-180, 0], [-90, 0], [0, 0], [90, 0], [180, 0]]})
   .attr("class", "equator")
   .attr("d", path);


  var country = g.selectAll(".country").data(topo);


  country.enter().insert("path")
      .attr("class", "country")
      .attr("d", path)
      .attr("id", function (d, i) { return d.id; })
      .attr("title", function (d, i) { return d.properties.name; });
      //.style("fill", function(d, i) { return d.properties.color; });

  //offsets for tooltips
  var offsetL = document.getElementById('container').offsetLeft+20;
  var offsetT = document.getElementById('container').offsetTop+10;
  var myPage
  //tooltips
  country
    .on("mousemove", function(d,i) {

      var mouse = d3.mouse(svg.node()).map( function(d) { return parseInt(d); } );

      tooltip.classed("hidden", false)
             .attr("style", "left:"+(mouse[0]+offsetL)+"px;top:"+(mouse[1]+offsetT)+"px")
             .html(d.properties.name);

      })
      .on("mouseout",  function(d,i) {
        tooltip.classed("hidden", true);
      })
      .on("click", function (d,i) {
      var myPage = myPages[d.properties.name];
	 	
	  var decode = encodeURIComponent(d.properties.name);
   <!--   location = myPage?myPage : decode + ".html"-->-->
	  country = decode.toString();
/*	   var namer = sessionStorage.getItem("decode");
        $.ajax({
            type: 'post',
            url: 'disease2.php',
            data: {namer:namer}
        });*/
		
	 location = "disease2.php?country=" + country
      });
    country.click().


  //EXAMPLE: adding some capitals from external CSV file
  d3.csv("country-capitals.csv", function(err, capitals) {

    capitals.forEach(function(i){
      addpoint(i.CapitalLongitude, i.CapitalLatitude, i.CapitalName );
    });

  });

}
   
function redraw() {
  width = document.getElementById('container').offsetWidth;
  height = width / 2;
  d3.select('svg').remove();
  setup(width,height);
  draw(topo);
}


function move() {

  var t = d3.event.translate;
  var s = d3.event.scale; 
  zscale = s;
  var h = height/4;


  t[0] = Math.min(
    (width/height)  * (s - 1), 
    Math.max( width * (1 - s), t[0] )
  );

  t[1] = Math.min(
    h * (s - 1) + h * s, 
    Math.max(height  * (1 - s) - h * s, t[1])
  );

  zoom.translate(t);
  g.attr("transform", "translate(" + t + ")scale(" + s + ")");

  //adjust the country hover stroke width based on zoom level
  d3.selectAll(".country").style("stroke-width", 1.5 / s);

}



var throttleTimer;
function throttle() {
  window.clearTimeout(throttleTimer);
    throttleTimer = window.setTimeout(function() {
      redraw();
    }, 200);
}


//geo translation on mouse click in map
function click() {
  var latlon = projection.invert(d3.mouse(this));
  console.log(latlon);
}


//function to add points and text to the map (used in plotting capitals)
//function addpoint(lat,lon,text) {

//  var gpoint = g.append("g").attr("class", "gpoint");
//  var x = projection([lat,lon])[0];
//  var y = projection([lat,lon])[1];

//  gpoint.append("svg:circle")
//        .attr("cx", x)
//        .attr("cy", y)
//        .attr("class","point")
//        .attr("r", 1.5);

//  //conditional in case a point has no associated text
//  if(text.length>0){

//    gpoint.append("text")
//          .attr("x", x+2)
//          .attr("y", y+2)
//          .attr("class","text")
//          .text(text);
//  }

//}

    //$(document).ready(function() {
    //    $('a.hohoho').click(function() {
    //        alert('ho ho ho');
    //    });
    //});

//window.onload = function () {
//    var anchors = document.getElementsByClassName('country');
//    for (var i = 0; i < anchors.length; i++) {
//        var anchor = anchors[i];
//        anchor.onclick = function () {
//            alert('ho ho ho');
//        }
//    }
//}

</script>
<!--    <a class="country" href="#" onclick="window.open('worldmap-template.html')"></a> -->
</body>
</html>