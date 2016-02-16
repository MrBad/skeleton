<?php
namespace Classes;

class Config extends Singleton
{

	/** @var  Config */
	protected static $instance;
	private $config_file = '';
	private $profile = '';
	private $vars = '';

	/**
	 * Instantiaza clasa de configurare
	 *
	 * @param String $ini_file - pathul catre fisierul de configurare
	 * @param String $profile - profilul ales
	 * @return Config
	 */
	public function __construct($ini_file, $profile = 'viorel')
	{
		if (!is_file($ini_file)) {
			die('Cannot find configuration file: ' . $ini_file);
		}
		$this->config_file = $ini_file;
		$this->profile = $profile;
		$this->_parseConfig();
		Config::$instance = $this;
	}

	/**
	 * Parseaza fisierul de configurare conforma profilului
	 *
	 */
	private function _parseConfig()
	{
		$vars = parse_ini_file($this->config_file, true);
		foreach ($vars as $key => $val) {
			if (!is_array($val)) {
				$this->vars[$key] = $val;
			}
		}

		foreach ($vars as $key => $val) {
			if (is_array($val) && $key == $this->profile) {
				foreach ($val as $k => $v) {
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
	public function get($var)
	{
		return $this->vars[$var];
	}

//	public static function getInstance()
//	{
////		if (! Config::$instance) {
////			Config::$instance = new self;
////		}
//		return Config::$instance;
//	}


}

?>