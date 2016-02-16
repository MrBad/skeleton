<?php
namespace Classes;

//
//		v 0.4 nov 2015 - fixed admin bug
//		v 0.3 apr 2013 - language support
//	Router Ver 0.2 3 oct 2008 - fixed multiple params bug
//

class Router extends Singleton {
	/**
	 * @var Model
	 */
	private $model = '';
	/**
	 * @var View
	 */
	private $view = '';
	/**
	 * @var Controller
	 */
	private $controller = '';
	public $request_uri;
	private $lang;
	public $lang_id;
	public $is_admin = false;
	public $is_ajax = false;
	private $admin_prefix = '';
	private $base_server = '';
	private $cache = false;
	private $hkey;
	public $history_stack = array();
	
	public	$params = null;
	public	$num_params = 0;
	public 	$page = 0;
	public 	$last_url = '';
	public $template = '';
	
	const DEFAULT_CONTROLLER = 'homepage';
	const DEFAULT_VIEW = 'index';
	const DEFAULT_ADMIN_RUTE = 'admin';
	const DEFAULT_TEMPLATE = 'main';

	const DEFAULT_PK_PARAM = 0;
	private $templates = [];
	private $preControllers = [];
	private $postControllers = [];

	protected static $instance;
	//
	//	Extract parameters from URI
	//
	public function __construct() {
		global $RewriteRules;

		/// history - ToDo - cleanup///
		$this->hkey = 'hs_'.substr(md5($_SERVER['HTTP_HOST']),0,3);
		
		$sajax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest');
		
		$this->history_stack = isset($_SESSION[$this->hkey]) ? unserialize($_SESSION[$this->hkey]) : array();
		$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		$last = array_pop($this->history_stack);
		array_push($this->history_stack, $last);
		if (($last != $url) && !$sajax) {
			array_push($this->history_stack, $url);
		}
		if (count($this->history_stack) > 2) {
			array_shift($this->history_stack);
		}
		$this->last_url = $this->history_stack[0];
//		Utils::pr($this->history_stack);
//		echo $this->last_url;
		
		
		$this->request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		$this->request_uri = preg_replace("'/+'", '/', $this->request_uri);
		
		for ($i=0; $i < count($RewriteRules); $i++) {
			$rule = $RewriteRules[$i];
			$this->request_uri = preg_replace($rule['rule'], $rule['replace'], $this->request_uri);
		}
		

		$match = array();
		if (preg_match("'^/".self::DEFAULT_ADMIN_RUTE ."'i", $this->request_uri, $match)) {
			$this->is_admin = true;
			$this->admin_prefix = self::DEFAULT_ADMIN_RUTE . '_';
			$this->request_uri = preg_replace("'^/".self::DEFAULT_ADMIN_RUTE ."'i", '', $this->request_uri);
		} 
		
		if (preg_match("'/feeds'i", $this->request_uri,$match)) {
			$this->is_ajax=true;
		}
		if (preg_match("'/aj(a)?x/'i", $this->request_uri, $match) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')) {
			$this->is_ajax = true;
			if (isset($match[0])) {
				$this->request_uri = str_replace($match[0], '', $this->request_uri);
			}
		}
		
		if (preg_match("'/page([0-9]+)\\.html$'i", $this->request_uri, $match)) {
			$this->page = $match[1];
			$this->request_uri = str_replace($match[0], '', $this->request_uri);
		} elseif (preg_match("'(?|&)pg=([0-9]+)$'i", $this->request_uri, $match)) {
			$this->page = (int) $match['1'];
			$this->request_uri = str_replace($match[0], '', $this->request_uri);
		}

		$this->controller = self::DEFAULT_CONTROLLER;
		$this->view = self::DEFAULT_VIEW;
		
		// new - to be tested //
		$this->request_uri = preg_replace("'/\\?.*$'", '', $this->request_uri);
		// end
		$this->request_uri = trim($this->request_uri, '/');

		if(! $this->is_admin && preg_match("{([^/]+)/admin_([^/$]+)}i", $this->request_uri, $match)) {
			$url = '/admin/';
			if($this->is_ajax) {
				$url .= '/ajax/';
			}
			$url .= $match[1] . '/' . ($match[2] == 'index' ? '' : $match[2]);
			Utils::Redirect($url);
		}

		if (!empty($this->request_uri)) {
			
			$parts = explode('/', $this->request_uri);
			for ($i = 0, $last_param='id'; $i < count($parts); $i++) {
				if ($i == 0) {
					$this->controller = Inflector::pluralize(Inflector::singularize($parts[0]));
				} elseif ($i == 1) {
					$this->view = $parts[1];
				} elseif ($i > 1 && $i % 2) {	
					$last_param = $parts[$i];
				} elseif ($i > 1 && ($i % 2 == 0)) {
					$val = str_replace('^','/',urldecode($parts[$i])); // ugly fix stupid apache bug
					if(preg_match("'([0-9]\\|)+'", $val)) { // for checkbox like | separated values
						$tmp = explode("|", $val);
						$val = array();
						foreach ($tmp as $v) {
							if (!empty($v)) {
								$val[] = $v;
							}
						}
					}
					if(!$this->params) {
						$this->params = new \stdClass();
					}
					$last_param = preg_replace("'\\W'", '', $last_param);
					$this->params->{$last_param} = $val;
					$this->num_params++;
					
				}
			}
		}

		$this->model = Inflector::singularize($this->controller);
		
		// template switch //
		if (empty($this->template)) {
			$this->template = self::DEFAULT_TEMPLATE;
		}
		Router::$instance = $this;
	}
	
	public function __destruct() {
		$_SESSION[$this->hkey] = serialize($this->history_stack);
	}

	/**
	 * @return Router
	 */
//	public static function getInstance()
//	{
//		if(!Router::$instance) {
//			Router::$instance = new self;
//		}
//		return Router::$instance;
//	}
	
	public function loadMVC($name, $view, $recurse=false) {
		$cfg = Config::getInstance();
//		echo $this->request_uri;
//		echo "$name=>$view<hr>";
		$hasView = true;
		$name = Inflector::singularize(Inflector::variablize($name));
		$classified_name = Inflector::classify($name);

		$controller_name = '\\Controllers\\'.Inflector::pluralize($classified_name);
		$controller_path = $cfg->get('root') . $cfg->get('controllers') . Inflector::pluralize($classified_name). '.php';
		
		$model_name = '\\Models\\'.$classified_name;
		$model_path = $cfg->get('root') . $cfg->get('models') . $classified_name . '.php';
		
		$view_name = $view;

		$view_path = $cfg->get('root') . $cfg->get('views') . $this->template .'/'. Inflector::pluralize($name) . '/' . $view_name . '.tpl';

		if (! is_file($controller_path)) {
			trigger_error("Router::loadMVC() - controller [<b>$controller_path</b>] does not exists", E_USER_NOTICE);
			return false;
		}
		
		if (! is_file($model_path)) {
			trigger_error("Router::loadMVC() - model [<b>$model_path</b>] does not exists", E_USER_NOTICE);
			return false;
		}
		
		if (! is_file($view_path) && !preg_match('/delete/', $view_name)) {
			if (!$this->is_ajax) {
//				trigger_error("Router::loadMVC() - view [<b>$view_path</b>] does not exists", E_USER_NOTICE);
			}
			$hasView = false;
		}
		
//		require_once($controller_path);
//		require_once($model_path);
		if ($hasView) {
			require_once($cfg->get('root') . $cfg->get('classes') . 'View.php');
		}
		
		if (! class_exists($controller_name)) {
			trigger_error("Router::loadMVC() - controller [<b>$controller_name</b>] not defined in $controller_path", E_USER_NOTICE);
//			return false;
		}
		if (! class_exists($model_name)) {
			trigger_error("Router::loadMVC() - model [<b>$model_name</b>] not defined in $model_path", E_USER_NOTICE);
//			return false;
		}

		$controller_class = new $controller_name;
		$model_class = new $model_name;
		$view_class = $hasView ? View::getInstance($this->template) : null;
		if ($hasView) {
			$view_class->config_load($cfg->get('root').'language.conf', $this->lang);
			$view_class->controller = $controller_class;
			$view_class->compile_dir = $cfg->get('root') . $cfg->get('tmp') . $cfg->get('smarty_compile_dir') . $this->template .'/';
			$view_class->cache_dir = $cfg->get('root') . $cfg->get('tmp') . $cfg->get('smarty_cache_dir') . $this->template .'/';
		} 
		
		$controller_class->last_url = $this->last_url;
		$controller_class->is_ajax = $this->is_ajax;
		$controller_class->template = $this->template;
		$controller_class->lang = $this->lang;
		$controller_class->lang_prefix = ($this->lang == DEFAULT_LANGUAGE || empty($this->lang)) ? '' : '/'.$this->lang;
		
		if ($hasView) {
			$view_class->base_server = $this->base_server;
			$view_class->request_uri = $this->request_uri;
			$view_class->name = $this->view;
			$view_class->lang = $this->lang;
			$view_class->lang_id = $this->lang_id;
//			$view_class->cache_handler_func = 'smarty_cache_eaccelerator';
			if ($cfg->get('debug')==0) {
				$view_class->compile_check = true;
				$view_class->force_compile = false;
			} else {
				$view_class->compile_check = true;
				$view_class->force_compile = false;
			}
		}
		
		//$model_class->setTableName(Inflector::tableize($name));
		$controller_class->data = isset($_POST['data']) ? $_POST['data'] : array();
		$unset = ['validate', 'hasMany', 'hasAndBelongsToMany','hasOne','belongsTo'];
		foreach($unset as $field) {
			if(isset($controller_class->data[$field])) {
				unset($controller_class->data[$field]);
				trigger_error("UnSetting [{$field}] from \$_POST", E_USER_NOTICE);
			}
		}
		if (isset($_FILES['data'])) {
			foreach ($_FILES['data']['name'] as $key=>$val) {
				$controller_class->files[$key] = array(
					'name' => $_FILES['data']['name'][$key],
					'type' => $_FILES['data']['type'][$key],
					'tmp_name' => $_FILES['data']['tmp_name'][$key],
					'error' => $_FILES['data']['error'][$key],
					'size' => $_FILES['data']['size'][$key],
				);
			}
		}

		$controller_class->params = $this->params;
		$controller_class->num_params = $this->num_params;
		$controller_class->page = $this->page;

		if (!method_exists($controller_class, $view_name)) {
			trigger_error('Router::loadMVC() - method [<b>' . $controller_name . '::' . $view_name.'</b>] does not exists', E_USER_WARNING);
			return false;
		}
		
		$controller_class->model = $model_class;
		$controller_class->view = $view_class;
		$controller_class->request_uri = $this->request_uri;
		$model_class->view_name = $view_name;

		
//		$html = '';
		if ($hasView) {
			$view_class->compile_check = $view_class->force_compile = $cfg->get('debug')==1 ? true:false;
			$ret = call_user_func(array(&$controller_class, $view_name));
			$view_class->assign('base_server', $this->base_server);
			$view_class->assign('lang', $this->lang);
			$view_class->assign('lang_prefix', $this->lang == DEFAULT_LANGUAGE ? '' : '/'.$this->lang);
			$view_class->assign('is_ajax', $this->is_ajax);
			$view_class->assign('lang_id', $this->lang_id);
			$cdn = $cfg->get('CDN');
			if (!empty($cdn)) {
				$view_class->assign_by_ref('CDN', $cdn);
			}
			$static = $cfg->get('STATIC');
			if (!empty($static)) {
				$view_class->assign_by_ref('STATIC', $static);
			}
			if($ret !== false) {
				//$html = $view_class->fetch($view_path, $view_class->uid);
				array_push($this->templates, [
					'path'=>$view_path,
					'uid'=>$view_class->uid
				]);
			}
		} else {
			call_user_func(array(&$controller_class, $view_name));
		}
		
		/**
		 * Load pre - controllers
		 */
		if ($recurse && isset($controller_class->pre_controllers)) {
			foreach ($controller_class->pre_controllers as $pre_controller) {
				$arr = array_keys($pre_controller);
				$pre_controller_name = array_pop($arr);
				$pre_view = $pre_controller[$pre_controller_name];
				$this->loadMVC($pre_controller_name, $pre_view);
			}
		}
		
		if ($hasView) {
//			echo $html;
//			unset($html);
		}
		
		
		/**
		 * Load post - controllers
		 */
		if ($recurse && isset($controller_class->post_controllers)) {

			foreach ($controller_class->post_controllers as $post_controller) {
				$arr = array_keys($post_controller);
				$post_controller_name = array_pop($arr);
				$post_view = $post_controller[$post_controller_name];
				$this->loadMVC($post_controller_name, $post_view);
			}
		}
		
		return true;
	}
	
	function init() {

		foreach($this->preControllers as $controller) {
			$this->loadMVC($controller['name'], $controller['action'], $controller['recursive']);
		}
		$ret = $this->loadMVC($this->controller, $this->admin_prefix . $this->view, true);
		if (!$ret) {
			array_pop($this->history_stack);
		}
		foreach($this->postControllers as $controller) {
			$this->loadMVC($controller['name'], $controller['action'], $controller['recursive']);
		}
		return $ret;
	}

	public function display()
	{
		$view = View::getInstance();

		foreach($this->templates as $template) {
			$view->display($template['path'], $template['uid']);
		}
	}

	public function registerPreController($name, $action, $recursive=true)
	{
		array_push($this->preControllers, [
			'name'=>$name,
			'action'=>$action,
			'recursive'=>$recursive,
		]);
	}
	public function registerPostController($name, $action, $recursive=true)
	{
		array_push($this->postControllers, [
			'name'=>$name,
			'action'=>$action,
			'recursive'=>$recursive,
		]);
	}
	
	function setLanguage($language) {
		$this->lang = $language;
	}
	function setLanguageId($language_id) {
		$this->lang_id = $language_id;
	}
	
	public function describe(){
		return array(
			'model'	=>	$this->model,
			'controller'	=>	$this->controller,
			'view'	=>	$this->view,
			'params'	=>	$this->params,
		);
	}
	
//	function setCacheOn($cache_seconds=3600){
//		$this->cache = true;
//		$this->view->caching = true;
//		$this->view->cache_lifetime=$cache_seconds;
//		$this->view->compile_check = false;
//		$this->view->force_compile = false;
//	}
//	function setCacheOff(){
//		$this->cache = false;
//		$this->view->caching = false;
//		$this->view->compile_check = true;
//		$this->view->force_compile = true;
//	}
//	function clearCache(){
//		$this->view->clear_all_cache();
//	}

	function getControllerName(){
		return $this->controller;
	}
	function getViewName(){
		return $this->view;
	}
}

