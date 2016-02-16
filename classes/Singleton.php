<?php
namespace Classes;

class Singleton
{

//	public static $instances = [];
//	protected static $instance;

	/**
	 * @return static
	 */
	public static function getInstance()
	{
		$class = get_called_class();
		if(! isset(static::$instance)) {
			static::$instance = new $class;
		}
		return static::$instance;
	}
}

