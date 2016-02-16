<?php
namespace Classes;

//
//	ver 0.4 - deprecated LoadModel
//	ver 0.3 - added Language support
//	ver 0.2 - added LoadModel
//
class Controller {
	
	/**
	 * Last accessed URL
	 *
	 * @var string
	 */
	public $last_url = '';
	
	/**
	 * Is requested via Ajax ?
	 *
	 * @var boolean
	 */
	public $is_ajax = false;
	
	/**
	 * Used template path - default 'main'
	 *
	 * @var string
	 */
	public $template='';
	
	/**
	 * Current language - ex: 'en'
	 *
	 * @var string
	 */
	public $lang='';
	
	/**
	 * Helper for building Urls - ex: '/en'
	 *
	 * @var string
	 */
	public $lang_prefix='';
	
	/**
	 * Data sent via POST
	 *
	 * @var array
	 */
	public $data = array();
	/**
	 * Uploaded files
	 *
	 * @var array
	 */
	public $files = array();
	
	/**
	 * Sent parameters GET
	 * 
	 * @var Object stdClass $params
	 */
	public $params;
	/** @var int  */
	public $num_params = 0;
	
	/**
	 * Current page
	 * 
	 * @var int
	 */
	public $page = 0;
	/**
	 * @var View
	 */
	public $view = null;
	
	/**
	 * Attached Model
	 * 
	 * @var Model
	 */
	public $model = null;
	
	/**
	 * Requested URI, after being rewrited
	 *
	 * @var string
	 */
	public $request_uri='';
	
	/**
	 * Controllers to be load before this
	 *
	 * @var Controller
	 */
	public $pre_controllers = array();
	
	/**
	 * Controllers to be load after this
	 *
	 * @var Controller
	 */
	public $post_controllers = array();
	
	
	public function __construct() {}
	
	public function __destruct() {}
	
	/**
	 * Descrie valorile curente ale clasei
	 *
	 * @return array
	 */
	function describe(){
		return array(
			'last_url'	=> $this->last_url,
			'is_ajax'	=> $this->is_ajax,
			'template'	=> $this->template,
			'lang'	=> $this->lang,
			'lang_prefix'	=> $this->lang_prefix,
			'data'	=> $this->data,
			'files'	=> $this->files,
			'params'	=> $this->params,
			'model'	=> $this->model,
			'view'	=> $this->view,
			'request_uri'	=> $this->request_uri,
			'pre_controllers'	=> $this->pre_controllers,
			'post_controllers'	=> $this->post_controllers,
		);
	}
	/**
	 * Load other model From this controller
	 * @deprecated
	 * @param String $model
	 * @return Model
	 */
	static function LoadModel($model) {
		trigger_error('Controller::LoadModel', E_USER_DEPRECATED);
//		require_once($cfg->get('root') . $cfg->get('models') . Inflector::variablize($model) . '.php');
		$m = '\\Models\\'.$model;
		$myModel = new $m;
		$myModel->setTableName(strtolower(Inflector::tableize($model)));
		return $myModel;
	}
}
