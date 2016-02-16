<?php

use Classes\Validator;
use Classes\Utils;
use Models\Company;;

if(!defined('ROOT')) {
	define('ROOT', realpath(dirname(__FILE__) . '/../../') . '/');
}

require_once ROOT . 'include/conf.php';

$c = new Company();

$companies = $c->getAll();
foreach ($companies as $company) {
	$user = $company->Users[0];
	if($user->account_type == 'company') {
		if(! Validator::validCIF($company->cif)) {
			$found = false;
			$cif = Utils::sanitizeCIF($company->cif);
			for($i=0; $i < 10; $i++) {
				if(Validator::validCIF($cif . $i)) {
					$found = true;
					break;
				}
			}

			if($found) {
				$company->cif = Utils::sanitizeCIF($cif.$i);
				$company->Save();
				echo $cif . $i . "<hr>";
			} else {
				echo "Cannot validate " .$company->id. ','.$company->company.', '.$company->cif. "<hr>";
			}
		}
	}
}
