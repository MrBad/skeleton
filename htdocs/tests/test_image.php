<!DOCTYPE html>
<html>
<head>
	<link href="/htdocs/css/pure.css" rel="stylesheet"/>
	<title>Test \Models\Image()</title>
</head>
<body>
<form method="post" enctype="multipart/form-data" class="pure-form pure-form-aligned">
	<fieldset>
		<legend>\Models\Image() test</legend>
		<div class="pure-control-group">
			<label for="image_id">Imagine:</label>
			<input type="file" name="my_image" id="image_id"/>

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
use Models\Image;

require __DIR__ . '/../../include/conf.php';

if (isset($_FILES['my_image'])) {
	$file = $_FILES['my_image'];
	$image = new Image($file, 'tests');
	$image->id = 370;

	if(! $image->validate()) {
		Utils::pr($image->validates_errors);
	}
	if($image->Save()) {
		echo "<img src=\"/upload/tests/thumbs_large/{$image->image}\">";
		Utils::pr($image->id);
		Utils::pr($image->image);
	}


}