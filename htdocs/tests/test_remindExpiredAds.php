<?php
if(!defined('ROOT')) {
define('ROOT', realpath(dirname(__FILE__) .'/../../') . '/');
}
require_once ROOT . 'bin/cronjobs/remindExpiredAds.php';