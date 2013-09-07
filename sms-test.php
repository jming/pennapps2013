<?php
 
    // make an associative array of received text content
    // and appropriate response by server
    $sms_responses = array(
        "report"=>"Where are you located? Reply with location <location>, for example, the name of the nearest school.",
        "location"=>"What age is the patient? Reply with age <age>",
        "age"=>"What is the gender of the patient? Reply with gender: <gender>",
        "gender"=>"What symptoms occur? Reply with symptoms <space separated list of symptoms>. Examples of symptoms are chills, fever, headache",
        "symptoms"=>"What date did the symptoms begin? Reply with date YYYY/MM/DD",
        "help"=>"HELP TEXT HERE",
    );
 
    // Parse body of text message, if form follows
    // one of [FIVE] standard report texts, collect
    // data and reply.
 
    // if the sender is known, then greet them by name
    // otherwise, consider them just another monkey
    $text = $_REQUEST['body'];
    $text_array = explode(" ", $text);
    
    if(!$response = $sms_responses[strtolower(text_array[0])]) {
        $response = $sms_responses["help"];
    }
 
    // now respond to text
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <Sms><?php echo $response ?></Sms>
</Response>