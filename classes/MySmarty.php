<?php
namespace Classes;
class MySmarty extends \SmartyBC {
	public function __construct(array $options = array())
	{
		$cfg = Config::getInstance();

		parent::__construct($options);
		$this->registerResource('db', new Smarty_Resource_DB());
		$this->register_modifier('mklink', ['\Classes\Utils', 'mklink']);
		$this->register_modifier('niceDate', ['\Classes\Utils', 'niceDate']);
		$this->register_modifier('ro_date', ['\Classes\Utils', 'ro_date']);
		$this->register_modifier('mkalt', ['\Classes\Utils', 'mkalt']);
		$this->register_modifier('scramble', ['\Classes\Utils', 'scramble']);

		$this->assign('full_hostname', $cfg->get('full_hostname'));
		$this->assign('hostname', $cfg->get('hostname'));

	}
}