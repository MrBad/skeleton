<?php

/**
 * Class Test
 */
class Test
{
	public $asd = 'aaa';
	public $dsa;
	/**
	 * @param string $name
	 * @Inject mamasita
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}
}

require __DIR__ . '/../../include/conf.php';

$reflector = new ReflectionClass('Test');
\Classes\Utils::vd($reflector->getDocComment());