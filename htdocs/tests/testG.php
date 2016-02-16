<?php
$api_key = 'AIzaSyC1UYmt1tWCR83DdZMUUPyiD9MmzI92T9g';
$api_language = 'ro';
$ret = file_get_contents("https://maps.googleapis.com/maps/api/place/details/json?key={$api_key}&language={$api_language}&placeid=ChIJT608vzr5sUARKKacfOMyBqw");
$ret = json_decode($ret);
if($ret->status === 'REQUEST_DENIED') {
	trigger_error($ret->error_message, E_USER_ERROR);
}
if($ret->status==='OK'){
	echo "OK";
};
echo "<pre>";
print_r($ret);
echo "</pre>";