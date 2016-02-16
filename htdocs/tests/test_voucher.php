<?php

use Models\Voucher;
use Classes\Utils;

if(!defined('ROOT')){
	define('ROOT', dirname(__FILE__) . '/../../');
}

require_once ROOT . 'include/conf.php';

$voucher = new Voucher();
//for($i = 0; $i < 10; $i++)
//$voucher->generateNewVoucher();
$voucher = $voucher->getAVoucher();
Utils::pr($voucher->describe());