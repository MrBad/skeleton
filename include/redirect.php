<?php

use Classes\Utils;


$uri = $_SERVER['REQUEST_URI'];
$match = array();
$found = false;

if ($_SERVER['SERVER_NAME'] != $cfg->get('full_hostname')) {
	//	Utils::Redirect('http://'.$cfg->get('full_hostname') .$_SERVER['REQUEST_URI'], true);
}

/**
 * Redirect missing ending slash
 */
if (preg_match("'^(/[a-z0-9\\-]+)$'i", $uri, $match)) {
	Utils::Redirect('http://' . $cfg->get('full_hostname') . $match[1] . '/', true);
} elseif (preg_match("'^(/[a-z0-9\\-]+/[a-z0-9\\-]+)$'i", $uri, $match)) {
	Utils::Redirect('http://' . $cfg->get('full_hostname') . $match[1] . '/', true);
}


$ORIG_URI_NOLANG = $_SERVER['REQUEST_URI'];
// Detect Language //
global $languages;
$language = "";
if (preg_match("'^/([a-z]{2})/'", $uri, $match)) {
	if (in_array($match[1], $languages)) {
		$uri = preg_replace("'^/" . $match[1] . "'", '', $uri);
		$language = $match[1];
		if ($language == DEFAULT_LANGUAGE) {
			Utils::Redirect($uri);
		}
		$_SERVER['REQUEST_URI'] = $uri;
		$ORIG_URI_NOLANG = $uri;
	}
}
require_once(ROOT . 'classes/Lang.php');
$langClass = \Classes\Lang::getInstance($cfg->get('root') . 'language.conf', empty($language) ? DEFAULT_LANGUAGE : $language);
