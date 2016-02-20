<?php
namespace Classes;

/**
 * Validator class ver 0.1
 *
 *
 */
define('VALID_NOT_EMPTY', '/^.+$/');
define('alphaNumeric', '/^[a-zA-Z0-9]+$/');
define('VALID_EMAIL', '/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-](?!\.)){0,61}[a-zA-Z0-9]?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9\-](?!$)){0,61}[a-zA-Z0-9]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/');
define('VALID_NUMERIC', '/^([0-9]+([\.]?)[0-9]+)|([0-9]+)$/');
define('VALID_INTEGER', '/^[0-9]+$/');
define('VALID_FLOAT', '/^[0-9]+[\.][0-9]+|[0-9]+$/');
define('VALID_BOOLEAN', '/^[01]$/');
define('VALID_YMD_DATE', "'^(19|20)[0-9]{2}-(0[1-9]|1[0-9])-(0[1-9]|[1-3][0-9])$'");

define('VALID_ORC', "'^[JF][0-9]{2}/[^/]+/(1|2)[0-9]{3}$'");
define('VALID_USERNAME', '/^[a-z][a-z0-9]*([\.\_]?)[a-z0-9]+$/i');
define('VALID_IP', '/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/i');
define('VALID_STRING', '/^[a-z\s\']+$/i');


class Validator
{

	public function __construct()
	{
	}

	public static function minLength($str, $min)
	{
		$len = strlen($str);
		return $len < $min ? false : true;
	}

	public static function maxLength($str, $max)
	{
		$len = strlen($str);
		return $len > $max ? false : true;
	}

	public static function min($val, $min)
	{
		return $min <= (float)$val ? true : false;
	}

	public static function max($val, $max)
	{
		return $max >= (float)$val ? true : false;
	}

	public static function notEmpty($obj)
	{
		return empty($obj) ? false : true;
	}


	public static function validRoPhone($phone)
	{
//		$phone = preg_replace("'[\./ \-\(\)]+'", '', $phone);
		if (preg_match("'^(
								(\\+4|004)?07[0-9]{8}	#telefoane mobile inclusiv +4,004
							)|(
								(02|03)(58|57|48|34|59|63|31|39|38|68|55|42|64|41|67|45|51|36|46|53|66|54|43|32|62|52|65|33|49|44|61|60|69|30|47|56|40|35|50|37|72|74)[0-9]{6} # telefoane fixe inclusiv prefix, excluzand bucuresti
							)|(
								(02|03)1[0-9]{7}	# bucuresti fix
							)$'x", $phone)) {
			return true;
		}
//			if (preg_match("'^(004|\+4)?[0-9]{10}$'", $phone)) {
//				return true;
//			}
//			if (preg_match("'^021[0-9]{7}$'", $phone)) {
//				print_r($phone);
//				die();
//				return true;
//			}
		return false;
	}

	static function validFrPhone($phone)
	{
		if (preg_match("'^0[1-7]{1}(([0-9]{2}){4})|((\\s[0-9]{2}){4})|((-[0-9]{2}){4})$'", $phone)) {
			return true;
		}
		return false;
	}

	public static function validCaptcha($captcha, $clean_session = true)
	{
		if (!isset($_SESSION['captcha'])) {
			return false;
		}
		$ret = trim(strtolower($_SESSION['captcha'])) == trim(strtolower($captcha)) ? true : false;
		if ($clean_session) {
			unset($_SESSION['captcha']);
		}
		return $ret;
	}

	public function __destruct()
	{
	}

	public static function emailWasBounced($email)
	{
		$sql = Mysql::getInstance();
		$query = "SELECT count(1)
					FROM nw_nospam
					WHERE email='" . addslashes($email) . "'
					AND is_bounce='1'";
		$ret = $sql->QueryItem($query);
		return $ret > 0 ? true : false;
	}

	public static function validIBAN($iban)
	{

		// Normalize input (remove spaces and make upcase)
		$iban = strtoupper(str_replace(' ', '', $iban));

		if (preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{1,30}$/', $iban)) {
			$country = substr($iban, 0, 2);
			$check = intval(substr($iban, 2, 2));
			$account = substr($iban, 4);

			// To numeric representation
			$search = range('A', 'Z');
			$replace = [];
			foreach (range(10, 35) as $tmp)
				$replace[] = strval($tmp);
			$numstr = str_replace($search, $replace, $account . $country . '00');

			// Calculate checksum
			$checksum = intval(substr($numstr, 0, 1));
			for ($pos = 1; $pos < strlen($numstr); $pos++) {
				$checksum *= 10;
				$checksum += intval(substr($numstr, $pos, 1));
				$checksum %= 97;
			}

			return ((98 - $checksum) == $check);
		} else
			return false;
	}

	public static function validCIF($cif)
	{
		// Daca este string, elimina atributul fiscal si spatiile
		if (!is_int($cif)) {
			$cif = strtoupper($cif);
			if (strpos($cif, 'RO') === 0) {
				$cif = substr($cif, 2);
			}
			$cif = (int)trim($cif);
		}

		// daca are mai mult de 10 cifre sau mai putin de 6, nu-i valid
		if (strlen($cif) > 10 || strlen($cif) < 6) {
			return false;
		}
		// numarul de control
		$v = 753217532;

		// extrage cifra de control
		$c1 = $cif % 10;
		$cif = (int)($cif / 10);

		// executa operatiile pe cifre
		$t = 0;
		while ($cif > 0) {
			$t += ($cif % 10) * ($v % 10);
			$cif = (int)($cif / 10);
			$v = (int)($v / 10);
		}

		// aplica inmultirea cu 10 si afla modulo 11
		$c2 = $t * 10 % 11;

		// daca modulo 11 este 10, atunci cifra de control este 0
		if ($c2 == 10) {
			$c2 = 0;
		}
		return $c1 === $c2;
	}

	public static function validCNP($p_cnp)
	{
		// CNP must have 13 characters
		if (strlen($p_cnp) != 13) {
			return false;
		}
		$cnp = str_split($p_cnp);
		unset($p_cnp);
		$hashTable = array(2, 7, 9, 1, 4, 6, 3, 5, 8, 2, 7, 9);
		$hashResult = 0;
		// All characters must be numeric
		for ($i = 0; $i < 13; $i++) {
			if (!is_numeric($cnp[$i])) {
				return false;
			}
			$cnp[$i] = (int)$cnp[$i];
			if ($i < 12) {
				$hashResult += (int)$cnp[$i] * (int)$hashTable[$i];
			}
		}
		unset($hashTable, $i);
		$hashResult = $hashResult % 11;
		if ($hashResult == 10) {
			$hashResult = 1;
		}
		// Check Year
		$year = ($cnp[1] * 10) + $cnp[2];
		switch ($cnp[0]) {
			case 1  :
			case 2 : {
				$year += 1900;
			}
				break; // cetateni romani nascuti intre 1 ian 1900 si 31 dec 1999
			case 3  :
			case 4 : {
				$year += 1800;
			}
				break; // cetateni romani nascuti intre 1 ian 1800 si 31 dec 1899
			case 5  :
			case 6 : {
				$year += 2000;
			}
				break; // cetateni romani nascuti intre 1 ian 2000 si 31 dec 2099
			case 7  :
			case 8 :
			case 9 : {                // rezidenti si Cetateni Straini
				$year += 2000;
				if ($year > (int)date('Y') - 14) {
					$year -= 100;
				}
			}
				break;
			default : {
				return false;
			}
				break;
		}
		return ($year > 1800 && $year < 2099 && $cnp[12] == $hashResult);
	}


	public static function validURL($url)
	{
		return filter_var($url, FILTER_VALIDATE_URL);
	}
}
