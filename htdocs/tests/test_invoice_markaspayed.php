<?php

if(! defined('ROOT')){
	define('ROOT', dirname(__FILE__) . '/../../');
}

require_once ROOT . 'include/conf.php';

$invoice = new \Models\Invoice();
$invoice_id = 1042;
$invoice = $invoice->getById($invoice_id);

$invoice->markAsPayed();

