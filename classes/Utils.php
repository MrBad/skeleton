<?php
namespace Classes;

class Utils
{
	/**
	 * Creaza un link url-friendly
	 *
	 * @param String $string
	 * @return String
	 */
	static function mklink($string)
	{
		$find = array("'ş|ș'", "'ţ|ț'", "'ă'", "'â'", "'î'", "'Ş'", "'Ţ|Ț'", "'Ă'", "'Â'", "'Î'", "'è'", "'È'", "'é'", "'É'", "'à'", "'À'", "'ç'", "'Ç'", "'û|ü'", "'Û'", "'ö'");
		$replace = array("s", "t", "a", "a", "i", "S", "T", "A", "A", "I", 'e', 'E', 'e', 'E', 'a', 'A', 'c', 'C', 'u', 'U', 'o');
		$string = preg_replace($find, $replace, $string);
		return trim(strtolower(preg_replace('@[^\w]+@', '-', $string)), '-');
	}

	static function float($number)
	{
		$number = floatval($number);
		var_dump($number);
		return ($number);
	}

	/**
	 * Curata un string pt a-l face compatibil cu alt-uri, title, description etc
	 *
	 * @param String $string
	 * @return String
	 */
	static function mkalt($string)
	{
		$string = (preg_replace('@["]+@', ' ', $string));
		$string = strip_tags(htmlspecialchars($string));
		$string = preg_replace("'[\r\n]+'", ' ', $string);
		$string = preg_replace("'\s+'si", " ", $string);
		return $string;
	}

	/**
	 * Intoarce timpul curent secunde, ms
	 *
	 * @return float
	 */
	static function getMicroTime()
	{
//	   list($usec, $sec) = explode(" ",microtime()); 
//	   return ((float)$usec + (float)$sec); 
		return microtime(true);
	}

	/**
	 * Verifica daca stringul dat este un email
	 *
	 * @param string $email
	 * @return Boolean
	 */
	static function is_email($email)
	{
		return (preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-](?!\.)){0,61}[a-zA-Z0-9]?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9\-](?!$)){0,61}[a-zA-Z0-9]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $email) == 0 ? false : true);
	}


	/**
	 * Genereaza un string random
	 * @param int $len lungimea
	 * @param array $range
	 * @return string
	 */
	static function randomString($len, $range=[])
	{

		static $seed_initiated;

		if (!$seed_initiated) {
			list($uSec, $sec) = explode(' ', microtime());
			$seed = (int)(((10000000000 * (float)$uSec) ^ (float)$sec) ^ posix_getpid());
			mt_srand($seed);
			$seed_initiated = true;
		}

		if(!empty($range)) {
			$rndStr = $range;
		} else {
			$rndStr = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
		}
			$ret = '';

		$rndLen = count($rndStr) - 1;
		for ($i = 0; $i < $len; $i++) {
			$ret .= $rndStr[mt_rand(0, $rndLen)];
		}
		return $ret;
	}

	/**
	 * Redirect to url
	 *
	 * @param string $url
	 * @param boolean $is_301
	 */
	static function Redirect($url, $is_301 = false)
	{
		if ($is_301) {
			header("HTTP/1.1 301 Moved Permanently");
		}
		header('Location: ' . $url);
		die();
	}

	static function ParentJsRedirect($url)
	{
		echo '<script language="javascript">';
		echo 'parent.location="' . $url . '"';
		echo '</script>';
	}

	/**
	 * Stripeaza slash-urile unui array in mod recursiv
	 *
	 * @param Array $arr
	 * @return Array
	 */
	static function strip_slashes(&$arr)
	{
		if (is_array($arr)) {
			foreach ($arr as $key => $value) {
				$arr[$key] = Utils::strip_slashes($value);
			}
		} else {
			$arr = stripslashes($arr);
		}
		return $arr;
	}

	/**
	 * Verifica daca mai exista o alta instanta a acestui proces
	 *
	 * @param String $pid_file - fisierul unde se salveaza pid
	 * @return Boolean
	 */
	static function otherInstanceExists($pid_file)
	{
		if (is_file($pid_file)) {
			$pid = file_get_contents($pid_file);
			if (is_dir('/proc/' . $pid . '/')) {
				return true;
			}
		}

		// create pid file //
		$pid = posix_getpid();
		$fp = fopen($pid_file, 'w');
		if (!$fp) {
			die("Cannot create pid file $pid_file\n");
		}
		fwrite($fp, $pid);
		fclose($fp);
		return false;
	}

	static function autoRename($file)
	{
		$base = $file;
		$ext = '';
		$match = array();
		if (preg_match('/(.*)\.(.*?)$/', $file, $match)) {
			$base = $match[1];
			$ext = $match[2];
		}
		for ($i = 1; is_file($file); $i++) {
			$file = $base . $i . '.' . $ext;
		}
		return basename($file);
	}

	static function MakeRoDate($time_stamp)
	{

		$weekdays = array('Duminica', 'Luni', 'Marti', 'Miercuri', 'Joi', 'Vineri', 'Sambata', 'Duminica');
		$months = array('Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie');
		$match = array();
		if (preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', $time_stamp, $match)) {
			$year = $match[1];
			$month = $match[2];
			$day = $match[3];
			$time_stamp = mktime(0, 0, 0, $month, $day, $year);
		}

		$month = (int)date('m', $time_stamp);
		$day = (int)date('d', $time_stamp);
		$day_week = (int)date('N', $time_stamp);
		$year = (int)date('Y', $time_stamp);

		return $weekdays[$day_week] . ', ' . $day . ' ' . $months[$month - 1] . ' ' . $year;
	}

	static function niceDate($timestamp, $lang = 'ro')
	{
		$curr_time = time();
		$ret = '';

		if ($lang == 'fr') {
			$weekdays = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
			$months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
			$day_week = (int)date('N', $timestamp);

			$m = (int)date('m', $timestamp);
			$d = (int)date('d', $timestamp);
			$y = (int)date('Y', $timestamp);

			$cm = (int)date('m', $curr_time);
			$cd = (int)date('d', $curr_time);
			$cy = (int)date('Y', $curr_time);

			$diff = $curr_time - $timestamp;

			if ($diff < 60) {
				$diff = $diff < 1 ? 1 : $diff;
				$ret = 'il ia ' . $diff . ' sec.';
			} elseif ($diff < 3600) {
				$int = floor($diff / 60);
				$ret = 'il ia ' . $int . ' min.';// . ( $int > 1 ? 'e':'');
			} elseif ($diff < 3600 * 24) {
				$ret = $d == $cd ? 'aujourd\'hui' : 'hier';
				$ret .= ', ' . date('H:i', $timestamp);
			} elseif ($diff < 3600 * 24 * 7) {
				if ($d == $cd - 1) {
					$ret = 'hier';
				} else {
					$ret = $weekdays[$day_week];
				}
				$ret .= ', ' . date('H:i', $timestamp);
			} else {
				$ret = $d . ' ' . $months[$m - 1] . ' ' . $y;
			}
		} else if ($lang == 'en') {
			$weekdays = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
			$months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
			$day_week = (int)date('N', $timestamp);

			$m = (int)date('m', $timestamp);
			$d = (int)date('d', $timestamp);
			$y = (int)date('Y', $timestamp);

			$cm = (int)date('m', $curr_time);
			$cd = (int)date('d', $curr_time);
			$cy = (int)date('Y', $curr_time);

			$diff = $curr_time - $timestamp;

			if ($diff < 60) {
				$diff = $diff < 1 ? 1 : $diff;
				$ret = $diff . ' sec. ago';
			} elseif ($diff < 3600) {
				$int = floor($diff / 60);
				$ret = $int . ' min. ago';// . ( $int > 1 ? 'e':'');
			} elseif ($diff < 3600 * 24) {
				$ret = $d == $cd ? 'today' : 'yesterday';
				$ret .= ', ' . date('H:i', $timestamp);
			} elseif ($diff < 3600 * 24 * 7) {
				if ($d == $cd - 1) {
					$ret = 'yesterday';
				} else {
					$ret = $weekdays[$day_week];
				}
				$ret .= ', ' . date('H:i', $timestamp);
			} else {
				$ret = $d . ' ' . $months[$m - 1] . ' ' . $y;
			}
		} else {
			$weekdays = array('Duminica', 'Luni', 'Marti', 'Miercuri', 'Joi', 'Vineri', 'Sambata', 'Duminica');
			$months = array('Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie');
			$day_week = (int)date('N', $timestamp);

			$m = (int)date('m', $timestamp);
			$d = (int)date('d', $timestamp);
			$y = (int)date('Y', $timestamp);

			$cm = (int)date('m', $curr_time);
			$cd = (int)date('d', $curr_time);
			$cy = (int)date('Y', $curr_time);

			$diff = $curr_time - $timestamp;

			if ($diff < 60) {
				$diff = $diff < 1 ? 1 : $diff;
				$ret = 'acum ' . $diff . ' sec.';
			} elseif ($diff < 3600) {
				$int = floor($diff / 60);
				$ret = 'acum ' . $int . ' min.';// . ( $int > 1 ? 'e':'');
			} elseif ($diff < 3600 * 24) {
				$ret = $d == $cd ? 'azi' : 'ieri';
				$ret .= ', ' . date('H:i', $timestamp);
			} elseif ($diff < 3600 * 24 * 7) {
				if ($d == $cd - 1) {
					$ret = 'ieri';
				} else {
					$ret = $weekdays[$day_week];
				}
				$ret .= ', ' . date('H:i', $timestamp);
			} else {
				$ret = $d . ' ' . $months[$m - 1] . ' ' . $y;
			}
		}

		return $ret;
	}

	static function longDate($timestamp)
	{
		$ret = '';
		$weekdays = array('Duminica', 'Luni', 'Marti', 'Miercuri', 'Joi', 'Vineri', 'Sambata', 'Duminica');
		$months = array('Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie');
		$day_week = (int)date('N', $timestamp);

		$m = (int)date('m', $timestamp);
		$d = (int)date('d', $timestamp);
		$y = (int)date('Y', $timestamp);

		$ret = $weekdays[$day_week] . ', ' . $d . ' ' . $months[$m - 1] . ' ' . $y;
		return $ret;
	}

	static function IsYMDDate($ymddate)
	{
		if (preg_match("'^([12][0-9]{3})-([01][0-9])-([0-3][0-9])$'", $ymddate, $match)) {
			$y = $match[1];
			$m = $match[2];
			$d = $match[3];
			if ($y > 1 && $y < 2999 && $m > 0 && $m < 13 && $d > 0 && $d < 32) {
				return true;
			}
		}
		return false;
	}

	static function YMDDateToUnixTimestamp($ymddate)
	{
		$match = array();
		if (preg_match("'^([0-9]{4})-([0-9]{2})-([0-9]{2})$'", $ymddate, $match)) {
			$y = $match[1];
			$m = $match[2];
			$d = $match[3];

			return mktime(0, 0, 0, $m, $d, $y);
		}
		return false;
	}

	static function UnixTimestampToYMDDate($unix_timestamp)
	{
		return date('Y-m-d', $unix_timestamp);
	}


	static function sanitizeFile($file)
	{
		return preg_replace('/[^\w\.]+/', '_', $file);
	}

	static function cleanText($string)
	{
		$string = preg_replace('/<\/?o:p>/', '', $string);
		return $string;
	}

	static function cleanForMeta($string)
	{
		$string = strip_tags($string);
		$string = str_replace('"', "", $string);
		$string = preg_replace("'[\\r\\n]+'si", ' ', $string);
		$string = preg_replace("'[\\s\\t]+'si", ' ', $string);
		$string = trim($string, '- ');
		$string = substr($string, 0, 155);
		return $string;
	}


	static function liteHTML($string)
	{
		$string = strip_tags($string, "<br><em><u><strike><ol><li><ul><strong>");
		return $string;
	}

	static function mail_strip_html($html)
	{
		$txt = preg_replace("@</?(strong|b|font)[^>]*>@i", '', $html);
		$txt = preg_replace("@<\s*a\s*href\s*=[\s\"\']*([^\s\"\']+)[^>]*>([^<]*)<\s*/\s*a[^>]*>@is", "\\2 ( \\1 )", $txt); // replace hrefs
		//$txt = preg_replace("@<\s*br\s*/?\s*>@i","\n",$txt);	// replace brs
		//$txt = preg_replace("@<\s*p\s+[^>]*>@i","\n\n",$txt);	// tags
		$txt = preg_replace("/<\\s*br\\s*\\/?>/", "\n", $txt);
		$txt = strip_tags($txt);
		$txt = preg_replace("'&nbsp;'", ' ', $txt);
		$txt = html_entity_decode($txt, null, 'UTF-8');
		$txt = preg_replace("@(\n[\\s\t]{2,})+@", "\n", $txt);
		$txt = preg_replace("@\t+@", "\t", $txt);
		$txt = preg_replace("@[ ]+@", " ", $txt);
		return $txt;
	}

	/**
	 * print_r alias
	 *
	 * @param mixed $array
	 */
	static function pr($array)
	{
		echo "<pre>";
//		print_r(debug_backtrace(false));
		print_r($array);
		echo "</pre>";
	}

	/**
	 * var_dump alias
	 *
	 * @param mixed $array
	 */
	static function vd($array)
	{
		echo "<pre>";
//		print_r(debug_backtrace(false));
		var_dump($array);
		echo "</pre>";
	}

	/**
	 * Sorteaza un array cu bubble - BetaBeta
	 *
	 * @param array $array
	 * @param string $key
	 * @param int $sens
	 * @return array
	 */
	static function bubble_sort($array, $key, $sens)
	{
		do {
			$len = count($array) - 1;
			$swapped = false;

			for ($i = 0; $i < $len; $i++) {
				if ($sens == SORT_ASC) {
					if ($array[$i][$key] > $array[$i + 1][$key]) {
						$tmp = $array[$i];
						$array[$i] = $array[$i + 1];
						$array[$i + 1] = $tmp;
						$swapped = true;
					}
				} else {
					if ($array[$i][$key] < $array[$i + 1][$key]) {
						$tmp = $array[$i];
						$array[$i] = $array[$i + 1];
						$array[$i + 1] = $tmp;
						$swapped = true;
					}
				}

			}
		} while ($swapped);
		return $array;
	}

	/**
	 * Intoarce varsta unei persoane,dand data de nastere in format Y-m-d
	 *        Nota, nu am folosit mktime, pt ca nu lucreaza pe ts < 1900
	 * @param string $ymd
	 * @return integer
	 */
	static function getAgeFromYmd($ymd)
	{
		$age = false;
		list($y, $m, $d) = explode('-', $ymd);
		$cy = date('Y');
		$cm = date('m');
		$cd = date('d');

		$age = $cy - $y;
		if ($cm <= $m && $cd < $d) {
			$age--;
		}
		return $age;
	}

	static function splitWords($str)
	{
		return preg_split('/[^\w]/', $str);
	}

	static function mailDelay($num_mails_remaining, $verbose = true)
	{

		$cfg = Config::getInstance();
		if ($num_mails_remaining % $cfg->get('delay_mail_after_number_of_mails') != 0) {
			return;
		}
		$slp = rand($cfg->get('delay_mail_min_seconds_sleep'), $cfg->get('delay_mail_max_seconds_sleep'));
		if ($verbose) {
			echo "sleeping $slp s\n";
		}
		sleep($slp);
		/*
		do {
			$num_queued_mails = (int) file_get_contents($cfg->get('mail_queue_contor'));
			if ($num_queued_mails <= $cfg->get('max_mails_in_queue')) {
				break;
			}
			if ($verbose) {
				echo "Queue: $num_queued_mails in queue, sleeping ".$cfg->get('delay_on_queue_full')."s\n";
			}
			sleep($cfg->get('delay_on_queue_full'));
		} while ($num_queued_mails > $cfg->get('max_mails_in_queue'));
		*/
	}


//	static function truncate($string, $length, $etc = '', $break_words = false) {
//		if ($length == 0)
//			return '';
//		
//		if (strlen($string) > $length) {
//			$length -= strlen($etc);
//			if (!$break_words)
//				$string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
//			return substr($string, 0, $length).$etc;
//		} else
//			return $string;
//	}

	/**
	 * Compose the absolute url, by giving base url and relative path
	 *
	 * @param string $base_url
	 * @param string $url
	 * @param string $str
	 * @return string
	 */
	static function makeAbsoluteUrl($base_url, $url, $str = '')
	{
		$ret = '';

//		file_put_contents($cfg->get('root') . $cfg->get('tmp') . 'absurl.txt', $base_url . ', ' . $url . "\n", FILE_APPEND);

		$uparts = parse_url($url);
		// is absolute path //
		if (isset($uparts['scheme']) && isset($uparts['host'])) {
			return $url;
		}

		if (preg_match("'<base\s+href=\"(http://[^\"]+)\"[^>]*>'si", $str, $bmatch)) {
//			$base_url = trim($bmatch[1], '/');
			$base_url = trim($bmatch[1], '');
		}

		// relative path //
		$bparts = parse_url($base_url);
		if (!$bparts) {
			trigger_error("Invalid base_url: {$base_url}\n", E_NOTICE);
			return false;
		}

		if (!isset($bparts['scheme']) || !isset($bparts['host'])) {
			trigger_error("Invalid base_url - scheme or host: {$base_url}\n", E_USER_NOTICE);
			return false;
		}

		$ret = $bparts['scheme'] . '://' . $bparts['host'];
		if (isset($bparts['port'])) {
			$ret .= ':' . $bparts['port'];
		}
		$ret .= '/';

		if (!empty($uparts['path'])) {
			if ($uparts['path'][0] == '/') { // absolute path, use only base host
				$uparts['path'] = preg_replace("'^/'", '', $uparts['path']);
				$ret .= $uparts['path'];
			} // relative path, path from base url
			else {
				$uparts['path'] = preg_replace("'^\./'", '', $uparts['path']);
				if (isset($bparts['path'])) {
					//$ret .= ltrim(preg_replace("'[^/]+\.[^$]+$'", '', $bparts['path']), '/');
					$ret .= ltrim(preg_replace("'[^/]+$'", '', $bparts['path']), '/');
				}

				$ret .= ltrim($uparts['path'], '/');

				while (preg_match("'[^/]+/\.\./'si", $ret)) {            // if contains multiple /xxx/../ strip them
					$ret = preg_replace("'[^/]+/\.\./'si", '', $ret);
				}
			}
		}

		if (isset($uparts['query'])) {
			$ret .= '?' . $uparts['query'];
		}
		if (isset($uparts['fragment'])) {
			$ret .= '#' . $uparts['fragment'];
		}
		$ret = preg_replace("'&amp;'i", '&', $ret);
		return $ret;
	}

	static function isMobi()
	{
		$mobile_browser = '0';
//return true;

//		if (preg_match('/Mozilla/[^\(]+\(Linux; U; Android (3|4)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
//		    return false;
//		}

		if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
			$mobile_browser++;
		}

		if (isset($_SERVER['HTTP_ACCEPT']) && (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
			$mobile_browser++;
		}

		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
		$mobile_agents = array(
			'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
			'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
			'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
			'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
			'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
			'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
			'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
			'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
			'wapr', 'webc', 'winw', 'winw', 'xda ', 'xda-');

		if (in_array($mobile_ua, $mobile_agents)) {
			$mobile_browser++;
		}
		if (preg_match("'Googlebot-Mobile'i", $_SERVER['HTTP_USER_AGENT'])) {
			$mobile_browser++;
		}
		if (preg_match("'^[a-z]+/[0-9\.]+\s+\(BlackBerry;[^\)]+\)'i", $_SERVER['HTTP_USER_AGENT'])) {
			$mobile_browser++;
		}
		if (preg_match("'^[^\(]+\(iPad;.*?CPU\sOS\s[0-9_]+\slike\sMac\sOS\sX'", $_SERVER['HTTP_USER_AGENT'])) {
			$mobile_browser++;
		}
		if (isset($_SERVER['ALL_HTTP']) && strpos(strtolower($_SERVER['ALL_HTTP']), 'OperaMini') > 0) {
			$mobile_browser++;
		}
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') > 0) {
			$mobile_browser = 0;
		}
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone os') > 0) {
			$mobile_browser++;
		}
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'macintosh') > 0) {
			$mobile_browser = 0;
		}

		return $mobile_browser > 0 ? true : false;

	}

	static function getGPoint($str)
	{
		$api_key = 'AIzaSyC1UYmt1tWCR83DdZMUUPyiD9MmzI92T9g';
		$api_language = 'ro';
		$types = urlencode('(region)');
		echo $str;
		$str = urlencode($str);
		$ret = file_get_contents("https://maps.googleapis.com/maps/api/place/autocomplete/json?key={$api_key}&language={$api_language}&types=(regions)&input={$str}");
		$ret = json_decode($ret);
		if ($ret->status === 'REQUEST_DENIED') {
			trigger_error($ret->error_message, E_USER_ERROR);
		}
		if ($ret->status === 'OK') {

		};
	}

	public static function ro_date($string, $short = false, $display_hour = false)
	{
		$days = array('Duminica', 'Luni', 'Marti', 'Miercuri', 'Joi', 'Vineri', 'Sambata', 'Duminica');
		$months = array('Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie');
		$days_short = ["Lu.", "Ma.", "Mi.", "Jo.", "Vi.", "Sa.", "Du."];
		$months_short = ['ian.', 'feb.', 'mart.', 'apr.', 'mai', 'iun.', 'iul.', 'aug.', 'sept.', 'act.', 'nov.', 'dec.'];

		$didx = (int)strftime('%u', $string);
		$midx = (int)strftime('%m', $string) - 1;

		$str = $short ? "" : $days[$didx] . ', ';
		$str .= date('d', $string) . ' ' . $months[$midx] . ' ' . date('Y', $string);
		if ($display_hour) {
			$str .= ', ' . date('H:i', $string);
		}
		return $str;
	}

	/**
	 * @param string $string
	 * @param bool $really_scramble if false just return string as it is
	 * @param int $max characters to show from a word
	 * @return string
	 */
	public static function scramble($string, $really_scramble = true, $max = 4)
	{
		if (!$really_scramble) {
			return $string;
		}

		$len = strlen($string);
		if ($len <= $max) {
			return $string;
		}
		$string = preg_replace_callback("'\\w+'i", function ($word) use ($max) {
			if (strlen($word[0]) <= $max) {
				return $word[0];
			}
			return substr($word[0], 0, $max) . "...";
		}, $string);
		return $string;

	}

//	public static function scramble($string, $max=4, $force=false, $other_is_premium=0, $user_id=0) {
//		$auth = Auth::getInstance();
//		if(! $force) {
//			if($auth->isAuth() && ($auth->isPremium() || $other_is_premium || $auth->getUserId() == $user_id)) {
//				return $string;
//			}
//		}
//
//		$len = strlen($string);
//		if($len <= $max) {
//			return $string;
//		}
//		$string = preg_replace_callback("'\\w+'i", function($word) use ($max) {
//			if(strlen($word[0]) <= $max) {
//				return $word[0];
//			}
//			return substr($word[0], 0, $max) . "...";
//		}, $string);
//		return $string;
//
//	}

	public static function objToArray($obj)
	{
		$ret = [];
		foreach ($obj as $prop => $value) {
			if (is_object($value)) {
				$ret[$prop] = self::objToArray($value);
			} else {
				$ret[$prop] = $value;
			}
		}
		return $ret;
	}

	function truncate($string, $length = 80, $etc = '...',
					  $break_words = false, $middle = false)
	{
		if ($length == 0)
			return '';

		if (mb_strlen($string, 'utf-8') > $length) {
			$length -= min($length, mb_strlen($etc, 'utf-8'));
			if (!$break_words && !$middle) {
				$string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length + 1, 'utf-8'));
			}
			if (!$middle) {
				return mb_substr($string, 0, $length, 'utf-8') . $etc;
			} else {
				return mb_substr($string, 0, $length / 2, 'utf-8') . $etc . mb_substr($string, -$length / 2, 'utf-8');
			}
		} else {
			return $string;
		}
	}

	public static function sanitizeCIF($cif) {
		$cif = strtoupper($cif);
		$cif = preg_replace("{[\\s\t]}", '', $cif);
		return $cif;
	}
}

if (!function_exists('mime_content_type')) {
	function mime_content_type($file, $method = 0)
	{
		if ($method == 0) {
			ob_start();
			system('/usr/bin/file -i -b ' . realpath($file));
			$type = ob_get_clean();

			$parts = explode(';', $type);

			return trim($parts[0]);
		}
		return 'application/octet-stream';
	}
}
