<?php
namespace Models;

use Classes\Auth;
use Classes\Model;
use Classes\Mysql;
use Classes\Utils;

/**
 * Class User
 * @package Models
 * @method User[] getByToken(string $token)
 * @method User[] getByEmail(string $email)
 */
class User extends Model
{

	/** @var int $id */
	public $id;

	/** @var string $username */
	public $username;

	/** @var string $email */
	public $email;

	/** @var string $password */
	public $password;

	/** @var string $first_name */
	public $first_name;

	/** @var string $last_name */
	public $last_name;

	/** @var  string $status */
	public $status;

	/** @var string $token */
	public $token;

	/** @var  \DateTime $created_at */
	public $created_at;

	/** @var  \DateTime $last_login_at */
	public $last_login_at;

	protected $hasMany = [];
	protected $hasAndBelongsToMany = [];
	protected $hasOne = [];
	protected $belongsTo = [];

	public $validate = [

		'username' => [
			[
				'rule' => VALID_NOT_EMPTY,
				'message' => 'Username cannot be left blank'
			], [
				'rule' => ['minLength', 6],
				'message' => 'Username has less than 6 characters'
			], [
				'rule' => ['maxLength', 64],
				'message' => 'Username has more than 64 characters'
			], [
				'rule' => VALID_USERNAME,
				'message' => 'Username is invalid - start with a letter, digits and max one _ or . inside'
			], [
				'rule' => ['isUnique'],
				'message' => 'Username is already taken'
			],
			'required' => true,
			'groups' => ['default', 'a_group'],
		],

		'email' => [
			[
				'rule' => VALID_NOT_EMPTY,
				'message' => 'Email cannot be left blank'
			], [
				'rule' => VALID_EMAIL,
				'message' => 'Email is not valid'
			], [
				'rule' => ['isUnique'],
				'message' => 'Email is already taken'
			],
			'required' => true,
			'groups' => ['default', 'a_group'],
		],

		'password' => [
			[
				'rule' => VALID_NOT_EMPTY,
				'message' => 'Password cannot be left blank'
			],
			'required' => true,
			'groups' => ['default', 'a_group'],
		],
		're_password' => [
			[
				'rule' => ['passwordMatch', 'password'],
				'message' => 'Passwords does not match'
			],
			'required' => true,
			'groups' => ['default', 'a_group'],
		],

		'first_name' => [
			[
				'rule' => VALID_NOT_EMPTY,
				'message' => 'First name cannot be left blank'
			],
			'required' => true,
			'groups' => ['default', 'a_group'],
		],

		'last_name' => [
			[
				'rule' => VALID_NOT_EMPTY,
				'message' => 'Last name cannot be left blank'
			],
			'required' => true,
			'groups' => ['default', 'a_group'],
		],
	];

	public $passwd_lost_rules = [
		'email' => [
			[
				'rule' => VALID_NOT_EMPTY,
				'message' => 'Email cannot be left blank'
			], [
				'rule' => VALID_EMAIL,
				'message' => 'Email is not valid'
			], [
				'rule' => ['emailExists'],
				'message' => 'No account defined by this email address'
			],
			'required' => true,
		]
	];
	public $reset_passwd_rules = [
		'password' => [
			[
				'rule' => VALID_NOT_EMPTY,
				'message' => 'Password cannot be left blank'
			],
			'required' => true,
		],
		're_password' => [
			[
				'rule' => ['passwordMatch', 'password'],
				'message' => 'Passwords does not match'
			],
			'required' => true,
		],
	];

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Password match validate rule
	 * @param string $fieldname
	 * @param string $repasswd
	 * @param string $passwd
	 * @return bool
	 */
	public function passwordMatch($fieldname, $repasswd, $passwd)
	{
		if ($repasswd === $passwd) {
			return true;
		}
		return false;
	}

	/**
	 * Called before saving this model data
	 * @param array $data
	 * @return array
	 */
	public function preSave($data)
	{
		if (!isset($data['id'])) { // on INSERT
			$data['status'] = 'pending';
			$data['created_at'] = date('Y-m-d H:i:j');
			$data['token'] = Utils::randomString(64);
			$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => 12]);
		}
		return $data;
	}

	/**
	 * Activate the user having this token
	 * @param string $token
	 * @return bool
	 */
	public function activate($token)
	{
		$sql = Mysql::getInstance();
		$query = "SELECT count(1) FROM users WHERE token='" . $sql->escape($token) . "'";
		$ret = $sql->QueryItem($query);
		if ($ret > 0) {
			$query = "UPDATE users SET status='active' WHERE token='" . $sql->escape($token) . "'";
			$sql->Update($query);
			return $sql->a_rows > 0;
		}
		return false;
	}

	/**
	 * Updating last action on site for this user_id
	 * @param int $user_id
	 * @param int $is_online
	 * @return bool
	 */
	private function updateLastAction($user_id, $is_online = 0)
	{
		$sql = Mysql::getInstance();
		$query = "UPDATE users
					SET is_online='" . (int)$is_online . "',
					last_login_at=now(),
                    last_ip='" . $sql->escape($_SERVER['REMOTE_ADDR']) . "'
                    WHERE id=" . (int)$user_id;

		return $sql->Update($query);
	}

	/**
	 * Login the user
	 * @param array $data - ['username'=>$username, 'password'=>$password]
	 * @return bool
	 */
	public function login($data)
	{
		$sql = Mysql::getInstance();
		$auth = Auth::getInstance();

		$query = "SELECT u.* FROM users u
					WHERE `status` IN ('active', 'confirmed') ";
		if (preg_match(VALID_EMAIL, $data['username'])) {
			$query .= "AND email='" . $sql->escape($data['username']) . "'";
		} else {
			$query .= "AND username='" . $sql->escape($data['username']) . "'";
		}

		/** @var User $user */
		$user = $sql->QueryRow($query);

		if ($user) {
			if (password_verify($data['password'], $user->password)) {
				$auth->setAuth($user->id, $user->username, $user->first_name, $user->last_name, $user->email);
				$this->updateLastAction($user->id, 1);
				return true;
			}
		}
		return false;
	}

	/**
	 * Logs out current user
	 */
	public function logout()
	{
		$sql = Mysql::getInstance();
		$auth = Auth::getInstance();
		if ($auth->isAuth()) {
			$query = "UPDATE users SET is_online='0' WHERE id=" . $auth->getUserId();
			$sql->Update($query);
			$this->updateLastAction($auth->getUserId());
			$auth->clearAuth();
			session_unset();
			session_destroy();
		}
	}

	/**
	 * Username exists?
	 * @param string $user
	 * @return bool
	 */
	public function userExists($user)
	{
		$sql = Mysql::getInstance();
		if (preg_match(VALID_EMAIL, $user)) {
			$query = "SELECT count(1) FROM users
                                WHERE email='" . addslashes($user) . "'";
		} else {
			$query = "SELECT count(1) FROM users
                                WHERE username='" . addslashes($user) . "'";
		}
		return $sql->QueryItem($query) > 0 ? true : false;
	}

	public function emailExists($fieldname, $email)
	{
		$sql = Mysql::getInstance();
		$query = "SELECT count(1) FROM users WHERE email='" . $sql->escape($email) . "'";
		return $sql->QueryItem($query) > 0 ? true : false;
	}

	/**
	 * Username is suspended?
	 * @param string $user
	 * @return bool
	 */
	public function isSuspended($user)
	{
		$sql = Mysql::getInstance();
		if (preg_match(VALID_EMAIL, $user)) {
			$query = "SELECT count(1) FROM users
                                WHERE email='" . addslashes($user) . "'
                                AND status='suspended'";
		} else {
			$query = "SELECT count(1) FROM users
                                WHERE username='" . addslashes($user) . "'
                                AND status='suspended'";
		}

		return $sql->QueryItem($query) > 0 ? true : false;
	}

	/**
	 * Username is active?
	 * @param string $user
	 * @return bool
	 */
	public function isActive($user)
	{
		$sql = Mysql::getInstance();
		if (preg_match(VALID_EMAIL, $user)) {
			$query = "SELECT count(1) FROM users
					WHERE email='" . addslashes($user) . "'
					AND status='active'";
		} else {
			$query = "SELECT count(1) FROM users
					WHERE username='" . addslashes($user) . "'
					AND status='active'";
		}
		return $sql->QueryItem($query) > 0 ? true : false;
	}

	/**
	 * Update current token
	 */
	public function updateToken()
	{
	}

	public function changePassword($password)
	{
		$sql = Mysql::getInstance();
		if (!$this->id) {
			return false;
		}
		$query = "UPDATE users SET password='" . $sql->escape(password_hash($password, PASSWORD_DEFAULT, ['cost' => 12])) . "' WHERE id=" . $this->id;
		if($sql->Update($query)) {
			$this->updateToken();
			return true;
		}
		return false;
	}
}

