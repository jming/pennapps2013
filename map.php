<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>Mapping Malaria</title>
  <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.css" />
  <!--[if lte IE 8]>
    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.ie.css" />
  <![endif]-->
  <script src="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.js"></script>
  <link rel="stylesheet" href="lib/Leaflet.markercluster/dist/MarkerCluster.css" />
<!-- 	<link rel="stylesheet" href="lib/Leaflet.markercluster/dist/MarkerCluster.Default.css" /> -->
  <link rel="stylesheet" href="css/custom-marker-cluster.css" />
	<!--[if lte IE 8]><link rel="stylesheet" href="lib/Leaflet.markercluster/dist/MarkerCluster.Default.ie.css" /><![endif]-->
  <script src="lib/Leaflet.markercluster/dist/leaflet.markercluster-src.js"></script>
  <style>
#map {
  width: 960px;
  height: 800px;
}
		</style>
  </head>
  <body>
    <div id="map"></div>
    <script>
    	var map = L.map('map', {
    	  center: [1, 38],
      	zoom: 6
      });
    	
    	L.tileLayer('http://{s}.tile.cloudmade.com/018ce9c77aca42948f284396da6fdb8f/106960/256/{z}/{x}/{y}.png', {
   			attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
    		maxZoom: 18
			}).addTo(map);
			
			var marker = L.marker([1, 38], { 
				opacity: getOpacity()
			}).addTo(map);
			
			marker.bindPopup("<b>Hello world!</b><br>I am a popup.");

			var markers = new L.MarkerClusterGroup({showCoverageOnHover: false});
		var markersList = [];

		function populate() {
			for (var i = 0; i < 100; i++) {
				var m = new L.Marker(getRandomLatLng(map), { opacity: getOpacity() });
				m.bindPopup("Data string here.");
				markersList.push(m);
				markers.addLayer(m);
			}
			return false;
		}
		function populateRandomVector() {
			for (var i = 0, latlngs = [], len = 20; i < len; i++) {
				latlngs.push(getRandomLatLng(map));
			}
			var path = new L.Polyline(latlngs);
			map.addLayer(path);
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

		markers.on('click', function (a) {
			a.openPopup();
		});

		populate();
		map.addLayer(markers);
		
			function getOpacity(date) {
				// TODO: make functional based on dataset
				return Math.random();
			}
    </script>
  </body>
</html>