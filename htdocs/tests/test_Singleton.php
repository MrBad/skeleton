<?php

use Classes\Utils;
use Classes\Singleton;

require_once __DIR__ . '/../../include/conf.php';

class C1 extends Singleton
{
	/** @var  C1 */
	protected static $instance;
	public function __construct()
	{
		echo "construct C1";
	}
	public function sayHi(){
		echo "Hi";
	}
}

class C2 extends Singleton {
	/** @var  C2 */
	protected static $instance;
	public function __construct()
	{
		echo "C2";
	}
	public function sayHy() {
		echo "Hy";
	}
}

$c1 = C1::getInstance();
Utils::vd($c1);
$c1->sayHi();
$c11 = C1::getInstance();
Utils::vd($c11);
$c11->sayHi();

$c2 = C2::getInstance();
Utils::vd($c2);
$c2->sayHy();
$c22 = C2::getInstance();
$c22->sayHy();

$c13 = C1::getInstance();
$c13->sayHi();
//Utils::vd(C2::$instances);

$c1->sayHi(); $c2->sayHy();