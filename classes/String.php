<?php
namespace Classes;

//$cfg = Config::getInstance();

class String
{
	static function stripDiacritics($string)
	{
		$find = array("'ş'", "'ţ'", "'ă'", "'â'", "'î'", "'Ş'", "'Ţ'", "'Ă'", "'Ă'", "'Â'", "'Î'", "{Ț}","{Ö}", "{Î}", "{Ó}", '{Â}', '{Ü}', '{Û}', '{Ä}', '{«}', '{»}', '{Á}', '{Í}', '{Ç}', '{É}', '{Å}', '{Ñ}', '{Č}', '{Ș}', '{İ}', '{Ž}',
			'{Ś}', '{Ł}', '{Ę}', '{Ė}', '{Č}'
		);
		$replace = array("s", "t", "a", "a", "i", "S", "T", "A", "A", "A", "I", "T", "O",    "I",   "O",   'A', 'U', 'U', 'A', '<', '>', 'A', 'I', 'C', 'E', 'A', 'N', 'C', 'S', 'I', 'Z',
			'S', 'L', 'E', 'E', 'C'
		);

		
		$string = preg_replace($find, $replace, $string);
		return $string;
	}

	static function stripHtml($string, $liteHtml = true)
	{
		global $from, $to;


//		Utils::pr($from);

		$string = preg_replace("'<((?:no)?script)[^>]*>[\\s\\S]*?</\\1>'si", '', $string);    // remove script | noscript
		$string = preg_replace("'<!--[\\s\\S]*?-->'si", '', $string); // remove comments
		$string = preg_replace("'<style[^>]*>[^<]+</style[^>]*>'si", '', $string); // remove inline style
		$string = preg_replace("'<figcaption[^>]*>[\\w\\W]+?</figcaption[^>]*>'si", '', $string); // remove figure caption
		$string = preg_replace("'<form[^>]*>[\\w\\W]+?</form[^>]*>'si", '', $string); // remove form

//		$accepted_html_tags = '<a><b><i><em><big><strong><small><sup><sub><u><br>';
		$accepted_html_tags = '<p><br>';


		$string = strip_tags($string, $accepted_html_tags);
		$string = preg_replace("'[\r\n]+'i", ' ', $string);
		$string = preg_replace("'[\\s\t]+'i", ' ', $string);
		$string = trim($string);
		$string = preg_replace("'<p[^>]*>([\s\S]+?)</p>'si", "\\1\n", $string);
		$string = preg_replace("'<br[^>]*>'si", "\n", $string);
		$string = preg_replace("'[\r\n]+[\s\t]*[\r\n]+'si", "\n", $string);
		$string = preg_replace("'[\r\n]+[\s\t]+'si", "\n", $string);


		// remove unwanted attributes //
//		$string = preg_replace("'<a[^>]*href=\"([^\"]+)\"[^>]*>([^<]+)</a>'", '<a href="\1" rel="nofollow" target="_blank">\2</a>', $string);	// <a>
//		$string = preg_replace("'<a[^>]*href=\"javascript([^\"]+)\"[^>]*>([^<]*)</a>'", '', $string);	// <a>

		// remove empty tags //
//		$string = preg_replace("'<([a-z]+)[^>]*>[\s\r\n\t]*</\1>'", '', $string);

		// remove duplicated tags like <strong><strong>//
//		$string = preg_replace("'<([a-z]+)><\\1>([^<]+)</\\1></\\1>'i", '<\1>\2</\1>', $string);


		$search = array("'<script[^>]*?>.*?</script>'si",    // strip out javascript
			"'<[\/\!]*?[^<>]*?>'si",            // strip out html tags
			"'([\r\n])[\s]+'",                    // strip out white space
			"'&(quot|#34|#034|#x22);'i",        // replace html entities
			"'&(amp|#38|#038|#x26);'i",            // added hexadecimal values
			"'&(lt|#60|#060|#x3c);'i",
			"'&(gt|#62|#062|#x3e);'i",
			"'&(nbsp|#160|#xa0);'i",
			"'&(iexcl|#161);'i",
			"'&(cent|#162);'i",
			"'&(pound|#163);'i",
			"'&(copy|#169);'i",
			"'&(reg|#174);'i",
			"'&(deg|#176);'i",
			"'&(#39|#039|#x27|#8217|rsquo);'",
			"'&(euro|#8364);'i",
			"'&(#8221|#8220);'i",
			"'&(#8211|#8212);'i",
			"'&(#233);'i",


		);
		$replace = array("",
			"",
			"\\1",
			"\"",
			"&",
			"<",
			">",
			" ",
			'¡',
			'¢',
			'£',
			'©',
			'®',
			'°',
			'\'',
			'€',
			"\"",
			'-',
			'e',

		);

		$string = preg_replace($search, $replace, $string);
		$string = preg_replace("''si", "'", $string);
		if (preg_match_all("'&#([0-9]{1,3});'si", $string, $match)) {
			foreach ($match[1] as $k) {
				$unused = array_merge(range(0, 8), array(11), range(127, 149), range(152, 159));
				if (!in_array($k, $unused) && $k < 255) {
					$from[] = "'&#" . $k . ";'";
					$to[] = chr($k);
				}
			}
		}

//		Utils::pr($from);
//		Utils::pr($to);
//		echo $string;
//		die;
		$string = preg_replace($from, $to, $string);                        // convert html entities
		$string = preg_replace("'[\s\t]*(?=[\r\n]+)'", '', $string);        // trim empty lines
		$string = preg_replace("'[\r\n]'", "\n", $string);                    // normalize endlines
		$string = preg_replace("'[\s\t]*([\s\S]*?[\r\n])'", '\1', $string);    // trim spaces from begining of the line

		return $string;
	}

	/**
	 * Strip <script> from html string
	 * @param string $html
	 * @return string
	 */
	public static function stripScripts($html) {
		$html = preg_replace("'<((?:no)?script)[^>]*>[\\s\\S]*?</\\1>'si", '', $html);    // remove script | noscript
		return $html;
	}

	/**
	 * Strip <iframe> from html
	 * @param string $html
	 * @return string
	 */
	public static function stripIFrames($html) {
		$html = preg_replace("'<(iframe)[^>]*>[\\s\\S]*?</\\1>'si", '', $html);    // remove iframes
		return $html;
	}

}

