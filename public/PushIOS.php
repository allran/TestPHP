
// <?php 
// $deviceToken = "<4fe30143 a96f19f6 726843bc 5e7a9ce0 33818511 94853027 49f6b44f b2cfb62d>";
// $apnsHost = 'gateway.sandbox.push.apple.com';
// $apnsPort = 2195;
// $apnsCert = '/upload/ckk.pem';

// $streamContext = stream_context_create();
// stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);

// $apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2,
// STREAM_CLIENT_CONNECT, $streamContext);
// $apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) .
// chr(strlen($payload)) . $payload;
// fwrite($apns, $apnsMessage);
// socket_close($apns);
// fclose($apns);
// ?> 


/*
<?php
// Comment these lines in production mode
ini_set('display_errors','on');
error_reporting(E_ALL);


// Apns config

// true - use apns in production mode
// false - use apns in dev mode
define("PRODUCTION_MODE",true);

$serverId = 1;
$serverName = 'my-server-domain.com';

if(PRODUCTION_MODE) {
	$apnsHost = 'gateway.sandbox.push.apple.com';
} else {
	$apnsHost = 'gateway.push.apple.com';
}

$apnsPort = 2195;
if(PRODUCTION_MODE) {
	// Use a development push certificate 
	//$apnsCert = $_SERVER['DOCUMENT_ROOT'].'/apns/apns-dominos-development.pem';
	$apnsCert = '/upload/ckk.pem';
} else {
	// Use a production push certificate 
	$apnsCert = $_SERVER['DOCUMENT_ROOT'].'/apns/apns-dominos-production.pem';
}


// --- Sending push notification ---

// Insert your device token here 
$device_token = "<4fe30143 a96f19f6 726843bc 5e7a9ce0 33818511 94853027 49f6b44f b2cfb62d>"; // Some Device Token


// Notification content

$payload = array();

//Basic message
$payload['aps'] = array(
'alert' => 'testing 1,2,3..', 
'badge' => 1, 
'sound' => 'default',
);
$payload['server'] = array(
'serverId' => $serverId,
 'name' => $serverName
);
// Add some custom data to notification
$payload['data'] = array(
'foo' => "bar"
);
$payload = json_encode($payload);

$streamContext = stream_context_create();
stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
stream_context_set_option($streamContext, 'ssl', 'passphrase', "");


$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error,      $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);


$deviceToken = str_replace(" ","",substr($device_token,1,-1));
echo $deviceToken;
$apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '',      $deviceToken)) . chr(0) . chr(mb_strlen($payload)) . $payload;
fwrite($apns, $apnsMessage);


//socket_close($apns);
fclose($apns);

?>
*/