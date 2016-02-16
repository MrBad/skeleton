<?php
	use Classes\Utils;

	require_once(dirname(__FILE__) . '/../../include/conf.php');

	$pattern = "'^([^\\\\]+)\\.([^$]+)$'si";


	if(preg_match($pattern, $str, $matches)) {
		Utils::pr($matches[1] .Utils::randomString(6). "." . $matches[2]);
	} else {
		echo "Nothing to match<br/>";
	}