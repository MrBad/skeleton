<?php
namespace Controllers;
use Classes\Controller;

class Errors extends Controller {
	public function e404(){
		header('HTTP/1.0 404 Not Found');
	}
}
