<?php
namespace Classes;


//
//	Mysql class, ver 0.3
//	- 05 feb 2010 - fixed logging :P	
//	- 03 nov 2008 - fixed bloody short tags :P	
//	- 11 ocr 2008 - fixed multiple servers connections link identifier bug
//
define('FETCH_ARRAY', 0);
define('FETCH_ASSOC', 1);
define('FETCH_OBJ', 2);
define('FETCH_DEFAULT', FETCH_OBJ);


class Mysql extends Singleton
{
	protected static $instance;
	/** @var \mysqli|null */
	public $id = null;

	/** @var string file where to log errors */
	public $error_log_file = '';

	/** @var int */
	public $rows = 0;

	/** @var \stdClass|array|null */
	public $data = null;

	/** @var int */
	public $a_rows = 0;

	private $host = '';
	private $user = '';
	private $passwd = '';
	private $db = '';

	private $fetch_type = FETCH_DEFAULT;
	private $result = null;
	private $queries = array();
	private $error = '';
	private $query = '';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$cfg = Config::getInstance();
		$this->error_log_file = ROOT . 'tmp/logs/mysqlErr.log';
		$host = $cfg->get('sql_host');
		$user = $cfg->get('sql_user');
		$passwd = $cfg->get('sql_pass');
		$db = $cfg->get('sql_db');
		$persistent = $cfg->get('sql_persistent') == 0 ? true : false;

		$this->host = $host;
		$this->user = $user;
		$this->passwd = $passwd;

		$this->fetch_type = FETCH_DEFAULT;

		if ($persistent === true) {
			$this->id = mysqli_connect($host, $user, $passwd);
		} else {
			$this->id = mysqli_connect($host, $user, $passwd);
		}

		if (!$this->id) {
			$this->error = 'Unable to connect to mysql server: ' . $this->host;
			$this->logError($this->error);
			return false;
		}

		$this->Query('SET NAMES "utf8"');
		$this->db = $db;
		$this->SelectDB($this->db);

//		Mysql::$instance = $this;

		return true;
	}

	/**
	 * @param string $msg
	 */
	public function logError($msg)
	{
		if (!empty($this->error_log_file)) {
			$fp = fopen($this->error_log_file, 'a+');
			if ($fp) {
				$msg = trim($msg) . "\n";
				fwrite($fp, date('Y-m-d, H:i:s', time()) . ' ' . $msg);
				fwrite($fp, "\t\t" . serialize(debug_backtrace(false)) . "\n");
				fclose($fp);
			}
		}
	}

	/**
	 * Query
	 *
	 * @param string $query
	 * @return bool
	 */
	public function Query($query)
	{
		if ($this->_query($query) !== false) {
			$this->rows = @mysqli_num_rows($this->result);
		}
		return $this->result;
	}

	/**
	 * Realizeaza query-ul - privata
	 *
	 * @param string $query
	 * @return bool
	 */
	private function _query($query)
	{

		if (!preg_match('/^EXPLAIN/si', $query)) {
			array_push($this->queries, $query);
		}
//		file_put_contents("tmp/logs/queries.txt", $query."\n", FILE_APPEND);
//		if($_SERVER['REMOTE_ADDR'] == '46.19.37.50') echo $query."<hr>";

		//$tstart=Utils::getMicroTime();
		$this->query = $query;

		//echo round(Utils::getMicroTime() - $tstart, 3) . ' ' . $query . '<hr>';
		$this->result = mysqli_query($this->id, $query);
		if ($this->result === false) {
			$this->error = 'Unable to perform query: ' . $query . '. ' . mysqli_error($this->id);
			$this->logError($this->error);
			//Utils::pr(debug_backtrace(false));
		}

		return $this->result == false ? false : true;
	}

	/**
	 * Selecteaza database-ul
	 *
	 * @param string $db
	 * @return bool
	 */
	public function SelectDB($db)
	{
		$this->db = $db;
		$ret = mysqli_select_db($this->id, $db);
		if ($ret === false) {
			$this->error = 'Unable to select database: ' . $db . '. ' . mysqli_error($this->id);
			$this->logError($this->error);
		}
		return $ret;
	}

//	public static function getInstance()
//	{
////		if (! Mysql::$instance) {
////			Mysql::$instance = new self;
////		}
//		return Mysql::$instance;
//	}

	/**
	 * Intoarce toate rezultatele
	 *
	 * @return \stdClass[]
	 */
	public function fetchAll()
	{
		$ret = array();
		for ($i = 0; $i < $this->rows; $i++) {
			$this->GetRow($i);
			$ret[] = $this->data;
		}
		return $ret;
	}

	/**
	 * Fetchuieste un rand
	 *
	 * @param int $row
	 * @return bool
	 */
	public function GetRow($row)
	{
		$ret = mysqli_data_seek($this->result, $row);

		if ($ret === false) {
			$this->error = 'Unable to seek data row: ' . $row . '. ' . mysqli_error($this->id);
			$this->logError($this->error . ', ' . $this->query);
			return false;
		}

		if ($this->fetch_type == FETCH_ASSOC) {
			$this->data = mysqli_fetch_assoc($this->result);
		} elseif ($this->fetch_type == FETCH_OBJ) {
			$this->data = mysqli_fetch_object($this->result);
		} else {
			$this->data = mysqli_fetch_array($this->result);
		}
		if ($this->data === false) {
			$this->error = 'Unable to fetch row: ' . $row . '. ' . mysqli_error($this->id);
			$this->logError($this->error);
			return false;
		}
		return true;
	}

	/**
	 * @param $query
	 * @return false|\stdClass
	 */
	public function QueryObject($query)
	{
		$fetch_tmp = $this->fetch_type;
		$this->fetch_type = FETCH_OBJ;
		$ret = $this->QueryRow($query);
		$this->fetch_type = $fetch_tmp;
		return $ret;
	}

	/**
	 * Interogheaza un rand
	 *
	 * @param string $query
	 * @return \stdClass | false
	 */
	public function QueryRow($query)
	{
		if ($this->_query($query)) {
			$this->rows = mysqli_num_rows($this->result);
			if ($this->rows > 0) {
				if ($this->GetRow(0)) {
					$ret = $this->data;
					return $ret;
				}
			}
		}
		return false;
	}

	/**
	 * Interogheaza un item
	 *
	 * @param string $query
	 * @return string | false
	 */
	public function QueryItem($query)
	{
		$fetch_type = $this->fetch_type;
		$this->fetch_type = FETCH_ASSOC;
		$this->data = $this->QueryRow($query, false);
		if ($this->data !== false) {
			if (is_array($this->data)) {
				$this->data = array_shift($this->data);
				$this->fetch_type = $fetch_type;
				return $this->data;
			}
		}
		$this->fetch_type = $fetch_type;
		return false;
	}

	/**
	 * Executa un delete
	 *
	 * @param string $query
	 * @return boolean
	 */
	public function Delete($query)
	{
		$this->result = $this->_query($query);
		if ($this->result) {
			$this->a_rows = mysqli_affected_rows($this->id);
		}
		return $this->result; // true or false
	}

	/**
	 * Update function
	 *
	 * @param string $query
	 * @return boolean
	 */
	public function Update($query)
	{
		$this->result = $this->_query($query);
		if ($this->result) {
			$this->a_rows = mysqli_affected_rows($this->id);
		}
		return $this->result; // true or false
	}

	/**
	 * Insereaza un rand
	 *
	 * @param string $query
	 * @return boolean
	 */
	public function Insert($query)
	{
		$this->result = $this->_query($query);
		if ($this->result) {
			$this->a_rows = mysqli_affected_rows($this->id);
		}
		return $this->result; // true or false
	}

	/**
	 * Replace a row
	 *
	 * @param string $query
	 * @return boolean
	 */
	public function Replace($query)
	{
		$this->result = $this->_query($query);
		if ($this->result) {
			$this->a_rows = mysqli_affected_rows($this->id);
		}
		return $this->result; // true or false
	}

	/**
	 * Intoarce id-ul ultimului insert ce a generat un autoincrement
	 *
	 * @return int
	 */
	public function InsertID()
	{
		$this->result = mysqli_insert_id($this->id);
		if ($this->result == 0) {
			$this->error = 'Last query does not generate an autoincrement value. ' . mysqli_error($this->id);
			$this->logError($this->error);
		}
		return ($this->result == 0 ? false : $this->result);
	}

	public function getQueries()
	{
		$ret = array();
		$fetch_type = $this->fetch_type;
		$this->fetch_type = FETCH_ASSOC;
//		foreach ($this->queries as $query) {
//			$dQuery = "EXPLAIN " . $query;
//			$res = $this->QueryRow($dQuery);
//			$ret[] = array(
//				'query' => $query,
//				'explain' => $res,
//			);
//		}
		$ret = $this->queries;
		$this->fetch_type = $fetch_type;
		return $ret;
	}

	public function free_result()
	{
//		if ($this->result) {
		mysqli_free_result($this->result);
//		}
	}

	/**
	 * Returns last mysql error
	 * @return string
	 */
	public function LastError()
	{
		return $this->error;
	}

	/**
	 * Seteaza tipul de fetch
	 *
	 * @param int $type
	 */
	public function SetFetchType($type)
	{
		$this->fetch_type = $type;
	}

	/**
	 * Destructorul
	 */
	public function __destruct()
	{
		$this->Close();
	}

	/**
	 * Wrapper pt CloseDB
	 *
	 * @return boolean
	 */
	public function Close()
	{
		return $this->CloseDB();
	}

	/**
	 * Inchide database-ul
	 *
	 * @return boolean
	 */
	public function CloseDB()
	{
		$ret = false;
		if ($this->id !== false) {
			$ret = @mysqli_close($this->id);
			if (!$ret) {
				$this->error = 'Unable to close connection on server: ' . $this->host . '. ' . @mysqli_error($this->id);
				$this->logError($this->error);
			}
		}
		return $ret;
	}

	/**
	 * @param string $str string to escape
	 * @return string
	 */
	public function escape($str)
	{
		return mysqli_real_escape_string($this->id, $str);
	}

	/**
	 * Begin transaction
	 * @return bool
	 */
	public function Begin()
	{
		return $this->Query('Begin');
	}

	/**
	 * Commit transaction
	 * @return bool
	 */
	public function Commit()
	{
		return $this->Query('Commit');
	}

}


