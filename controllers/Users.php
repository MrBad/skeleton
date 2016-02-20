<?php
namespace Controllers;

use Classes\Auth;
use Classes\Config;
use Classes\Controller;
use Classes\Lang;
use Classes\Mysql;
use Classes\Utils;
use Models\User;

/**
 * Class Users
 * @package Controllers
 */
class Users extends Controller
{

	/** @var  \Models\User */
	public $model;

	/**
	 * List users
	 */
	public function admin_index()
	{
		global $cfg;
		$users = $this->model->getAll($this->page, $cfg->get('items_per_page'));
		$this->view->assign_by_ref('users', $users);
		$this->view->assign('pages', $this->model->getPages());
	}

	/**
	 * Edit a user
	 */
	public function admin_edit()
	{
		$this->model->recursive = false;
		if (!empty($this->data)) {
			if (!$this->model->Save($this->data)) {
				$this->view->riseError('Cannot save');
			} else {
				Utils::Redirect('/admin/users/');
			}
		} else {
			$user = $this->model->getById($this->params->id);
			if ($user) {
				//$user = array_shift($user);
				$this->view->assign_by_ref('user', $user);
			}
		}
	}

	/**
	 * Delete a User
	 */
	public function admin_delete()
	{
		$this->model->Delete($this->params->id);
		Utils::Redirect('/admin/users/');
	}


	/**
	 * View a User
	 */
	public function admin_view()
	{
		if (!empty($this->params->id)) {
			$user = $this->model->getById($this->params->id);
			$this->view->assign('user', $user);
		}
	}

	/**
	 * Add a user
	 */
	public function register()
	{
		$auth = Auth::getInstance();
		$cfg = Config::getInstance();

		if ($auth->isAuth()) {
			Utils::Redirect('/');
		}
		if (!empty($this->data)) {
			if (!$this->model->Save($this->data)) {
				$this->view->riseError('Cannot save data');
			} else {
				// mail confirmation //
				$mail = new \PHPMailer();
				$mail->Mailer = 'sendmail';
				require_once(ROOT . '/include/smtp.php');

				$mail->Encoding = 'quoted-printable';
				$mail->CharSet = 'utf-8';
//				$mail->SMTPDebug = 3;

				// define them in config.ini and setup include/smtp.php //
				$mail->Hostname = $cfg->get('hostname');
				$mail->Sender = $cfg->get('admin_email');
				$mail->From = $cfg->get('admin_email');
				$mail->FromName = $cfg->get('admin_from');
				$mail->AddAddress($this->data['email'], $this->data['username']);
				$mail->AddReplyTo($cfg->get('admin_email'), $cfg->get('admin_from'));

				$mail->Subject = 'Confirm your email address';
				$this->view->assign('message',
					[
						'to_name' => $this->data['first_name'] . ' ' . $this->data['last_name'],
						'to_email' => $this->data['email'],
						'token' => $this->data['token'],
					]
				);
				$mail->Body = $this->view->fetch('mails/register_ok.tpl'); // you can use db:/
				$mail->AltBody = Utils::mail_strip_html($mail->Body);

				if ($mail->Send()) {
					Utils::Redirect('/users/registerOk/');
				} else {
					$this->view->riseError("Cannot send mail");
				}
			}
		}
	}

	public function registerOK()
	{
	}

	public function activate()
	{
		$cfg = Config::getInstance();

		if (isset($this->params->id)) {
			$user = $this->model->getByToken($this->params->id);
			if (!$user) {
				Utils::Redirect($this->lang_prefix . '/users/register/');
			}
			/** @var \models\User $user */
			$user = array_shift($user);

			$ret = $this->model->activate($this->params->id);
			if ($ret) {
				// autoLogin once //
				$data = [
					'username' => $user->username,
					'password' => $user->password,
				];

				if ($this->model->login($data)) {
					echo "xxx";
					//$this->view->assign('reg_ok', true);
					//Utils::Redirect('/profiles/myprofile/1/reg/ok/');
				}
			}

			$this->view->assign_by_ref('activated', $ret);
			$this->view->assign('hostname', $cfg->get('hostname'));
			$this->view->assign_by_ref('user', $user);
		}
	}

	public function login()
	{
		$auth = Auth::getInstance();
		$langClass = Lang::getInstance();
//		$cfg = Config::getInstance();
//		$sql = Mysql::getInstance();
		$user = new User();
		if ($auth->isAuth()) {
			Utils::Redirect('/');
		}

		if (!empty($this->data)) {
			if (!$user->userExists($this->data['username'])) {
				$this->view->riseError($langClass->get('invalid_login'));
			} elseif ($user->isSuspended($this->data['username'])) {
				$this->view->riseError($langClass->get('account_suspended'));
			} elseif (!$user->isActive($this->data['username'])) {
				$this->view->riseError($langClass->get('account_not_active'));
			} elseif (!$user->login($this->data)) {
				echo "xxx";
				$this->view->riseError($langClass->get('invalid_login'));
			} else {
				// login OK //
				if (isset($this->data['autoLogin'])) { // e bifa de autologin?
					$user->updateToken();
//					setcookie('autoLogin', '1', time() + 3600 * 24 * 300, '/', $cfg->get('hostname'));
				} else {
//					setcookie('autoLogin', '0', time() + 3600 * 24 * 300, '/', $cfg->get('hostname'));
				}

//				$query = "UPDATE users SET failed_attempts=0 WHERE id=" . $auth->getUserId();
//				$sql->Update($query);

				Utils::Redirect($this->lang_prefix . '/');
			}
		}
	}

	public function logout()
	{
		$cfg = Config::getInstance();
		$this->model->logout();
		setcookie('rndStr', '', time() - 10, '/', $cfg->get('hostname'));
		Utils::Redirect($this->lang_prefix . '/');
	}

	public function passwdLost()
	{

	}

}
