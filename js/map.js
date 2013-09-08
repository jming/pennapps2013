/*************************************************************************
 * Globals 
 *************************************************************************/
var flatDB;
var globalDatabase;

/*************************************************************************
 * Scales and transformation helpers
 *************************************************************************/
var opacityScale = d3.scale.linear()
	.range([0, 1]);

var colorScale = d3.scale.ordinal()
	.range(["#FEE5D9", "#FCAE91", "#B6A4A", "#DE2D26", "#A50F15"]);

function getOpacity(date) {
	return opacityScale(+date);
}

var format = d3.time.format("%Y-%m-%d");

// Use Leaflet to implement a D3 geographic projection.
function project(x) {
  var point = map.latLngToLayerPoint(new L.LatLng(x[1], x[0]));
  return [point.x, point.y];
}
var map = L.map('map', {
    center: [1, 38],
   	zoom: 6
  });

L.tileLayer('http://{s}.tile.cloudmade.com/018ce9c77aca42948f284396da6fdb8f/22677/256/{z}/{x}/{y}.png', {
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
    maxZoom: 18
  }).addTo(map);

window.onload = loadData;

var markers;
var svg;

/* Adds markers based on the dataset */
function showMap() {   	
  /*
  L.tileLayer('http://{s}.tile.cloudmade.com/018ce9c77aca42948f284396da6fdb8f/106960/256/{z}/{x}/{y}.png', {
  			attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
  		maxZoom: 18
  	}).addTo(map);
  */
  // Clear old markers
  if (markers != null) {
    markers.clearLayers();
  }
  
  markers = new L.MarkerClusterGroup({showCoverageOnHover: false});
  var markersList = [];
  
  populate(markersList, markers);
  map.addLayer(markers);

    /*
var g = d3.select("#outbreak_overlay");
		var cases = g.selectAll("circle")
      .data(flatDB)
    .enter().append("circle");
    
    map.on("viewreset", reset);
    reset();
    
*/
    
  // Reposition the SVG to cover the features.
  function reset() {
    var bottomLeft = project(bounds[0]),
        topRight = project(bounds[1]);
  
    svg .attr("width", topRight[0] - bottomLeft[0])
        .attr("height", bottomLeft[1] - topRight[1])
        .style("margin-left", bottomLeft[0] + "px")
        .style("margin-top", topRight[1] + "px");
  
    var g = d3.select("#outbreak_overlay");
    g.attr("transform", "translate(" + -bottomLeft[0] + "," + -topRight[1] + ")");
    
    //feature.attr("d", path);
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
    { opacity: getOpacity(d.dateOnset) });
  m.bindPopup([
					'<strong>Malaria Case</strong>',
					'<br /> Age: ', d.age,
					'<br /> Gender: ', d.gender,
					'<br /> Onset date: ', format(d.dateOnset)].join(''));
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
  svg = d3.select(map.getPanes().overlayPane).append("svg");
  var g = svg.append("g")
        .attr("class", "leaflet-zoom-hide")
        .attr("id", "outbreak_overlay");

  d3.json("js/outbreaks.json", function(error, data) {
    flatDB = [];

    data.forEach(function(d) {
    	d.age = parseInt(d.age);
    	d.dateOnset = format.parse(d.dateOnset);
      if (d.diagnosed != "") {
        d.wasDiagnosed = true;
      }
      else {
        d.wasDiagnosed = false;
      }
		});
    
		flatDB = flatDB.concat(data);

	 globalDatabase = d3.nest()
	   .key(function(d) { return d.gender; })
	   .key(function(d) { return d.wasDiagnosed; })
	   .map(flatDB, d3.map);
    
    // Update range of dates
  	var ext = d3.extent(flatDB, function(d) { return +d.dateOnset; });
	  opacityScale.domain(ext);
	  
    // Add markers
    showMap();
  });
};