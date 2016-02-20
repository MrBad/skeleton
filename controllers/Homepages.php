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

	/**
	 * Default entry point into site
	 */
	public function index()
	{
	}

	/**
	 * about controller
	 */
	public function about(){

	}

	/**
	 * Default entry point into /admin/
	 */
	public function admin_index()
	{
	}

	/**
	 * This is run before current controller
	 */
	public function preController()
	{
		$router = Router::getInstance();
		$auth = Auth::getInstance();
		$view = View::getInstance();
		$view->assign_by_ref('auth', $auth);
		$view->assign('controller', $router->getControllerName());
		$view->assign('action', $router->getViewName());

	}

	/**
	 * This is run after current controller
	 */
	public function postController()
	{
		global $start;
		$end = Utils::getMicroTime();
		$this->view->assign('generated', round($end - $start, 3));
		$this->view->assign('mem', round(memory_get_usage() / 1024));
	}
}

