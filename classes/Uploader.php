<?php
namespace Classes;


class Uploader
{
	
	
	public $autocreate_dir = true;
	public $autorename = true;
	
	/**
	 * Holds errors
	 *
	 * @var string
	 */
	public  $error = '';
	
	protected $file_dest_name;
	protected $file_dest_name_body;
	protected $file_dest_name_ext;
	protected $file_dest_mime;
	protected $file_dest_path;
	
	protected $file_src_name;
	protected $file_src_tmp_name;
	protected $file_src_name_body;
	protected $file_src_name_ext;
	protected $file_src_size;
	protected $file_src_error;
	protected $file_src_mime;
	
	
	
	public function __construct($file) {
		if (!isset($file['name']) | !isset($file['type']) | 
			!isset($file['tmp_name']) | !isset($file['error']) | !isset($file['size'])) {
			$this->error = 'Bad format';
			return false;
		}
		
		$this->file_src_name = $file['name'];
		$this->file_src_tmp_name = $file['tmp_name'];
		$this->file_src_size = $file['size'];
		$this->file_src_error = $file['error'];
	}
	public function __destruct() {}
	
	protected function prepare(){
		
		if (! preg_match("'^([\w\W]+)\.([a-z]+)?$'i", $this->file_src_name, $match)) {
			return false;
		}
		
		$this->file_src_name_body = $match[1];
		$this->file_src_name_ext = strtolower($match[2]);
		$this->file_src_mime = mime_content_type($this->file_src_tmp_name);
		
		$this->file_dest_name_body = $this->sanitizeName($this->file_src_name_body);
		$this->file_dest_name_ext = $this->file_src_name_ext;
		
		
		
		if ($this->autorename) {
			$this->file_dest_name = $this->generateName($this->file_dest_path, $this->file_dest_name_body, $this->file_dest_name_ext);
		} else {
			$this->file_dest_name = $this->file_dest_name_body . '.' . $this->file_dest_name_ext;
		}
		
		return true;
	}
	
	public function upload() {
		Utils::pr($this);
	}
	
	
	/**
	 * Set the upload path
	 *
	 * @param string $dir
	 * @return boolean
	 */
	public function setUploadPath($dir) {
		if (! is_dir($dir)) {
			if(! $this->rmkdir($dir)){
				return false;
			}
		}
		$this->file_dest_path = $dir;
		return true;
	}
	
	
	/**
	 * Attempt to create directories recursively
	 *
	 * @param string $dir
	 * @return boolean
	 */
	private function rmkdir($dir, $mask=0755) {
		if(is_dir($dir)){
			return true;
		} else {
			$ret_last = $this->rmkdir(dirname($dir), $mask); 
			$ret = mkdir($dir, $mask);
			return $ret && $ret_last;
		}
	}
	
	/**
	 * Sanitize a (base file) name 
	 *
	 * @param string $filename
	 * @return string
	 */
	private function sanitizeName($filename){
		$filename = basename(trim($filename));
		$filename = preg_replace("'[\W]+'", '_', $filename);
		$filename = trim($filename, '_');
		$filename = ucfirst(strtolower($filename));
		return $filename;
	}
	
	/**
	 * generate an untaken filename in a dir, preserving base name
	 *
	 * @param string $path
	 * @param string $file_base
	 * @param string $file_ext
	 * @param string $sufix
	 * @return string
	 */
	protected function generateName($path, $file_base, $file_ext, $sufix='') {
		if (is_file($path . $file_base . $sufix . '.' . $file_ext)) {
			return $this->generateName($path, $file_base, $file_ext, ++$sufix);
		}
		return $file_base . $sufix . '.' . $file_ext;
	}
	
	public function clean() {}
	
}


//class MovieUploader extends Uploader {
//
//}
//
//class MusicUploader extends Uploader {
//
//}



if (!function_exists('imagecreatefrombmp')) {
	function imagecreatefrombmp($filename)
{
 //Ouverture du fichier en mode binaire
   if (! $f1 = fopen($filename,"rb")) return FALSE;

 //1 : Chargement des entetes FICHIER
   $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
   if ($FILE['file_type'] != 19778) return FALSE;

 //2 : Chargement des entetes BMP
   $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
                 '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
                 '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
   $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
   if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
   $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
   $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
   $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
   $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
   $BMP['decal'] = 4-(4*$BMP['decal']);
   if ($BMP['decal'] == 4) $BMP['decal'] = 0;

 //3 : Chargement des couleurs de la palette
   $PALETTE = array();
   if ($BMP['colors'] < 16777216)
   {
   $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
   }

 //4 : Creation de l'image
   $IMG = fread($f1,$BMP['size_bitmap']);
   $VIDE = chr(0);

   $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
   $P = 0;
   $Y = $BMP['height']-1;
   while ($Y >= 0)
   {
   $X=0;
   while ($X < $BMP['width'])
   {
     if ($BMP['bits_per_pixel'] == 24)
       $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
     elseif ($BMP['bits_per_pixel'] == 16)
     { 
       $COLOR = unpack("n",substr($IMG,$P,2));
       $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 8)
     { 
       $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
       $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 4)
     {
       $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
       if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
       $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 1)
     {
       $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
       if    (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
       elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
       elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
       elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
       elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
       elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
       elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
       elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
       $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     else
       return FALSE;
     imagesetpixel($res,$X,$Y,$COLOR[1]);
     $X++;
     $P += $BMP['bytes_per_pixel'];
   }
   $Y--;
   $P+=$BMP['decal'];
   }

 //Fermeture du fichier
   fclose($f1);

 return $res;
}	
}
?>