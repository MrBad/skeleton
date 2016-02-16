<?php
function rand_string($length)
{
	$str = '';
	$rnd_arr = array_merge(range('A', 'Z'), range(1, 9));
	$len = count($rnd_arr) - 1;
	for ($i = 0; $i < $length; $i++) {
		$str .= $rnd_arr[mt_rand(0, $len)];
	}
	return $str;
}

session_start();
list($uSec, $sec) = explode(' ', microtime());
$seed = (int)(((10000000000 * (float)$uSec) ^ (float)$sec) ^ posix_getpid());
mt_srand($seed);

$_SESSION['captcha'] = rand_string(5);

$text = $_SESSION['captcha'];
$width = 120;
$height = 40;
$font_file = 'fonts/arial.ttf';
$font_size = 18;
$img = imagecreatetruecolor($width, $height);
//	imageantialias($img,true);



$white = imagecolorallocate($img, 255, 255, 255);
$black = imagecolorallocate($img, 0, 0, 0);
$rnd = mt_rand(88, 153);
$grey = imagecolorallocate($img, $rnd, $rnd, $rnd);
imagefill($img, 0, 0, $white);
//imagerectangle($img, 0, 0, $width - 1, $height - 1, $grey);


$size = imagettfbbox($font_size, 0, $font_file, $text);
$x = (($width - 1) - abs($size[2] - $size[0])) / 2;
imagettftext($img, $font_size, 0, $x, -min($size[5], $size[7]) + 8, $grey, $font_file, $text);


for ($i = 2; $i < $width - 2; $i += 2) {
	for ($j = 2; $j <= $height - 2; $j += 2) {
		$rnd = mt_rand(100, 153);
		$randColor = imagecolorallocate($img, $rnd, $rnd, $rnd);
		//imagesetpixel($img,	mt_rand(2, $width-2),	mt_rand(2, $height-2), $randcolor);
		imagesetpixel($img, mt_rand($i - 2, $i + 2), mt_rand($j - 2, $j + 2), $randColor);
		imagecolordeallocate($img, $randColor);
	}
}


header("Content-type: image/png");
imagepng($img);
imagecolordeallocate($img, $black);
imagecolordeallocate($img, $grey);
imagecolordeallocate($img, $white);
imagedestroy($img);

