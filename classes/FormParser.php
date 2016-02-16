<?php

namespace Classes;

class FormParser
{
	/**
	 * @param string $form
	 * @param string $baseUrl
	 * @return array
	 */
	public static function parseForm($form, $baseUrl)
	{
		$ret = [];
		if (preg_match("{<form[^>]+method[\t\\s]*=[\t\\s]*\"([^\"]+)\"[^>]*>}i", $form, $match)) {
			$ret['method'] = $match[1];
		}
		if (preg_match("{<form[^>]+action[\t\\s]*=[\t\\s]*\"([^\"]*)\"[^>]*>}", $form, $match)) {
			$ret['action'] = Utils::makeAbsoluteUrl($baseUrl, $match[1]);
		}
		if (preg_match_all("{<input[^>]+>}i", $form, $matches)) {
			foreach ($matches[0] as $inp) {
				if (preg_match("{name=\"([^\"]*)\"}i", $inp, $m)) {
					$name = $m[1];
					// TODO - if $name exists, make it array
					$ret['inputs'][$name] = '';
					if (preg_match("{value=\"([^\"]*)\"}i", $inp, $m2)) {
						$ret['inputs'][$name] = $m2[1];
					}
				}
			}
		}
		if (preg_match_all("{<textarea[^>]+name=\"([^\"]+?)\"[^>]*>([\\w\\W]*?)</textarea>}si", $form, $matches)) {
			foreach ($matches[1] as $k => $name) {
				$ret['inputs'][$name] = trim($matches[2][$k]);
			}
		}
		return $ret;
	}

	/**
	 * @param Array $formFields
	 * @return string
	 */
	public static function serializeForm($formFields)
	{
		$str = '';
		foreach ($formFields['inputs'] as $fieldName => $fieldValue) {
			$str .= '&' . urlencode($fieldName) . '=' . urlencode($fieldValue);
		}
		$str = ltrim($str, '&');
		return $str;
	}

	/**
	 * @param $string
	 * @return bool|array of matched form string
	 */
	public static function extractForms($string)
	{
		if (preg_match_all("{<form[^>]*?>[\\w\\W]*?</form>}si", $string, $match)) {
			return $match[0];
		}
		return false;
	}

}