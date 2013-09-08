<!-- TODO: ADD JS VALIDATION FOR FIELDS -->
<?
	if ($_POST) {
		require_once("database.php");
		connect_db();
		
		$age = mysql_real_escape_string($_POST["age"]);
		$gender = mysql_real_escape_string($_POST["gender"]);
		$onset = date(mysql_real_escape_string($_POST["date-onset"]));
		$diagnosed = mysql_real_escape_string($_POST["diagnosed"]);
		$date = date(mysql_real_escape_string($_POST["date-diagnosed"]));
		$latitude = mysql_real_escape_string($_POST["latitude"]);
		$longitude = mysql_real_escape_string($_POST["longitude"]);
		$currentdate = mysql_real_escape_string($_POST["currentdate"]);
		$symptoms = $_POST["symptoms"];
		// TODO: how do we want the location formatted?
		$location = mysql_real_escape_string($_POST["location-current"]);
		$date_current = date(mysql_real_escape_string($_POST["date-current"]));

		// TODO: update sql query based on table structure 
		$result = mysql_query("INSERT INTO reports (age, gender, onset, diagnosed, diagdate, latitude, longitude, currentdate) VALUES ($age, '$gender', '$onset', '$diagnosed', '$date', '$latitude', '$longitude', '$currentdate')");
		if (!$result):
			// TODO: display error
		else:
			$id = mysql_insert_id();
			foreach ($symptoms as $symp) {
				$symp = mysql_real_escape_string($symp);
				$result = mysql_query("INSERT INTO symptoms (report_id, symptom) VALUES ($id, '$symp')");
			}		
			// TODO: display success
		endif;
	}
?> 
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Malaria Mapping</title>
		<link href='http://fonts.googleapis.com/css?family=Lato:300,400,900,300italic,900italic' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="themes/malaria-mapping.min.css" />
		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.2/jquery.mobile.structure-1.3.2.min.css" />
		<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
		<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>
    <script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.css" />
    <!--[if lte IE 8]>
      <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.ie.css" />
    <![endif]-->
    <script src="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.js"></script>
    <link rel="stylesheet" href="lib/Leaflet.markercluster/dist/MarkerCluster.css" />
    
    <link rel="stylesheet" href="css/custom-marker-cluster.css" />
  	<!--[if lte IE 8]><link rel="stylesheet" href="css/custom-marker-cluster-ie.css" /><![endif]-->
    <script src="lib/Leaflet.markercluster/dist/leaflet.markercluster-src.js"></script>
		<script>
			window.onload = function () {
				var Geo = {};

				function success(position) {
					Geo.lat = position.coords.latitude;
					Geo.lng = position.coords.longitude;
					console.log("lat:"+Geo.lat+", lng:"+Geo.lng);
					postPosition(Geo.lat, Geo.lng);
				}

				function error() {
					console.log("Geocoder failed.");
				}

				function postPosition(lat, lng) {
					$("#location-latitude").val(lat);
					$("#location-longitude").val(lng);
				}

				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(success, error);
				}
			}
		</script>
		<style>
			*, .ui-body-a input {
				font-family:"Lato";
				font-weight:400;
			}
			.malaria-header {
				height:60px;
			}
			.malaria-button {
				width:47.5%;
				height:300px;
			}
			.ui-header .ui-title,
			.ui-footer .ui-title {
				font-weight:300;
				font-size:24px;
			}
			.ui-fullsize .ui-btn-inner,
			input.ui-input-text,
			textarea.ui-input-text {
				font-size:14px;
				font-weight:300;
			}
			.ui-header .ui-btn-left,
			.ui-footer .ui-button-left {
				left:20px;
				top:15px;
			}
			.malaria-button-image {
				height:150px;
				width:150px;
				padding:20px;
				padding-top:50px;
			}

      .ui-content {
        padding: 0;
      }
      
      #map {
        position: relative;
        width: 100%;
        height: 100%;
        min-height: 420px;
        overflow: hidden;
      }
      
      .my-div-icon {
      	width: 10px;
      	height: 10px;
      
      	text-align: center;
      	border-radius: 5px;
      	color: white;
      	font: 12px "Helvetica Neue", Arial, Helvetica, sans-serif;
      }
      
      div.my-div-icon:hover {
      	border: 2px solid white;
      }
		</style>
	</head>
<body>

<div data-role="page" id="home" data-theme="a">

	<div data-role="header" class="malaria-header">
		<h1>Mapping Malaria</h1>
		<a href="#info" data-icon="info" data-iconpos="notext">Info</a>
		<a href="#report" data-icon="plus" data-iconpos="notext">Report incident</a>
	</div>

	<div data-role="content">
		<?php include 'map.php'; ?>
	</div>

</div>

<div data-role="page" id="info" data-theme="a">

	<div data-role="header" class="malaria-header">
		<h1>About Mapping Malaria</h1>
		<a href="#home" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
	</div>

	<div data-role="content">
		3.3 billion people, or half the world's population is at risk for malaria. 
		<br><br>
		This application will mobile technology to crowd source the spread of malaria to better inform preventative measures.
		<br><br>
		<strong>Mapping Malaria</strong> was created by Alisa Nguyen, Deborah Alves, Joy Ming, and Julie Zhang for PennApps Fall 2013.
	</div>

</div>

<div data-role="page" id="report" data-theme="a">

	<div data-role="header" class="malaria-header">
		<h1>Report Incidence</h1>
		<a href="#home" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
	</div>

	<div data-role="content">
		<form method="post" action="#home">
			<div data-role="fieldcontain">
				<label for="age">Age:</label>
				<input type="text" name="age" id="age" value="" data-clear-btn="true"/>
			</div>
			<div data-role="fieldcontain">
				<fieldset data-role="controlgroup">
					<legend>Gender:</legend>
					<input type="radio" name="gender" id="radio-female" value="f"/>
					<label for="radio-female">Female</label>
					<input type="radio" name="gender" id="radio-male" value="m"/>
					<label for="radio-male">Male</label>
				</fieldset>
			</div>
			<div data-role="fieldcontain">
				<fieldset data-role="controlgroup">
					<legend>Symptoms:</legend>
					<? 
						$symptoms = array("chills", "fever", "sweating", "headache", "fatigue", "nausea", "vomiting", "cough");
						foreach ($symptoms as $s):
					?>
						<input type="checkbox" name="symptoms[]" value="<?=$s ?>" id="checkbox-<?=$s ?>" class="custom" />
						<label for="checkbox-<?= $s ?>"><?= ucfirst($s) ?></label>
					<? endforeach; ?>
				</fieldset>
			</div>
			<div data-role="fieldcontain">
				<label for="date-onset">Onset of symptoms:</label>
				<input type="date" name="date-onset" id="date-onset" value=""/>
			</div>
			<div data-role="fieldcontain">
				<fieldset data-role="controlgroup">
					<legend>Diagnosed by doctor?</legend>
					<input type="radio" name="diagnosed" id="radio-yes" value="y"/>
					<label for="radio-yes">Yes</label>
					<input type="radio" name="diagnosed" id="radio-no" value="n"/>
					<label for="radio-no">No</label>
				</fieldset>
			</div>
			<div data-role="fieldcontain">
				<label for="date-diagnosed">Date of diagnosis:</label>
				<input type="date" name="date-diagnosed" id="date-diagnosed" value=""/>
			</div>
			<?
				date_default_timezone_set("America/New_York");
				$curdate = date('Y-m-d h:i:s', time()); 
			?>
			<input type="text" name="currentdate" value="<?= $curdate ?>"/>
			<input type="hidden" name="latitude" id="location-latitude" />
			<input type="hidden" name="longitude" id="location-longitude" />
			<button type="submit" name="submit" value="submit-value">Submit</button>
		</form>
	</div>

</div>

</body>
</html>