<?php

namespace Classes;

use Models\Company;

require_once __DIR__ . '/../../include/conf.php';

class Acht extends Model
{
	/** @var  string */
	public $testing_field = 0;
	public function __construct()
	{
		Utils::pr('xxx');
	}
}
echo "<pre>";
$company = new Company();
$companies = $company->getAll();
foreach($companies as $c) {
	var_dump($c->Image);
//	var_dump($c->Activity);
	($c->Place = 'aaa');
	Utils::pr($c->describe());
}