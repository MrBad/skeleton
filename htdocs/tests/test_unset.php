<!DOCTYPE html>
<html>
<head>
	<link href="/htdocs/css/pure.css" rel="stylesheet"/>
	<title>Test unset data in router</title>
</head>
<body>
<form method="post" class="pure-form pure-form-aligned">
	<fieldset>
		<legend>test unset data in Router</legend>
		<div class="pure-control-group">
			<label for="afield">a Field:</label>
			<input type="text" name="data[afield]" id="afield"/>
		</div>
		<div class="pure-control-group">
			<label for="validate">Validate overwrite:</label>
			<input type="text" name="data[validate]" id="validate">
			<div class="pure-controls">
				<input type="submit" class="pure-button pure-button-primary">
			</div>
		</div>
	</fieldset>
</form>
</body>
</html>

<?php

use Classes\Utils;
use Classes\Router;


require __DIR__ . '/../../include/conf.php';

$router = new Router();
$router->init();
//Utils::pr($router->describe());


