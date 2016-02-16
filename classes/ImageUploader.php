<?php
namespace Classes;


class ImageUploader extends Uploader {

	public $quality = 90;
	public $resize = false;
	public $ratio_crop = false;

	public $file_dest_width = 0;
	public $file_dest_height = 0;

	private $file_src_type = '';
	private $file_src_subtype = '';

	private $file_dest_type = '';
	private $file_dest_subtype = '';

	public $file_src_width = 0;
	public $file_src_height = 0;

	public $ratio = 0;
	private $ratio_dest = 0;
	private $zoom = false;


	public $wmark_path = '';
	public $wmark_h = 'R';
	public $wmark_v = 'B';
	public $wmark_ratio = 45; // percent of initial image

	private $accepted_types = array('image');
	private $accepted_subtypes = array(
		'gif'=>'gif',
		'jpg'=>'jpg',
		'jpeg'=>'jpg',
		'pjpeg'=>'jpg',
		'x-png'=>'png',
		'png'=>'png',
//		'bmp'=>'bmp',
//		'x-ms-bmp'=>'bmp',
//		'x-windows-bmp'=>'bmp',
	);
	public function __construct($file) {
		parent::__construct($file);
	}

	public function __destruct() {}

	public function setDestMime($mime) {
		list($this->file_dest_type, $this->file_dest_subtype) = explode('/', $mime);
		if (! in_array($this->file_dest_type, $this->accepted_types) || !in_array($this->file_dest_subtype, array_keys($this->accepted_subtypes))) {
			return false;
		}
		$this->file_dest_subtype = $this->accepted_subtypes[$this->file_dest_subtype];
		return true;
	}

	private function setSrcMime($mime) {
		list($this->file_src_type, $this->file_src_subtype) = explode('/', $this->file_src_mime);

		if (! in_array($this->file_src_type, $this->accepted_types) || !in_array($this->file_src_subtype, array_keys($this->accepted_subtypes))) {
			return false;
		}
		$this->file_src_subtype = $this->accepted_subtypes[$this->file_src_subtype];
		return true;
	}

	public function process() {
		if (!parent::prepare()){
			return false;
		}

		if (!$this->setSrcMime($this->file_src_mime)) {
			return false;
		}

		$src_size = getimagesize($this->file_src_tmp_name);
		if (!$src_size) {
			return false;
		}

		$this->file_src_width = $src_size[0];
		$this->file_src_height = $src_size[1];

		$this->ratio = round($this->file_src_width / $this->file_src_height, 2);

		// compute destination width / height
		if ($this->file_dest_width == 0 && $this->file_dest_height == 0) {
			$this->file_dest_width = $this->file_src_width;
			$this->file_dest_height = $this->file_src_height;
		}
		elseif ($this->file_dest_width == 0) {
			$this->file_dest_width = (int) round($this->file_dest_height * $this->ratio);
		}
		elseif ($this->file_dest_height == 0) {
			$this->file_dest_height = (int) round($this->file_dest_width / $this->ratio);
		}

		if ($this->file_dest_width!=$this->file_src_width || $this->file_dest_height != $this->file_src_height) {
			$this->resize = true;
		}

		$this->ratio_dest = round($this->file_dest_width / $this->file_dest_height, 2);

		if ($this->file_src_width < $this->file_dest_width && $this->file_src_height < $this->file_dest_height) {
			$this->zoom = true;
		}

		$src_img = null;
		switch ($this->file_src_subtype) {
			case 'jpg':
				$src_img = imagecreatefromjpeg($this->file_src_tmp_name);
				break;

			case 'png':
				$src_img = imagecreatefrompng($this->file_src_tmp_name);
				break;

			case 'gif':
				$src_img = imagecreatefromgif($this->file_src_tmp_name);
				break;

			case 'bmp':
				$src_img = imagecreatefrombmp($this->file_src_tmp_name);
		}
		if(! $src_img) {
			return false;
		}

		$dest_img = imagecreatetruecolor($this->file_dest_width, $this->file_dest_height);
		$color_white = imagecolorallocate($dest_img, 255,255,255);
		imagefilledrectangle($dest_img, 0, 0, $this->file_dest_width, $this->file_dest_height, $color_white);


		if (! $this->resize) {
			imagecopy($dest_img, $src_img, 0, 0, 0, 0, $this->file_src_width, $this->file_src_height);
		} else {
			if ($this->ratio == $this->ratio_dest) {
				imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $this->file_dest_width, $this->file_dest_height, $this->file_src_width, $this->file_src_height);
			} else {

				// Resize and crop closer to the center //
				if($this->ratio_crop) {
					if ($this->ratio_dest <= 1) {
						$scale = $this->file_src_height / $this->file_dest_height;
					} else {
						$scale = $this->file_src_width / $this->file_dest_width;
					}
					$src_rel_width = (int) round($this->file_dest_width * $scale);
					$src_rel_height = (int) round($this->file_dest_height * $scale);
					$src_x = (int) round(($this->file_src_width - $src_rel_width) / 2);
					$src_y = (int) round(($this->file_src_height - $src_rel_height) / 2);


					// Negative margins //
					if ($src_y < 0) {
						$src_x = (int) abs($this->ratio_dest * $src_y);
						$src_y = 0;
						$src_rel_width = $src_rel_width - 2 * ($src_x);
						$src_rel_height = $this->file_src_height;
					}
					if ($src_x < 0) {
						$src_y = (int) abs($src_x / $this->ratio_dest);
						$src_x = 0;
						$src_rel_width = $this->file_src_width;
						$src_rel_height = $src_rel_height - 2 * ($src_y);
					}

					imagecopyresampled($dest_img, $src_img, 0, 0, $src_x, $src_y, $this->file_dest_width, $this->file_dest_height, $src_rel_width, $src_rel_height);
				}

				// Resize image, but dont crop - ToDo - test negative margins if they will appear
				else {

					if ($this->ratio_dest <= 1) {
						$scale = $this->file_src_width / $this->file_dest_width;
					} else {
						$scale = $this->file_src_height / $this->file_dest_height;
					}

					$dest_rel_height = (int) round($this->file_src_height / $scale);
					$dst_y = (int) round(($this->file_dest_height - $dest_rel_height) / 2);
					$dest_rel_width = (int) round($this->file_src_width / $scale);
					$dst_x = (int) round(($this->file_dest_width - $dest_rel_width) / 2);
					imagecopyresampled($dest_img, $src_img, $dst_x, $dst_y, 0, 0, $dest_rel_width, $dest_rel_height, $this->file_src_width, $this->file_src_height);
				}
			}
		}

		$parts = explode('.', $this->file_dest_name);
		$name_body = $parts[0];
		$this->file_dest_name = $this->generateName($this->file_dest_path, $name_body, $this->file_dest_subtype);

		//
		//	Watermark
		//
		$wimage = null;
		if (!empty($this->wmark_path)) {
			if (!is_file($this->wmark_path)) {
				$this->error = "No such watermark file ". $this->wmark_path;
			} elseif (!in_array($this->wmark_h, array('L','C','R'))) {
				$this->error = "Watermark horisontal position should be one of: L,C,R (Left, Center, Right)";
			} elseif (!in_array($this->wmark_v, array('T','M','B'))) {
				$this->error = "Watermark vertical position should be one of: T,M,B (Top, Middle, Bottom)";
			} elseif ($this->wmark_ratio < 0 || $this->wmark_ratio > 100) {
				$this->error = "Watermark resize ratio should be a percent between 0 and 100 of the initial image";
			} else {
				$wimg_info = getimagesize($this->wmark_path);
				if (!$wimg_info) {
					$this->error = "Watermark is not an image";
				} else {

					$spl = explode('/', $wimg_info['mime']);
					if($spl[0] != 'image' || !in_array($spl[1],$this->accepted_subtypes)) {
						$this->error = "Watermark is not an image";
					} else {

						$wimg_width = $wimg_info[0];
						$wimg_height = $wimg_info[1];

						switch ($spl[1]) {
							case 'jpg':
								$wimage = imagecreatefromjpeg($this->wmark_path);
								break;
							case 'png':
								$wimage = imagecreatefrompng($this->wmark_path);
								break;
							case 'gif':
								$wimage = imagecreatefromgif($this->wmark_path);
								break;
							case 'bmp':
								$wimage = imagecreatefrombmp($this->wmark_path);
						}

						// should we resize the watermark ? //
						if ($this->wmark_ratio > 0) {
							$wratio = $wimg_width / $wimg_height;
							if ($wratio >= 1) {
								$wimg_width = round($this->wmark_ratio * $this->file_dest_width / 100);
								$wimg_height = round($wimg_width / $wratio);
							} else {
								$wimg_height = round($this->wmark_ratio * $this->file_dest_height / 100); /// to be tested ///
								$wimg_width = round($wimg_height / $wratio);
							}
							$tmp_img = imagecreatetruecolor($wimg_width, $wimg_height);
							imagealphablending($tmp_img, false);
							imagecopyresampled($tmp_img, $wimage,0,0,0,0,$wimg_width, $wimg_height,$wimg_info[0],$wimg_info[1]);
							imagesavealpha($tmp_img, true);
							$wimage = $tmp_img;
						}

						$wdest_x = $wdest_y = 0;
						switch ($this->wmark_h) {
							case 'C':
								$wdest_x = round(($this->file_dest_width - $wimg_width) /2);
								break;
							case 'R':
								$wdest_x = round(($this->file_dest_width - $wimg_width));
								break;
						}
						switch ($this->wmark_v) {
							case 'M':
								$wdest_y = round(($this->file_dest_height - $wimg_height) /2);
								break;
							case 'B':
								$wdest_y = round(($this->file_dest_height - $wimg_height));
								break;
						}

//						imagealphablending($dest_img, true);
						imagecopy($dest_img, $wimage,$wdest_x,$wdest_y,0,0,$wimg_width,$wimg_height);
//						imagealphablending($dest_img, false);
//						imagesavealpha($dest_img,true);
						imagedestroy($wimage);
					}
				}
			}
		} // end watermark //

		switch ($this->file_dest_subtype) {
			case 'jpg':
				imagejpeg($dest_img, $this->file_dest_path . $this->file_dest_name, $this->quality);
				break;

			case 'png':
				imagepng($dest_img, $this->file_dest_path . $this->file_dest_name,round($this->quality / 10),PNG_NO_FILTER);
				break;

			case 'gif':
				imagegif($dest_img, $this->file_dest_path . $this->file_dest_name);
				break;

			case 'bmp':
				$this->imagebmp($dest_img, $this->file_dest_path . $this->file_dest_name);
				break;
		}

		imagedestroy($dest_img);

		return $this->file_dest_name;

	}


	/**
	 * Saves a BMP image
	 *
	 * This function has been published on the PHP website, and can be used freely
	 *
	 * @access public
	 */
	function imagebmp(&$im, $filename = "") {

		if (!$im) return false;
		$w = imagesx($im);
		$h = imagesy($im);
		$result = '';

		// if the image is not true color, we convert it first
		if (!imageistruecolor($im)) {
			$tmp = imagecreatetruecolor($w, $h);
			imagecopy($tmp, $im, 0, 0, 0, 0, $w, $h);
			imagedestroy($im);
			$im = & $tmp;
		}

		$biBPLine = $w * 3;
		$biStride = ($biBPLine + 3) & ~3;
		$biSizeImage = $biStride * $h;
		$bfOffBits = 54;
		$bfSize = $bfOffBits + $biSizeImage;

		$result .= substr('BM', 0, 2);
		$result .=  pack ('VvvV', $bfSize, 0, 0, $bfOffBits);
		$result .= pack ('VVVvvVVVVVV', 40, $w, $h, 1, 24, 0, $biSizeImage, 0, 0, 0, 0);

		$numpad = $biStride - $biBPLine;
		for ($y = $h - 1; $y >= 0; --$y) {
			for ($x = 0; $x < $w; ++$x) {
				$col = imagecolorat ($im, $x, $y);
				$result .=  substr(pack ('V', $col), 0, 3);
			}
			for ($i = 0; $i < $numpad; ++$i)
				$result .= pack ('C', 0);
		}

		if($filename==""){
			echo $result;
		} else {
			$file = fopen($filename, "wb");
			fwrite($file, $result);
			fclose($file);
		}
		return true;
	}
}
