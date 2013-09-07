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
?>