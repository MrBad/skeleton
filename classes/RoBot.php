<?php
namespace Classes;

/**
 *
 *    RoBot class v 0.14
 *        Author : Viorel Irimia
 *        Contact: viorel.irimia@gmail.com - http://antionline.ro
 *
 *        A browser like class
 *
 * 01 aug 2010 - fixed relative path on 301 redirects
 * 30 jul 2010 - fixed deflate / inflate
 * 15 feb 2010 - added setHeader
 *    11 nov 2008 - implemented Cookies
 *
 *    ToDo:
 *        - make an init sequence
 *        - trully logging
 *        - implement keep-alive / reuse sockets
 *
 */

class RoBot
{

	public $user_agent = 'Mozilla/5.0 (compatible; RoBot/0.1)';
	public $accept = 'text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5';
	public $accept_language = 'en-gb,en;q=0.5';
	public $accept_charset = 'ISO-8859-1,utf-8;q=0.7,*;q=0.7';

	public $use_gzip = true;                // shall we use gzip/deflate
	public $keep_alive = 300;                // keep-alive - not really implemented
	public $use_persistent_conn = true;        // shall we use persistent connections ?
	public $conn_timeout = 25;                // time in seconds to wait for a connection to be enstablished
	public $read_timeout = 25;                // time in seconds to wait for the server to send data

	public $referer = '';                    // referer to send
	public $use_auto_referer = true;            // post referer from page to page ?

	public $use_cookies = true;                // fetch and auto-post cookies?
	public $max_redirects = 5;

	public $log = '';                        // logging string

	public $headers = '';
	public $body = '';


	private $last_redirect_addr = '';        // last redirected addr fetched from headers
	private $has_redirected = false;        // flag - page has redirected in this fetch ?
	private $socket = null;
	private $cookies = array();
	private $status = 0;                    // response status


	private $c_encoding = '';
	private $c_length = 0;
	private $c_type = '';
	private $header_charset = '';


	private $body_is_chunked = false;    // flag -> is the body sent in chunked data?
	private $redirects = 0;                // number of redirects since last fetch
	private $last_url = '';                // last fetched url
	private $current_url = '';            // current url
	private $last_ip = null;            // last ip

	// url parts //
	private $scheme = '';
	private $host = '';
	private $port = 80;
	private $path = '';
	private $query = '';
	private $fragment = '';
	private $implemented_schemes = array('http');

	private $post_headers = array();

	private function compose_headers()
	{

		if ($this->use_auto_referer) {
			if ($this->referer == '') {
				$this->referer = $this->scheme . "://" . $this->host
					. ($this->port != 80 ? ':' . $this->port : '')
					. '/';
			} elseif ($this->last_url != '') {
				$this->referer = $this->last_url;
			}
		}
		$string = "GET " . $this->path;
		$string .= $this->query != '' ? "?" . $this->query : '';
		$string .= " HTTP/1.1\r\n"
			. "Host: " . $this->host . ($this->port != 80 ? ':' . $this->port : '') . "\r\n"
			. "User-Agent: " . $this->user_agent . "\r\n"
			. "Accept: " . $this->accept . "\r\n"
			. "Cache-Control: no-cache\r\n"
			. ($this->use_gzip ? "Accept-Encoding: gzip,deflate\r\n" : "")
			. "Accept-Language: " . $this->accept_language . "\r\n"
			. ($this->referer != '' ? "Referer: " . $this->referer . "\r\n" : '')
			. "Accept-Charset: " . $this->accept_charset . "\r\n";

		//
		//	Pass post_headers
		//
		if (!empty($this->post_headers)) {
			foreach ($this->post_headers as $post_line) {
				$string .= $post_line . "\r\n";
			}
		}

		//
		//	Pass Cookies
		//
		if ($this->use_cookies) {
			if (isset($this->cookies[$this->host]) && is_array($this->cookies[$this->host])) {
				$string .= "Cookie: ";
				foreach ($this->cookies[$this->host] as $key => $value) {
					$string .= $key . '=' . urlencode($value) . '; ';
				}
				$string = substr($string, 0, -2);
				$string .= "\r\n";
			}
		}

		$string .= ($this->keep_alive > 0 ? "Keep-Alive: " . $this->keep_alive . "\r\n" : "")
			. "Connection: " . ($this->keep_alive > 0 ? "keep-alive" : "Close") . "\r\n"
			. "\r\n";

		return $string;
	}

	/**
	 * setHeader - like headers in php ...
	 *
	 * @param string $header_line
	 */
	public function setHeader($header_line)
	{
		$this->post_headers[] = trim($header_line, "\r\n");
	}

	private function extract_headers()
	{

		$match = array();

		//
		//	Extract response code
		//
		if (preg_match("'^HTTP/1\.[01]\\s*([0-9]+)\\s*'si", $this->headers, $match)) {
			$this->status = (int)$match[1];
		}

		//
		//	Check for redirect
		//
		if (in_array($this->status, array(301, 302))) {
			$this->has_redirected = true;
			if (preg_match("'Location:\\s*([^\r\n]+)'i", $this->headers, $match)) {
				// fixed abs //
				$this->last_redirect_addr = Utils::makeAbsoluteUrl($this->current_url, $match[1]);
			}
		}

		//
		//	Extract Content-Encoding
		//
		if (preg_match("'Content-Encoding:\\s*gzip'si", $this->headers, $match)) {
			$this->c_encoding = 'gzip';
		} elseif (preg_match("'Content-Encoding:\\s*deflate'si", $this->headers, $match)) {
			$this->c_encoding = 'deflate';
		}

		//
		//	Extract Content-Length
		//
		if (preg_match("'Content-Length:\\s*([0-9]+)'si", $this->headers, $match)) {
			$this->c_length = (int)$match[1];
		}

		//
		//	Extract Content-Type
		//
		if (preg_match("'Content-Type: ([a-z]+/[a-z]+);\\s*charset=([a-z0-9-]+)'si", $this->headers, $match)) {
			$this->c_type = $match[1];
			$this->charset = $this->header_charset = strtoupper(trim($match[2]));
		}

		//
		//	Extracting cookies - ToDo use path, domain, expires
		//
		if ($this->use_cookies) {
			if (preg_match_all("'set-cookie:\\s+([^=]+)=([^;\\s\r\n]+)(?:;\\s|)(.*?)[\r\n]'si", $this->headers, $match)) {
				$domain = $this->host;
				foreach ($match[1] as $k => $v) {
					$name = $v;
					$value = urldecode($match[2][$k]);
					$this->cookies[$domain][$name] = $value;
				}
			}
		}

		$this->body_is_chunked = preg_match("'Transfer-Encoding:\\s*chunked'si", $this->headers);
	}

	/**
	 * @deprecated
	 */
	private function extract_body()
	{


		if ($this->body_is_chunked) {
			$this->log .= "Content is chunked\n";
			$tmp = '';
			$offset = 0;
			$initial_body_length = strlen($this->body);
			for (; ;) {
				$pos = strpos($this->body, "\r\n", $offset);
				$a = substr($this->body, $offset, $pos - $offset);
				$len = hexdec($a);
				if ($len <= 0 || $offset >= $initial_body_length) {
					break;
				}
				$offset += strlen($a) + 2; // 2 chars - \r\n
				$tmp .= substr($this->body, $offset, $len);
				$offset += $len + 2; // 2 chars - \r\n

			}
			$this->body = $tmp;
		}

		if ($this->c_encoding == 'gzip') {
			$this->log .= "Content is gzipped\n";
			$this->body = gzdecode($this->body);
		} elseif ($this->c_encoding == 'deflate') {
			$this->log .= "Content is deflated\n";
			$this->body = gzinflate($this->body);
		}
	}


	public function fetchHead()
	{
		$this->log .= "getting header {$this->current_url}\n";
		if (!$this->openSocket()) {
			return false;
		}

		$h = $this->compose_headers();
		$h = preg_replace("'^GET(\s+/)'si", 'HEAD\1', $h);

		if (!fwrite($this->socket, $h)) {
			$this->log .= "Cannot send headers\n";
			$this->closeSocket();
			return false;
		}

		do {
			$sock_stat = socket_get_status($this->socket);
			$line = fgets($this->socket, 4096);
			$line = trim($line, "\r\n");
			if (empty($line)) {
				break;
			}
			$this->headers .= $line . "\r\n";
		} while (!feof($this->socket) && !$sock_stat['timed_out']);

		if ($sock_stat['timed_out']) {
			$this->log .= "Socket timed out\n";
			return false;
		}

		$this->extract_headers();

		if (!$this->use_persistent_conn) {
			$this->closeSocket();
		}

		$this->last_url = $this->current_url;

		if ($this->has_redirected && $this->redirects < $this->max_redirects) {
			$this->redirects++;
			$this->log .= "Redirected to {$this->last_redirect_addr}\n";
			$this->setUrl($this->last_redirect_addr);
			$this->body = $this->fetch();
		} elseif ($this->redirects >= $this->max_redirects) {
			$this->log .= "Max redirects reached {$this->max_redirects}\n";
		}

		return $this->headers;
	}

	/**
	 * Fetching the url
	 *
	 * @return string or false
	 */
	public function fetch()
	{
//		echo $this->last_url . "-" . $this->current_url . "<hr>\n";
		$this->log .= "fetching {$this->current_url}\n";
		if (!$this->openSocket()) {
			return false;
		}

		if (!fwrite($this->socket, $this->compose_headers())) {
			$this->log .= "Cannot send headers\n";
			$this->closeSocket();
			return false;
		}


		do {
			$sock_stat = socket_get_status($this->socket);
			$line = fgets($this->socket, 4096);
			$line = trim($line, "\r\n");
			if (empty($line)) {
				break;
			}
			$this->headers .= $line . "\r\n";
		} while (!feof($this->socket) && !$sock_stat['timed_out']);

		if ($sock_stat['timed_out']) {
			$this->log .= "Socket timed out\n";
			return false;
		}

		$this->extract_headers();


		if ($this->body_is_chunked) {
			//
			//	Transfer Encoding is chunked, reading chunks
			//			- for more info see http://tools.ietf.org/html/rfc2616#page-25
			//
			do {
				$line = trim(fgets($this->socket, 4096), "\r\n");
				$bytes = hexdec($line); // how big is this chunk 
				if ($bytes == 0) {
					break;
				}
				$sock_stat = socket_get_status($this->socket);

				do {
					$buffer = fread($this->socket, $bytes);
					$len = strlen($buffer);
					if ($len == 0) {
						break;
					}
					$bytes -= $len;
					$this->body .= $buffer;
					$sock_stat = socket_get_status($this->socket);
				} while (!feof($this->socket) && !$sock_stat['timed_out'] && $bytes > 0);

				fread($this->socket, 2); // discard \r\n from the end of the chunk

			} while (!feof($this->socket) && !$sock_stat['timed_out']);
		} else {
			//
			//	Reading as usual
			//
			$bytes = $this->c_length;
			if ($bytes > 0) {
				do {
					$buffer = fread($this->socket, $bytes);
					$len = strlen($buffer);
					if ($len == 0) {
						break;
					}
					$bytes -= $len;
					$this->body .= $buffer;
					$sock_stat = socket_get_status($this->socket);
				} while (!feof($this->socket) && !$sock_stat['timed_out'] && $bytes > 0);
			} else {
				do { // crazy servers micr$ //
					$buffer = fread($this->socket, 4096);
					$len = strlen($buffer);
					if ($len == 0) {
						break;
					}
					$this->body .= $buffer;
					$sock_stat = socket_get_status($this->socket);

				} while (!feof($this->socket) && !$sock_stat['timed_out']);
			}
		}

		if ($sock_stat['timed_out']) {
			$this->log .= "Reading timed out\n";
			$this->closeSocket();
			return false;
		}

		//
		//	If content encoding is gzip inflate it
		//
		if ($this->c_encoding == 'gzip') {
			$this->body = gzdecode($this->body);
		} elseif ($this->c_encoding == 'deflate') {
			$this->body = gzinflate($this->body);
		}


		if (!$this->use_persistent_conn) {
			$this->closeSocket();
		}

		$this->last_url = $this->current_url;

		if ($this->has_redirected && $this->redirects < $this->max_redirects) {
			$this->redirects++;
			$this->log .= "Redirected to {$this->last_redirect_addr}\n";
			$this->setUrl($this->last_redirect_addr);
			$this->body = $this->fetch();
		} elseif ($this->redirects >= $this->max_redirects) {
			$this->log .= "Max redirects reached {$this->max_redirects}\n";
		}


		if (empty($this->charset)) {
			if (preg_match("'<meta[\s\t]+http-equiv=\"Content-Type\"[^\"]+content=\"[a-z]+/[a-z]+;[\s\t]*charset=([a-z0-9\-]+)\"[^>]*>'si", $this->body, $match)) {
				$this->charset = strtoupper(trim($match[1]));
			}
		}

		return $this->body;
	}

	/**
	 * Open a socket
	 *
	 * @return boolean
	 */
	private function openSocket()
	{
		$errno = $errstr = '';
		// check IP //
		$ip = gethostbyname($this->host);
		if ($ip == $this->host) {
			return false;
		}

		if ($this->use_persistent_conn && $this->keep_alive > 0) {
			if ($ip == $this->last_ip) {
				$this->last_ip = $ip;
//				echo "Reused";
//				return true;
			}
		}

		$this->last_ip = $ip;


		if ($this->use_persistent_conn) {
			$this->socket = pfsockopen($ip, $this->port, $errno, $errstr, $this->conn_timeout);
		} else {
			$this->socket = fsockopen($ip, $this->port, $errno, $errstr, $this->conn_timeout);
		}
		if (!$this->socket) {
			return false;
		}
		socket_set_timeout($this->socket, $this->read_timeout);

		return true;
	}

	/**
	 * Close the socket
	 *
	 */
	private function closeSocket()
	{
		if ($this->socket) {
			fclose($this->socket);
			$this->socket = null;
		}
	}

	/**
	 * Init vars
	 */
	private function init()
	{
		$this->headers = '';
		$this->body = '';
		$this->last_redirect_addr = '';
		$this->has_redirected = false;
		$this->status = 0;
		$this->c_encoding = '';
		$this->c_length = '';
		$this->c_type = '';
		$this->header_charset = '';
		$this->charset = '';
		$this->body_is_chunked = false;
		$this->redirects = 0;
		$this->scheme = '';
		$this->host = '';
		$this->port = 80;
		$this->path = '';
		$this->query = '';
		$this->fragment = '';
	}

	function __destruct()
	{
//		echo "Closed";
		$this->closeSocket();
	}

	/**
	 * Set url
	 *
	 * @param string $url
	 * @return boolean
	 */
	public function setUrl($url)
	{

		$url = str_replace(' ', '%20', $url);
		$url = preg_replace("'&amp;'si", '&', $url); // ?
		$this->init();

		$parts = parse_url($url);
		if (!$parts) {
			return false;
		}
		if (!isset($parts['scheme']) || !isset($parts['host'])) {
			return false;
		}
		if (!in_array($parts['scheme'], $this->implemented_schemes)) {
			return false;
		}

		$this->scheme = $parts['scheme'];
		$this->host = $parts['host'];
		$this->port = isset($parts['port']) ? (int)$parts['port'] : 80;
		$this->path = isset($parts['path']) ? $parts['path'] : '/';
		$this->query = isset($parts['query']) ? $parts['query'] : '';
		$this->fragment = isset($parts['fragment']) ? $parts['fragment'] : '';

		$this->current_url = $url;
//		echo htmlspecialchars($this->current_url);
		return true;
	}



	//
	//	Help methods
	//

	/**
	 * Returns all cookies fetched from heders
	 *
	 * @return array
	 */
	public function  getCookies()
	{
		return $this->cookies;
	}

	/**
	 * Set a cookie to be passed on next fetch
	 *
	 * @param string $cookie
	 * @param string $value
	 * @param string $domain
	 */
	public function  setCookie($cookie, $value, $domain = '')
	{
		if ($domain == '') {
			$domain = $this->host;
		}
		$this->cookies[$domain] = $value;
	}

	/**
	 * Set the referer
	 *
	 * @param string $referer
	 * @return boolean
	 */
	public function setReferer($referer)
	{
		$parts = parse_url($referer);
		if ($parts) {
			if (isset($parts['scheme']) && isset($parts['host'])) {
				$this->referer = $referer;
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns headers
	 *
	 * @return string
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * Returns body
	 *
	 * @return string
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * Returns server status
	 *
	 * @return int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	public function getContentType()
	{
		return $this->c_type;
	}

	public function getCharset()
	{
		return $this->charset;
	}

}


// Ripped from http://www.tellinya.com/read/2007/08/28/83.html
if (!function_exists('gzdecode')) {

	function gzdecode($data)
	{
		$flags = ord(substr($data, 3, 1));
		$headerlen = 10;
		$extralen = 0;
		if ($flags & 4) {
			$extralen = unpack('v', substr($data, 10, 2));
			$extralen = $extralen[1];
			$headerlen += 2 + $extralen;
		}
		if ($flags & 8) // Filename
			$headerlen = strpos($data, chr(0), $headerlen) + 1;
		if ($flags & 16) // Comment
			$headerlen = strpos($data, chr(0), $headerlen) + 1;
		if ($flags & 2) // CRC at end of file
			$headerlen += 2;
		$unpacked = gzinflate(substr($data, $headerlen));
		if ($unpacked === FALSE)
			$unpacked = $data;
		return $unpacked;
	}
}


?>