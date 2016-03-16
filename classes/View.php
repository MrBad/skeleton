<?php
namespace Classes;

use Classes\MySmarty;

class View extends MySmarty
{

	public $controller;
	/**
	 * Parametrii de intrare via Post pt a fi redirectati in formular
	 *
	 * @var array
	 */
	public $data = array();
	public $base_server = '';
	public $request_uri = '';
	public $action = '';
	public $uid = null;
	public $lang = null;
	public $lang_id = null;
	public $name = '';
	private static $instance = null;

	public function __construct($templateName)
	{
		$cfg = Config::getInstance();
		parent::__construct();
		$this->template_dir = $cfg->get('root') . $cfg->get('smarty_templates_dir') . $templateName;
		$this->compile_dir = $cfg->get('root') . $cfg->get('tmp') . $cfg->get('smarty_compile_dir') . $templateName;
		$this->cache_dir = $cfg->get('root') . $cfg->get('tmp') . $cfg->get('smarty_cache_dir') . $templateName;
		$this->force_compile = true;
		$this->compile_check = true;
		$this->debugging = false;
		$this->error_reporting = E_ALL & ~E_NOTICE;
	}


	static public function getInstance($templateName = '')
	{
		$cfg = Config::getInstance();
		if (empty($templateName)) {
			$templateName = $cfg->get('default_template_name') . '';
		}
		if (!self::$instance) {
			self::$instance = new self($templateName);
		}
		self::$instance->caching = 0;
		self::$instance->cache_lifetime = 0;
		return self::$instance;
	}

	public function __destruct()
	{
	}

	/**
	 * Afiseaza o eroare
	 *
	 * @param string $error
	 */
	public function riseError($error)
	{
		$this->assign('err', $error);

		if ($this->name != 'edit') {
			if (empty($this->controller->data['id'])) {
				foreach ($this->controller->data as $key => $value) {
					if (is_array($value)) {
						$this->assign($key, $value);
					} else {
						$value = htmlspecialchars($value);
						$this->assign($key, $value);
					}
				}
			}
		}
		$err_msg = $this->controller->model->validates_errors;
		$this->assign_by_ref('err_msg', $err_msg);
	}

	/**
	 * Afiseaza un mesaj
	 *
	 * @param string $message
	 */
	public function showMessage($message)
	{
		$this->assign('msg', $message);
	}

	/*
	public function fetch($resource_name, $cache_id, $compile_id = null, $display = false){
//		echo $this->lang;
//		if ($this->lang == 'en') {
			return parent::fetch($resource_name, $cache_id, $compile_id, $display);
//		} else {
//			$str = parent::fetch($resource_name, $cache_id, $compile_id, $display);
			
//			if (preg_match_all("'<\s*a\s[^>]*?href\s*=\s*([\"\'])(/.*?)\\1[^>]*>'", $str, $links)) {
				
//				foreach ($links[0] as $link) {
//					echo $link;
//					$str = preg_replace("'<\s*a\s[^>]*?href\s*=\s*([\"\'])(/.*?)\\1[^>]*>'", "", $str, 1);
//				}
//			}
//		}
	}
	*/

	public function setUid($uid)
	{
		$uid = $this->lang . '|' . $uid;
		$this->uid = $uid;
	}
}

