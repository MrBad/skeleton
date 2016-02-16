<?php

if(! defined('ROOT')) {
    define('ROOT', dirname(__FILE__) . '/../../');
}

require_once ROOT . 'include/conf.php';

require_once ROOT . 'bin/cronjobs/recomputePrice.php';

