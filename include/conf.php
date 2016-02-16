<?php

use Classes\Config;
use Classes\Utils;
use Classes\Mysql;

//
//	Setup basic configurations
//

date_default_timezone_set('Europe/Bucharest');
setlocale(LC_TIME, array('ro.utf-8', 'ro_RO.UTF-8', 'ro_RO.utf-8', 'ro', 'ro_RO', 'ro_RO.ISO8859-2'));

if (!defined('ROOT')) {
	define('ROOT', realpath(dirname(__FILE__) . "/../") . '/');
}

require ROOT . '/vendor/autoload.php';

require_once(ROOT . 'classes/Config.php');

$cfg = new Config(ROOT . 'config.ini', 'local'); // local section or online

error_reporting(E_ALL);
ini_set('display_errors', 'On');

session_set_cookie_params(0, '/', $cfg->get('hostname'));
session_start();

//
//	Strip input
//
if (get_magic_quotes_gpc()) {
	Utils::strip_slashes($_GET);
	Utils::strip_slashes($_POST);
	Utils::strip_slashes($_COOKIE);
	Utils::strip_slashes($_SESSION);
}

require_once(ROOT . 'include/map.php');

if ($cfg->get('is_mentenance')) {
	Utils::Redirect('/mentenanta.html');
}

//
//	Init mysql connection;
//
$sql = new Mysql($cfg->get('sql_host'), $cfg->get('sql_user'), $cfg->get('sql_pass'), $cfg->get('sql_db'), $cfg->get('sql_persistent') == 0 ? true : false);
if (!$sql->id) {
	Utils::Redirect('/mentenanta.html');
}

require_once(ROOT . 'classes/Validator.php');

global $language, $languages, $langClass;
$languages = [
	'ro',
	'en'
];
define('DEFAULT_LANGUAGE', $cfg->get('default_language'));
$language = DEFAULT_LANGUAGE;
