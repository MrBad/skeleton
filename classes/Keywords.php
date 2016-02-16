<?php
namespace Classes;
// v0.2 - kw multi
class Keywords {
public $special_chars = "àâäèêéëîîïôöûüæœçÀÂÄÈÊÉËÎÎÏÔÖÛÜÆŒÇ\'’";
private $stop_words = array(
		'pentru', 'din', 'mai', 'care','ale', 'lui', 'fie', 'fara', 'aceasta', 'cat', 
		'acest', 'cei', 'cea', 'asta', 
		'orice','orce',
		'catre',
		'intre',
		'daca',
		'sub', 'peste',
		'acolo',
		'sau', 
		'unor', 'unei', 'unui', 'unele',
		'alte', 'altele',
		'nici', 'doar',
		'dupa',
		'voi', 'vor',
		// altele //
		'sunt', 'buna', 'www', 'domeniul', 'oferim', 'luna', 'prima', 'asigura', 'gama', 'intreaga',
		'este', 'mult', 'etc', 'mic', 'http', 'pot', 'prin', 'toate', 'fiind', 'atat', 'pret',
		
		// de pe anunturi //
		'totu', 'rog', 'seriozitate', 'tel', 'dvd', 'aproximativ', 'max', 'pers', 'dvs', 'nimic', 'noastra', 
		'ron', 'rol', 'leu', 'usd', 'eur', 'euro', 
		'cam',
		'categoria',
		'vizualizare',
		'ultimele', 
		'adaugate',
		'decat','fost','rau','frumos',
		'intr',
		
		// fr //
		'alors','au','aucuns','aussi','autre','avant','avec','avoir','bon','car','ce','cela','ces','ceux','chaque','ci','comme','comment','dans','des','du','dedans','dehors','depuis','deux','devrait','doit','donc','dos','droite','début','elle','elles','en','encore','essai','est','et','eu','fait','faites','fois','font','force','haut','hors','ici','il','ils','je 	juste','la','le','les','leur','là','ma','maintenant','mais','mes','mine','moins','mon','mot','même','ni','nommés','notre','nous','nouveaux','ou','où','par','parce','parole','pas','personnes','peut','peu','pièce','plupart','pour','pourquoi','quand','que','quel','quelle','quelles','quels','qui','sa','sans','ses','seulement','si','sien','son','sont','sous','soyez 	sujet','sur','ta','tandis','tellement','tels','tes','ton','tous','tout','trop','très','tu','valeur','voie','voient','vont','votre','vous','vu','ça','étaient','état','étions','été','être',
		'une','suis','moi','plus','vos'
	);
	
	private function cleanUpString($string) {
		if(preg_match('/<body\s*\t*>(.*?)<\/body\s*\t*>/si', $string, $match)) {
			$string = $match[1];
		}
		// remove script //
		$string = preg_replace('/<script.*?>(.*?)<\/script.*?>/si', ' ', $string);
		$string = preg_replace('/<noscript.*?>(.*?)<\/noscript.*?>/si', ' ', $string);
		$string = preg_replace('/(<[^>]+>)/si', '\1 ', $string);
	
		// remove comments //
		$string = preg_replace('/<!--.*?-->/si', '', $string);
		$string = strip_tags($string);
		
		// remove html special chars //
		$string = html_entity_decode($string);
		$string = preg_replace('/\&[^;]{1,5};/', ' ', $string);

		
		/*
		// diacritics //
		$find[]	= "'(à|â|ä)'";
		$replace[]	= "a";
		$find[]	= "'(À|Â|Ä)'";
		$replace[]	= "A";
		
		$find[]	= "'(è|ê|é|ë)'";
		$replace[]	= "e";
		$find[]	= "'(È|Ê|É|Ë)'";
		$replace[]	= "E";
		
		$find[]	= "'(î|ï)'";
		$replace[]	= "i";
		$find[]	= "'(Î|Ï)'";
		$replace[]	= "I";
		
		$find[]	= "'(ô|ö)'";
		$replace[]	= "o";
		$find[]	= "'(Ô|Ö)'";
		$replace[]	= "O";
		
		$find[]	= "'(û|ü)'";
		$replace[]	= "u";
		$find[]	= "'(Û|Ü)'";
		$replace[]	= "U";
		
		$find[]	= "'(æ)'";
		$replace[]	= "ae";
		$find[]	= "'(Æ)'";
		$replace[]	= "AE";
		
		$find[]	= "'(œ)'";
		$replace[]	= "ce";
		$find[]	= "'(Œ)'";
		$replace[]	= "CE";
		
		$find[]	= "'(ç)'";
		$replace[]	= "c";
		$find[]	= "'(Ç)'";
		$replace[]	= "C";
		
		$string = preg_replace($find, $replace, $string);
		*/
		
		$string = preg_replace('/[^\w'.$this->special_chars.';,\.\-]+/si', ' ', $string);
		$string = ($string);
		return $string;
	}
	
	private function arrayCount($array) {

		$ret = array();
		foreach ($array as $word) {
			if (strlen($word) < 3) {
				continue;
			}

			if (in_array($word, $this->stop_words)) {
				continue;
			}
			if (isset($ret[$word])) {
				$ret[$word]['count']++;
			} else {
				$ret[$word]['count'] = 1;
			}
		}
		$total_words = 0;
		foreach ($ret as $word=>$params) {
			$total_words+=$params['count'];
		}
		foreach($ret as $k=>$v) {
			$ret[$k]['density'] = round(100 * $ret[$k]['count'] / $total_words,2);
		}
		arsort($ret);
		return $ret;
	}
	
	public function extractKeywords($string, $len = 0) {
		$string = Keywords::cleanUpString($string);
		
		$words = preg_split('/[^\w'.$this->special_chars.']+/si', $string);
		$words = Keywords::arrayCount($words);
		
		if ($len > 0 && count($words) > $len) {
			$tmp = array();
			foreach ($words as $word=>$params) {
				if ($len <= 0 ) {
					break;
				}
				$tmp[$word] = $params;
				$len--;
			}
			$words = $tmp;
		}
		
		return $words;
	}
	
	public function extractKeywordsMulti($string, $len=0, $max_num_kw=3) {
		if ($max_num_kw < 2) {
			return $this->extractKeywords($string, $len);
		}
		//$string = Keywords::cleanUpString($string);
		$words = array();

		$regex = '/';
		for ($i=0; $i < $max_num_kw; $i++) {
			$regex .= '[\w'.$this->special_chars.']+';
			if ($i < $max_num_kw-1) {
				//$regex .= '[^\w'.$this->special_chars.']+';
				$regex .= '[\s\t]+';
			}
		}
		$regex .= '/';

		$match = array();
		$offs = 0;
		while(preg_match($regex, $string, $match, PREG_OFFSET_CAPTURE, $offs)) {
//			echo $match[0][0]."<hr>";
			//$spl = preg_split('/[^\w'.$this->special_chars.']+/', $match[0][0]);
			$spl = preg_split('/[\s\t]+/', $match[0][0]);
			$offs = $match[0][1] + strlen($spl[0]);
		
			if(strlen($spl[0]) < 3 || strlen($spl[1]) < 3) {
				continue;
			}
			$words[] = $match[0][0];
		}
		$words = Keywords::arrayCount($words);
		
		if ($len > 0 && count($words) > $len) {
			$tmp = array();
			foreach ($words as $word=>$params) {
				if ($len <= 0 ) {
					break;
				}
				$tmp[$word] = $params;
				$len--;
			}
			$words = $tmp;
		}
		
		return $words;
	}
	
	function insertTags($string, $tags) {
		
		$string = preg_replace("'\{\[\[[^\]+]\]\]\}'s", '', $string);
		foreach ($tags as $k=>$tag) {
			$string = preg_replace("'(".addslashes($tag).")'is", "{[[".$k."]]}", $string);
			//$string = preg_replace_callback("'(".addslashes($tag).")'is", "gogo", $string);
			//echo $string . "<hr>";
			//die();
		}
		if (preg_match_all("'\{\[\[([^\]]+)\]\]\}'s", $string, $matches)) {
			foreach ($matches[1] as $match) {
//				echo $match;
				$string = preg_replace("'\{\[\[(".$match.")\]\]\}'s", 
							"<a href=\"/annonces/".Utils::mklink($tags[$match])."/\">".$tags[$match]."</a>", 
							$string);
			}
		}
		return $string;
	}
}

function gogo ($matches){
	Utils::pr($matches);
}
?>