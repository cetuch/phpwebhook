<?php
function processMessage($update) {
    if($update[“queryResult”][“action”] == “input.unknown”){
		$queryText = $update[“queryResult”][“queryText”];
		$ch = curl_init(‘http://www.manager.co.th/RSS/Home/Breakingnews.xml');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$contents = curl_exec($ch);
		curl_close($ch);
		$xml = new SimpleXmlElement($contents);
		//for($i=0; $i<count($xml->channel->item); $i++){
			for($i=0; $i<5; $i++){
		$url = $xml->channel->item[$i]->link;
		$title = $xml->channel->item[$i]->title;
		$description = $xml->channel->item[$i]->description;
		$name1 .= $title.”\n”.$url.”\n\n”;
		}
		sendMessage(array(
			“source” => $update[“responseId”],
			“fulfillmentText”=>”ข่าว “.$name1,
			“payload” => array(
			“items”=>[
				array(
			“simpleResponse”=>
				array(
			“textToSpeech”=>”ข่าว “.$name1
			)
		)
	],
	), 
		));
    }else if($update["queryResult"]["action"] == "convert"){
        if($update["queryResult"]["parameters"]["outputcurrency"] == "USD"){
           $amount =  intval($update["queryResult"]["parameters"]["amountToConverte"]["amount"]);
           $convertresult = $amount * 360;
        }
         sendMessage(array(
            "source" => $update["responseId"],
            "fulfillmentText"=>"The conversion result is".$convertresult,
            "payload" => array(
                "items"=>[
                    array(
                        "simpleResponse"=>
                    array(
                        "textToSpeech"=>"The conversion result is".$convertresult
                         )
                    )
                ],
                ),
           
        ));
    }else{
        sendMessage(array(
            "source" => $update["responseId"],
            "fulfillmentText"=>"Error",
            "payload" => array(
                "items"=>[
                    array(
                        "simpleResponse"=>
                    array(
                        "textToSpeech"=>"Bad request"
                         )
                    )
                ],
                ),
           
        ));
        
    }
}
 
function sendMessage($parameters) {
    echo json_encode($parameters);
}
 
$update_response = file_get_contents("php://input");
$update = json_decode($update_response, true);
if (isset($update["queryResult"]["action"])) {
    processMessage($update);
    $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
   fwrite($myfile, $update["queryResult"]["action"]);
    fclose($myfile);
}else{
     sendMessage(array(
            "source" => $update["responseId"],
            "fulfillmentText"=>"Hello from webhook",
            "payload" => array(
                "items"=>[
                    array(
                        "simpleResponse"=>
                    array(
                        "textToSpeech"=>"Bad request"
                         )
                    )
                ],
                ),
           
        ));
}
?>
