<!-- TODO: ADD JS VALIDATION FOR FIELDS -->
<?
	
	$help_text = array(
		"location" => array("Where are you located?", "Reply with \"location [location]\".", "For example, you can input the name of the nearest school, post office, hospital, etc. as a means of indicating your location."),
		"symptoms" => array("What symptoms occur?", "Reply with \"symptoms [space separated list of symptoms]\".", "Examples of symptoms are chills, fever, headache, fatigue, nausea, vomiting, cough."),
		"date-onset" => array("What date did the symptoms begin?", "Reply with \"date-onset YYYY-MM-DD\"", "Please be as accurate as you can, but if you are not sure, feel free to estimate a date within two or three days."),
		"diagnose" => array("Has the patient been diagnosed with malaria by a health professional?", "Reply with \"diagnose [yes/no]\"", ""),
		"date-diagnosed" => array("If the patient has been diagnosed, what was the date of the diagnosis?", "Reply with \"date-diagnosed YYYY-MM-DD\"", ""),
		"age" => array("What age is the patient?", "Reply with \"age [age]\" or \"age -\" if you prefer not to answer.", ""),
		"gender" => array("What is the gender of the patient?", " Reply with \"gender [gender]\" or \"gender -\" if you prefer not to answer.", "")
	);

	$banner = 0; // 0 for no banner, 1 for success, 2 for error
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
		$location = mysql_real_escape_string($_POST["location-current"]);
		$date_current = date(mysql_real_escape_string($_POST["date-current"]));

		$result = mysql_query("INSERT INTO reports (age, gender, onset, diagnosed, diagdate, latitude, longitude, currentdate) VALUES ($age, '$gender', '$onset', '$diagnosed', '$date', '$latitude', '$longitude', '$currentdate')");
		if (!$result):
			$banner = 2;
		else:
			$id = mysql_insert_id();
			foreach ($symptoms as $symp) {
				$symp = mysql_real_escape_string($symp);
				$result = mysql_query("INSERT INTO symptoms (report_id, symptom) VALUES ($id, '$symp')");
			}
			$banner = 1;
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
    <link rel="stylesheet" href="lib/Leaflet.markercluster-master/dist/MarkerCluster.css" />
    
    <link rel="stylesheet" href="css/custom-marker-cluster.css" />
  	<!--[if lte IE 8]><link rel="stylesheet" href="css/custom-marker-cluster-ie.css" /><![endif]-->
    <script src="lib/Leaflet.markercluster-master/dist/leaflet.markercluster-src.js"></script>
		<script>
			$(document).ready(function () {
				var Geo = {};

				function success(position) {
					Geo.lat = position.coords.latitude;
					Geo.lng = position.coords.longitude;
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

				function set_help(txts) {
					$("#help-text00").text(txts[0]);
					$("#help-text01").text(txts[1]);
					$("#help-text02").text(txts[2]);
				}

				$("#message").fadeIn("slow");
	    	$("#message a.close-notify").click(function() {
	        	$("#message").fadeOut("slow");
	        	return false;
	    	});


	    	function resizemap() {
	    		var minheight = parseInt($("div[data-role='page']").css("min-height"),10);
		    	var headerheight = parseInt($("div[data-role='header']").css("height"),10);
		    	var h = Math.max(420, minheight-headerheight);
		    	$("#map").css("min-height", h+"px");
		    	console.log(minheight);
		    	console.log(headerheight);
	    	}
	    	resizemap();
	    	$(window).resize(resizemap);

			});
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

			#message {
				padding: 10px;
				text-align: center;
				background-color: #ccc;
				color: #000;
				margin-bottom: 20px;
			}

			#message a {
				float: right;
				font-size: 16px;
			}

      .ui-content {
        padding: 0;
        height: 100%;
      }
      
      #map {
        position: relative;
        width: 100%;
        height: 100%;
        min-height: 800px;
        z-index: 1;
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
      
      .leaflet-popup-content {
        color: black;
        text-shadow: none;
      }
      
      .leaflet-control-attribution {
        text-shadow: none;
        text-decoration: none;
      }
		</style>
	</head>
<body>

<div data-role="page" id="home" data-theme="a">

	<div data-role="header" class="malaria-header">
		<h1>Welcome</h1>
		<a href="#info" data-icon="info" data-iconpos="notext">Info</a>
		<a href="#report" data-icon="plus" data-iconpos="notext">Report incident</a>
	</div>

	<div data-role="content">
		<?php include 'map.php'; ?>
	</div>

</div>

<div data-role="page" id="info" data-theme="a">

	<div data-role="header" class="malaria-header">
		<h1>Information</h1>
		<a href="#home" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
	</div>

	<div data-role="content">

		<h2>About</h2>
		3.3 billion people, or half the world's population is at risk for malaria. 
		This application will mobile technology to crowd source the spread of malaria to better inform preventative measures.
		<br><br>
		<strong>Mapping Malaria</strong> was created by 
		Alisa Nguyen, Deborah Alves, Joy Ming, and Julie Zhang for PennApps Fall 2013.

		<h2>Web Help</h2>
		The homepage will display the visualization of data.
		There is an option in the top right corner to add a report of an incidence of malaria. 
		Please fill out each of the fields to the best of your ability. 
		None of them are required, but more data will help create a better picture of the state of the spread of malaria.

		<h2>SMS Help</h2>
		To initialize a malaria incidence report, send "REPORT" to (425) 728-7442.
		You will then be presented a series of the prompts, with each of the prompts detailed below:
		<ul data-role="listview" data-inset="true">
			<? foreach ($help_text as $key=>$value): ?>
			<li data-icon="false"><a href="#help" onclick="set_help(<?= htmlspecialchars(json_encode($value)) ?>)"><?=$value[0]?></a></li>
			<? endforeach; ?>
		</ul>

	</div>

</div>

<div data-role="dialog" id="help" data-theme="a">

	<div data-role="header">
		<h1>Help text</h1>
	</div>

	<div data-role="content">
		<h2 id="help-text00"></h2>
		<p style="margin: none;" id="help-text01"></p>
		<p id="help-text02"></p>
	</div>

</div>

<div data-role="page" id="report" data-theme="a">

	<div data-role="header" class="malaria-header">
		<h1>Report</h1>
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
				<fieldset data-role="controlgroup" id="diagnosed-radio">
					<legend>Diagnosed by doctor?</legend>
					<input type="radio" name="diagnosed" id="radio-yes" onclick="$('#diagnosed-datepicker').show(200)" value="y"/>
					<label for="radio-yes">Yes</label>
					<input type="radio" name="diagnosed" id="radio-no" onclick="$('#diagnosed-datepicker').hide(200)" value="n"/>
					<label for="radio-no">No</label>
				</fieldset>
			</div>
			<div data-role="fieldcontain" id="diagnosed-datepicker" style="display:none;">
				<label for="date-diagnosed">Date of diagnosis:</label>
				<input type="date" name="date-diagnosed" id="date-diagnosed" value=""/>
			</div>
			<?
				date_default_timezone_set("America/New_York");
				$curdate = date('Y-m-d h:i:s', time()); 
			?>
			<input type="hidden" name="currentdate" value="<?= $curdate ?>"/>
			<input type="hidden" name="latitude" id="location-latitude" />
			<input type="hidden" name="longitude" id="location-longitude" />
			<button type="submit" name="submit" value="submit-value">Submit</button>
		</form>
	</div>

</div>

</body>
</html>