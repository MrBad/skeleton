<?php
use Classes\Utils;
use Classes\Router;

	ob_start();

	if (!defined('ROOT')) {
		define('ROOT', realpath(dirname(__FILE__) . "/../") . '/');
	}

	require_once(ROOT . '/include/conf.php');

	$start = Utils::getMicroTime();
	require_once(ROOT . '/include/redirect.php');

	//
	//	init Router
	//
	$start_ts = Utils::getMicroTime();
    global $language, $languages, $router;
	if(empty($language)) {
		$language = DEFAULT_LANGUAGE;
	}
	$router = Router::getInstance();

	$router->setLanguage($language);
	$router->setLanguageId(array_search($language, $languages));


	$router->registerPreController('homepages', 'preController', false);
	$router->registerPostController('homepages', 'postController', false);

	if (! $router->init()) {
		$router->loadMVC('errors', 'e404', false);
	}

	$router->display();

	ob_end_flush();

	$end = Utils::getMicroTime();
//	echo $end - $start_ts . "<br/>";