<?php

    // set up connection to mySql database
    require_once("database.php");
    connect_db();
    
    // start the session
    session_start();
 
    // get the session varible if it exists
    $report = $_SESSION['report'];
 
    // if it doesnt, set the default
    if(!strlen($report)) {
        $report = array(
        "location"=>"",
        "symptoms"=>array(),
        "date-onset"=>"",
        "diagnose"=>"",
        "date-diagnosed"=>"",
        "age"=>"",
        "gender"=>"",
        );
    }
 
    // make an associative array of received text content
    // and appropriate response by server
    $smsResponses = array(
        "report"=>"[1/7] Where are you located? Reply with \"location [location]\". For example, the name of the nearest school. For help, reply with \"?\"",
        "location"=>"[2/7] What symptoms occur? Reply with \"symptoms [space separated list of symptoms]\". Examples of symptoms are chills, fever, headache. For help, reply with \"?\"",
        "symptoms"=>"[3/7] What date did the symptoms begin? Reply with \"date-onset YYYY-MM-DD\" For help, reply with \"?\"",
        "date-onset"=> "[4a/7] Has the patient been diagnosed with malaria by a health professional? Reply with \"diagnose [yes/no]\" For help, reply with \"?\"",
        "diagnose"=>"[4b/7] If the patient has been diagnosed, what was the date of the diagnosis? Reply with \"date-diagnosed YYYY-MM-DD\" For help, reply with \"?\"",
        "date-diagnosed"=>"[5/7] What age is the patient? Reply with \"age [age]\" or \"age -\" if you prefer not to answer. For help, reply with \"?\"",
        "age"=>"[6/7] What is the gender of the patient? Reply with \"gender [gender]\" or \"gender -\" if you prefer not to answer. For help, reply with \"?\"",
        "gender"=>"[7/7] Thank you! The malaria incidence has been recorded. Visit deborahhh.com/index.php to view the Malaria Map.",
        "?"=>"Please visit deborahhh.com/help for more information.",
    );
 
    // parse body of text message, if form follows
    // one of the standard report texts, collect data and reply
    $text = $_REQUEST['Body'];
    $textArray = explode(" ", $text);
    
    if($response = $smsResponses[strtolower($textArray[0])]) {
        if(!strcasecmp($textArray[0], "?")) {
            if(strcasecmp($textArray[0], "symptoms")) {
                $report["symptoms"] = array_slice($textArray, 1);
            }else {
                $report[strtolower($textArray[0])] = strtolower($textArray[1]);
            }
        }
    } else {
        $response = $sms_responses["?"];
    }
    
    // save it
    $_SESSION['report'] = $report;
 
    // if all fields in $_SESSION['report'] filled in, add new entry to SQL table
    $filled = 0;
    foreach($report as $rep=>$val) {
        if(!strlen($val)) {
            $filled++;
        }
    }
    
    $locs = array(
        "nakuru high school"=>array(
        "lat"=>-0.273799, "long"=>36.094011,),
        "riruta starehe school"=>array(
        "lat"=>-1.290786, "long"=>36.728618,),
        "maritati primary school"=>array(
        "lat"=>0.123591, "long"=>37.32585,),
        
    );
    
    if($filled == 0) {
        $age = mysql_real_escape_string($report["age"]);
        $gender = mysql_real_escape_string($report["gender"]);
        $onset = date(mysql_real_escape_string($report["date-onset"]));
        $diagnosed = mysql_real_escape_string($report["diagnosed"]);
        $date = date(mysql_real_escape_string($report["date-diagnosed"]));
        $symptoms = $report["symptoms"];
        $latitude = 0;
        $longitude = 0;
        if($newloc = $locs[strtolower($report['location'])]) {
            $latitude =  $newloc["lat"];
            $longitude = $newloc["long"];
        }
        date_default_timezone_set("America/New_York");
        $curdate = date('Y-m-d h:i:s', time()); 

        $result = mysql_query("INSERT INTO reports (age, gender, onset, diagnosed, diagdate, latitude, longitude, currentdate) VALUES ($age, '$gender', '$onset', '$diagnosed', '$date', '$latitude', '$longitude', '$curdate')");
        
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
 
    // now respond to text
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <Sms><?php echo($response);?></Sms>
</Response>