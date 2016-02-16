<?php
namespace Controllers;
use Classes\Controller;
use Classes\Utils;

class Errors extends Controller {
	public function e404(){
		header('HTTP/1.0 404 Not Found');
	}
}
