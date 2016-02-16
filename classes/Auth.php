<?php
namespace Classes;

class Auth extends Singleton
{
	protected static $instance;
	private $user_id;
	private $company_id;
	private $account_type;
	private $is_auth;
	private $username;
	private $first_name;
	private $last_name;
	private $email;
	private $gid;
	private $is_premium;
	private $last_visit;
	private $passwd;

	public function __construct()
	{

		$sql=Mysql::getInstance();

		$this->user_id = 0;
		$this->is_auth = false;

		if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0) {
			$this->user_id = $_SESSION['user_id'];
			$this->is_auth = true;
		}
		if(isset($_SESSION['company_id'])) {
			$this->company_id = (int) $_SESSION['company_id'];
		}
		if(isset($_SESSION['is_premium'])) {
			$this->is_premium = (int) $_SESSION['is_premium'];
		}
		if(isset($_SESSION['account_type'])) {
			$this->account_type = $_SESSION['account_type'];
		}
		if (isset($_SESSION['username'])) {
			$this->username = $_SESSION['username'];
		}
		if (isset($_SESSION['first_name'])) {
			$this->first_name = $_SESSION['first_name'];
		}
		if (isset($_SESSION['first_name'])) {
			$this->last_name = $_SESSION['last_name'];
		}
		if (isset($_SESSION['email'])) {
			$this->email = $_SESSION['email'];
		}
		if (isset($_SESSION['gid'])) {
			$this->gid = $_SESSION['gid'];
		}
		if ($this->isAuth()) {

			$query = "UPDATE users
					SET last_ts=unix_timestamp(), is_online='1' 
					WHERE id=" . $this->user_id;

			$sql->Update($query);

			$query = "SELECT count(1) FROM users WHERE id=" . $this->user_id . " AND `status`='suspended'";
			if ($sql->QueryItem($query) > 0) {
				$this->clearAuth();
			}
		}
		Auth::$instance = $this;
	}

	/**
	 * Check if user is logged in ...
	 *
	 * @return boolean
	 */
	public function isAuth()
	{
		return ((int)$this->user_id > 0) && $this->is_auth;
	}

	/**
	 * Clear session - on log out
	 *
	 */
	public function clearAuth()
	{
		$this->user_id = 0;
		$this->company_id = 0;
		$this->is_premium = 0;
		$this->account_type = '';
		$this->is_auth = false;
		$this->first_name = $this->last_name = '';
		$this->username = '';
		unset($_SESSION['user_id']);
		unset($_SESSION['company_id']);
		unset($_SESSION['is_premium']);
		unset($_SESSION['account_type']);
		unset($_SESSION['username']);
		unset($_SESSION['first_name']);
		unset($_SESSION['last_name']);
		unset($_SESSION['email']);
		unset($_SESSION['gid']);
	}

//	public static function getInstance()
//	{
//		if (!Auth::$instance) {
//			Auth::$instance = new self;
//		}
//		return Auth::$instance;
//	}

	public function __destruct()
	{
//		$this->saveToSession();
	}

	/**
	 * Save values for a logged in user
	 *
	 * @param int $user_id
	 * @param int $company_id
	 * @param int $account_type
	 * @param string $username
	 * @param string $first_name
	 * @param string $last_name
	 * @param string $email
	 * @param string $gid
	 * @param int $is_premium
	 */
	public function setAuth($user_id, $company_id, $account_type, $username, $first_name, $last_name, $email, $gid, $is_premium=0)
	{
		if (is_numeric($user_id) && (int)$user_id > 0) {
			$this->user_id = (int) $user_id;
			$this->company_id = (int) $company_id;
			$this->account_type = $account_type;
			$this->username = $username;
			$this->is_auth = true;
			$this->first_name = $first_name;
			$this->last_name = $last_name;
			$this->email = $email;
			$this->gid = $gid;
			$this->is_premium = $is_premium;
			$this->saveToSession();
		}
	}
	public function setIsPremium()
	{
		$this->is_premium = 1;
		$this->saveToSession();
	}
	public function resetPremium()
	{
		$this->is_premium = 0;
		$this->saveToSession();
	}

	private function saveToSession()
	{

		$_SESSION['user_id'] = $this->user_id;
		$_SESSION['company_id'] = $this->company_id;
		$_SESSION['is_premium'] = $this->is_premium;
		$_SESSION['account_type'] = $this->account_type;
		$_SESSION['username'] = $this->username;
		$_SESSION['first_name'] = $this->first_name;
		$_SESSION['last_name'] = $this->last_name;
		$_SESSION['email'] = $this->email;
		$_SESSION['gid'] = $this->gid;
	}

	/**
	 * Return user id
	 *
	 * @return int
	 */
	public function getUserId()
	{
		return $this->user_id;
	}

	/**
	 * Return real name of the user
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->first_name . ' ' . $this->last_name;
	}

	/**
	 * Return username / nickname
	 *
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	public function getCompanyId() {
		return $this->company_id;
	}
	public function isPremium() {
		return $this->is_premium > 0 ? true : false;
	}
	public function getAccountType() {
		return $this->account_type;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getUserGid()
	{
		return $this->gid;
	}

	public function getLastVisit()
	{
		return time();
	}

	/**
	 * Describer
	 *
	 * @return mixed
	 */
	public function describe()
	{
		return array(
			'user_id' => $this->user_id,
			'company_id' => $this->company_id,
			'is_premium' => $this->is_premium,
			'username' => $this->username,
			'is_auth' => $this->is_auth == true ? 'true' : 'false',
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'email' => $this->email,
		);
	}

}
