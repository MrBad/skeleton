<?php

namespace Classes;

class Lang
{

	/** @var  Lang */
	private static $instance;

	private $lang_file='';
	private $lang='';
	private $vars='';
	
	/**
	 * Instantiaza clasa de configurare
	 *
	 * @param String $lang_file - path to language file
	 * @param String $lang - 2 letters language
	 */
	public function __construct($lang_file, $lang) {
		$this->setLangFile($lang_file);
		$this->setLang($lang);
		$this->_parseLang();
	}

	public static function getInstance($lang_file='', $lang='')
	{
		$cfg = Config::getInstance();
		if(empty($lang)) {
			$lang = DEFAULT_LANGUAGE;
		}
		if(empty($lang_file)) {
			$lang_file = $cfg->get('root') . 'language.conf';
		}
		if (!Lang::$instance) {
			Lang::$instance = new self($lang_file, $lang);
		}
		return Lang::$instance;
	}

	public function setLangFile($lang_file) {
		if(!is_file($lang_file)) {
			trigger_error("Cannot find " . $lang_file . ". Please check path!", E_USER_ERROR);
			die();
		}
		$this->lang_file = $lang_file;
	}
	
	public function setLang($lang) {
		$this->lang = $lang;
	}
	
	/**
	 * Parseaza fisierul de configurare conforma profilului
	 *
	 */
	private function _parseLang() {
		$vars = parse_ini_file($this->lang_file, true);
		foreach($vars as $key=>$val) {
			if(!is_array($val)) {
				$this->vars[$key] = $val;
			}
		}

		foreach ($vars as $key=>$val) {
			if(is_array($val) && $key == $this->lang) {
				foreach($val as $k=>$v) {
					$this->vars[$k] = $v;
				}
			}
		}
	}
	
	/**
	 * Intoarce o variabila din fisierul de configurare
	 *
	 * @param String $var - variabila ceruta
	 * @return String
	 */
	public function get($var) {
		return $this->vars[$var];
	}
}

