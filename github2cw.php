<?php
/**
* GithubからのPull Requestをchatworkに通知します。
* 簡易版なので、コメント通知などは対応していません。
*/


// APIキーを設定してください
define("CHATWORK_APIKEY",'YOUR_APIKEY');


//------------------------------------------------------------------------------

define('CW_ENDPOINT_URL',"https://api.chatwork.com/v1/rooms/%%ROOM_ID%%/messages");


$cwRoomId = $_REQUEST['rid'];

// Payloadを取得
$payload = file_get_contents('php://input');

$ary = json_decode($payload,true);

if(isset($ary["pull_request"])){

	$action    = $ary["action"];
	$url       = $ary["pull_request"]["_links"]["html"]["href"];
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
        } elseif($action == 'synchronize'){
                $type = "GitHub - Update Pull Request.";
	} elseif($action == "closed"){
		if($merged_at){
			$type = "GitHub - Merged Pull Request.";
		} else{
			$type = "GitHub - Closed Pull Request.";
		}
	} elseif($action == "created"){
		print "Not message send. コメント通知は非対応です。";
		exit;

	} else{
		print "Not message send.";
		exit;
	}

	$message = $type."\n".$url."\n by ".$user."\n".$title;

	// メッセージを送信
	chatworkApiSendMessage($cwRoomId,$message);

	print "Message send.";
}

exit;

//------------------------------------------------------------------------------

/**
* chatworkAPI連携
* メッセージの送信
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

	// chatworkにメッセージ送信
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


