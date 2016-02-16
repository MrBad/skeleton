<?php

define('ROOT', dirname(__FILE__) . '/../../');
require_once ROOT . 'include/conf.php';

$user = new \Models\User();
$user = $user->getById(2);

//\Classes\Utils::pr($user->describe());

//$user->upgradeUserToPremium(2);
$user->downGrade(10);

$user = $user->getById(2);
\Classes\Utils::pr($user->describe());

echo date('Y-m-d, H:i:s', $user->premium_end_ts);