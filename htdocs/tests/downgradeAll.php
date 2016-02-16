<?php
die('deprecated');
use Models\User;
use Classes\Utils;

if(! defined('ROOT')) {
	define('ROOT', realpath(dirname(__FILE__) . '/../../') . '/');
}

require_once ROOT . 'include/conf.php';

$u = new User();
$premium = $u->getAll(0,0,null,"AND users.is_premium='1'");
foreach ($premium as $user) {
	$user->is_premium = 0;
	$user->premium_end_ts = 0;
	$user->Save();
}
