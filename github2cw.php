<?php
/**
* Github�����Pull Request��chatwork�ɒʒm���܂��B
* �ȈՔłȂ̂ŁA�R�����g�ʒm�Ȃǂ͑Ή����Ă��܂���B
*/


// API�L�[��ݒ肵�Ă�������
define("CHATWORK_APIKEY",'YOUR_APIKEY');


//------------------------------------------------------------------------------

define('CW_ENDPOINT_URL',"https://api.chatwork.com/v1/rooms/%%ROOM_ID%%/messages");


$cwRoomId = $_REQUEST['rid'];

// Payload���擾
$payload = file_get_contents('php://input');

$ary = json_decode($payload,true);

if(isset($ary["pull_request"])){

	$action    = $ary["action"];
	$url       = $ary["pull_request"]["url"];
	$title     = $ary["pull_request"]["title"];
	$user      = $ary["pull_request"]["user"]["login"];
	$merged_at = $ary["pull_request"]["merged_at"];

	/*
	error_log("action:".$action);
	error_log("url:".   $url   );
	error_log("title:". $title );
	error_log("user:".  $user  );
	error_log("merged_at:".  $merged_at  );
	*/

	if($action == 'opened'){
		$type = "GitHub - Created Pull Request.";
	} elseif($action == "closed"){
		if($merged_at){
			$type = "GitHub - Merged Pull Request.";
		} else{
			$type = "GitHub - Closed Pull Request.";
		}
	} elseif($action == "created"){
		print "Not message send. �R�����g�ʒm�͔�Ή��ł��B";
		exit;

	} else{
		print "Not message send.";
		exit;
	}

	$message = "----------\n".$type."\n".$url."\n by ".$user."\n".$title;

	// ���b�Z�[�W�𑗐M
	chatworkApiSendMessage($cwRoomId,$message);

	print "Message send.";
}

exit;

//------------------------------------------------------------------------------

/**
* chatworkAPI�A�g
* ���b�Z�[�W�̑��M
*/
function chatworkApiSendMessage($cwRoomId,$message){

	$message = mb_strimwidth($message,0,300,"...",'UTF-8');

	$endpointURL = str_replace('%%ROOM_ID%%', $cwRoomId, CW_ENDPOINT_URL);

	$aryData = array('body'=>$message);

	$data = http_build_query($aryData);

	$cwApikey = CHATWORK_APIKEY;

	$opts = array(
		'http'=>array(
			'method'=>"POST",
			'header'=>"X-ChatWorkToken: {$cwApikey}\r\n",
			"content" => $data
	)
	);

	$context = stream_context_create($opts);

	// chatwork�Ƀ��b�Z�[�W���M
	$resutl = file_get_contents($endpointURL, false, $context);

	print "REQUESTED.";

	if($resutl){
	    print "OK:".$resutl;
	} else{
	    print "NG:".$http_response_header[0];
	}

	print "<br />";
	print "send message:{$message}<br />";

	return true;
}


