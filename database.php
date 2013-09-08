<?	
	// define("DB_HOST", "localhost:8888");
	// define("DB_USERNAME", "root");
	// define("DB_PASSWORD", "root");
	// define("DB_DATABASE", "pennapps");

	define("DB_HOST", "localhost");
	define("DB_USERNAME", "deborahh_penn");
	define("DB_PASSWORD", "pennapps2013");
	define("DB_DATABASE", "deborahh_pennapps");

	// connects to the database
	// TODO: handle errors
	function connect_db() {
		$link = mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
		if (!$link) {
			die("Could not connect to the database.");
		}
		$result = mysql_select_db(DB_DATABASE, $link);
		if (!$result) {
			die("Could not select database.");
		}
		return $link;
	}

	// returns a json string to be used by the visualization
	function get_data() {
		connect_db();
		$data = array();

		$fields = array("id", "gender", "onset", "diagnosed", "diagdate", "age");
		$result = mysql_query("SELECT * FROM reports");
		while ($row = mysql_fetch_array($result)) {
			foreach ($fields as $field) {
				$report[$field] = $row[$field];
			}
			$id = $row["id"];

			$symptoms = array();
			$r = mysql_query("SELECT symptom FROM symptoms WHERE report_id = $id");
			while ($row2 = mysql_fetch_array($r)) {
				array_push($symptoms, $row2["symptom"]);
			}
			$report["symptoms"] = $symptoms;

			array_push($data, $report);
		}
		return json_encode($data);
	}
?>