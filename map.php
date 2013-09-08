<?php require_once("database.php"); ?>

<div id="map" style="height: 100% !important;"></div>
<script>

/*************************************************************************
 * Globals 
 *************************************************************************/
var flatDB;
var globalDatabase;
var bounds = new Array(2);
var markers;

/*************************************************************************
 * Scales and transformation helpers
 *************************************************************************/
var opacityScale = d3.scale.linear()
	.range([0.1, 1]);

var colorScale = d3.scale.ordinal()
	.range(["#FCAE91", "#FB6A4A", "#DE2D26", "#A50F15"]);

function getOpacity(date) {
	return opacityScale(+date);
}

function getColor(date) {
	return colorScale(+date); 
}
var format = d3.time.format("%Y-%m-%d");
var formatCurrent = d3.time.format("%Y-%m-%d %X");

// Use Leaflet to implement a D3 geographic projection.
function project(x) {
  var point = map.latLngToLayerPoint(new L.LatLng(x[0], x[1]));
  return [point.x, point.y];
}

function showDate(date) {
	if (+date == 0) {
		return "NA";
	}
	return format(date);
}

/****************************************************************************
 * Leaflet
 ***************************************************************************/
var map = L.map('map', {
    center: [1, 38],
   	zoom: 6
  });

  L.tileLayer('http://{s}.tile.cloudmade.com/018ce9c77aca42948f284396da6fdb8f/106960/256/{z}/{x}/{y}.png', {
  	attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
  	maxZoom: 18
  }).addTo(map);
 

/* Alternate map tile style: grayscale
L.tileLayer('http://{s}.tile.cloudmade.com/018ce9c77aca42948f284396da6fdb8f/22677/256/{z}/{x}/{y}.png', {
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
    maxZoom: 18
  }).addTo(map);
*/

var myIcon = L.divIcon({className: 'my-div-icon'});

var svg = d3.select(map.getPanes().overlayPane).append("svg");
var g = svg.append("g")
        .attr("class", "leaflet-zoom-hide")
        .attr("id", "outbreak_overlay");

//window.onload = loadData;

/* Update the markers every second */
var int = self.setInterval(loadData, 5*1000);

//loadData();

/* Adds markers based on the dataset */
function showMap() {   	

  // Clear old markers
  if (markers != null) {
    markers.clearLayers();
  }
  
  markers = new L.MarkerClusterGroup({ showCoverageOnHover: false });
  var markersList = [];
  
  populate(markersList, markers);
  map.addLayer(markers);

    var cases = g.selectAll("circle")
      .data(flatDB);
    
    cases.attr("cx", function(d, i) {
          return project(d.coordinates)[0];
      })
      .attr("cy", function(d) {
      		return project(d.coordinates)[1];
      })
      .attr("fill", function(d) { return getColor(d.onset); });
      
    var newCases = cases.enter().append("circle")
    	.attr("class", "case")
    	.attr("cx", function(d, i) {
          return project(d.coordinates)[0];
      })
      .attr("cy", function(d) {
      		return project(d.coordinates)[1];
      })
      .attr("r", 5)
      .attr("fill", "#A50F15")
      .attr("opacity", function(d) { return getOpacity(d.onset); })
      .attr("stroke", "none");
    
    cases.exit()
    .transition()
				.duration(750)
				.attr("opacity", 0)
				.remove();
    
    map.on("viewreset", reset);
    reset();
 
    
  // Reposition the SVG to cover the features.
  function reset() {
    var bottomLeft = project(bounds[0]);
    var topRight = project(bounds[1]);

    svg.attr("width", topRight[0] - bottomLeft[0] + 10)
        .attr("height", bottomLeft[1] - topRight[1] + 10)
        .style("margin-left", bottomLeft[0]  - 5 + "px")
        .style("margin-top", topRight[1] - 5 + "px");
    
    g.attr("transform", "translate(" + -bottomLeft[0] + "," + -topRight[1] + ")");
    
    var circles = d3.selectAll(".case");
    circles.attr("cx", function (d) { return project(d.coordinates)[0]; })
           .attr("cy", function (d) { return project(d.coordinates)[1]; });
  };
}

// Adds all datapoints as markers
function populate(markersList, markers) {
	flatDB.forEach(function(d, i, array) {
    var m = createMarker(d, i, array);
    markersList.push(m);
    markers.addLayer(m);
	});
	
	return false;
}

function createMarker(d, i, array) {
  var m = new L.Marker(new L.LatLng(d.coordinates[0], d.coordinates[1]),
    { opacity: getOpacity(d.onset) });
  m.bindPopup([
					'<strong>Malaria Case</strong>',
					'<br /> Age: ', d.age,
					'<br /> Gender: ', d.gender,
					'<br /> Onset date: ', showDate(d.onset),
					'<br /> Diagnosed: ', d.diagnosed,
					'<br /> Symptoms: ', d.symptoms.join(', ')].join(''));
  return m;
}

function getRandomLatLng(map) {
	var bounds = map.getBounds(),
		southWest = bounds.getSouthWest(),
		northEast = bounds.getNorthEast(),
		lngSpan = northEast.lng - southWest.lng,
		latSpan = northEast.lat - southWest.lat;

	return new L.LatLng(
		southWest.lat + latSpan * Math.random(),
		southWest.lng + lngSpan * Math.random());
}

function loadData() {
  var data = <?php echo get_data(); ?>;
  //d3.json("js/outbreaks.json", function(error, data) {
    flatDB = [];

    data.forEach(function(d) {
    	d.age = parseInt(d.age);
    	if (d.onset == "0000-00-00") {
    		d.onset = new Date();
    	}
    	else {
    		d.onset = format.parse(d.onset);
    	}
    	if (d.diagdate == "0000-00-00") {
    		d.diagdate = new Date();
    	}
    	else {
    		d.diagdate = format.parse(d.diagdate);
      }
      if (d.diagnosed == "") {
        d.wasDiagnosed = false;
      }
      else {
        d.wasDiagnosed = (d.diagnosed == "y") ? true : false;
      }
      d.coordinates = [parseFloat(d.latitude), parseFloat(d.longitude)];
      d.currentDate = formatCurrent.parse(d.currentdate);
		});
    
		flatDB = flatDB.concat(data);

	  globalDatabase = d3.nest()
	   .key(function(d) { return d.gender; })
	   .key(function(d) { return d.wasDiagnosed; })
	   .map(flatDB, d3.map);
    
    // Update range of dates
  	var ext = d3.extent(flatDB, function(d) { return +d.onset; });
	  opacityScale.domain(ext);
	  colorScale.domain(ext);
	  
	  var latExt = d3.extent(flatDB, function(d) { return d.coordinates[0]; });
	  var longExt = d3.extent(flatDB, function(d) { return d.coordinates[1]; });
	  
	  // bottom left corner
	  bounds[0] = [latExt[0], longExt[0]];
	  
	  // upper right corner
	  bounds[1] = [latExt[1], longExt[1]];
	  
    // Add markers
    showMap();
  //});
};
</script>