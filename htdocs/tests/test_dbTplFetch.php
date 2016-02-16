<?php
use Classes\MySmarty;
use Classes\Smarty_Resource_DB;

require dirname(__DIR__ ) . '/../include/conf.php';
$smarty = new MySmarty();

//require ROOT . 'include/dbTplFetch.php';

$smarty->template_dir = $cfg->get('root') . 'views';
$smarty->compile_dir = $cfg->get('root') . $cfg->get('tmp') . $cfg->get('smarty_compile_dir');
$smarty->registerResource('db', new Smarty_Resource_DB());
$str = $smarty->fetch('db:homepages/mails/passwd_change.tpl');

echo $str;