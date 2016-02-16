<?php
use Classes\Utils;
define('ROOT', '/home/develop/edispecer/');

require_once ROOT . 'vendor/autoload.php';

$address = new Mobilpay_Payment_Address();
Utils::vd($address);