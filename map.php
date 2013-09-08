<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>Mapping Malaria</title>
	<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
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

.case {
	width: 10px;
	height: 10px;
}
		</style>
  </head>
  <body>
    <div id="map"></div>
    <script src="js/map.js"></script>
  </body>
</html>