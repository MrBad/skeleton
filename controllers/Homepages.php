<?php
namespace Controllers;

use Classes\Auth;
use Classes\Config;
use Classes\Controller;
use Classes\Lang;
use Classes\Mysql;
use Classes\Router;
use Classes\Utils;
use Classes\View;
use Models\Load;
use Models\Message;
use Models\Truck;
use Models\User;

class Homepages extends Controller
{

	public function index()
	{
	}
	public function about(){

	}

	public function admin_index()
	{
	}

	public function preController()
	{
		$router = Router::getInstance();
		$auth = Auth::getInstance();
		$view = View::getInstance();
		$lang = Lang::getInstance();
		$view->assign_by_ref('auth', $auth);
		$view->assign_by_ref('Lang', $lang);
		$view->assign('controller', $router->getControllerName());
		$view->assign('action', $router->getViewName());

	}

	public function postController()
	{
		global $start;
		$end = Utils::getMicroTime();
		$this->view->assign('generated', round($end - $start, 3));
		$this->view->assign('mem', round(memory_get_usage() / 1024));
	}
}

