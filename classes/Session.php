<?php
namespace Classes;
class Session {
	
	public $sessLifeTime = 0;
	public function __construct(){
		session_set_save_handler(
				array(&$this, 'open'), 
				array(&$this, 'close'), 
				array(&$this, 'read'),
				array(&$this, 'write'),
				array(&$this, 'destroy'),
				array(&$this, 'gc')
		);
		register_shutdown_function('session_write_close');
		$this->sessLifeTime = get_cfg_var("session.gc_maxlifetime");
		if (get_cfg_var("session.gc_probability") == 0) {
			ini_set('session.gc_probability', '1'); // altfel nu o sa fie curatata niciodata sesiunea	
		}
	}
	public function __destruct(){
//		echo "Session::__destruct();<hr>";
//		$this->gc($this->sessLifeTime);
	}
	public function open($save_path, $session_name) {
		return true;
	}
	
	public function close() {
		return true;
	}
	
	public function read($session_id) {

		$sql = Mysql::getInstance();
		
		$query="SELECT data FROM sessions WHERE session_id='".addslashes($session_id)."' AND expire > " . (time());
		$data = $sql->QueryItem($query);
		if ($data) {
			return $data;
		} else {
			 return "";
		}
	}
	
	public function write($session_id, $sess_data) {

		$sql = Mysql::getInstance();
		
		$query="SELECT count(1) FROM sessions WHERE session_id='".addslashes($session_id)."'";
		
		$user_id = 0;
		if ( preg_match("'user_id\|i:([0-9]+);'", $sess_data, $match)) {
			$user_id = $match[1];
		}

		if ($sql->QueryItem($query)) {
			$query="UPDATE sessions SET 
					ip='".$_SERVER['REMOTE_ADDR']."',		
					data='".addslashes($sess_data)."', 
					user_id='".$user_id."',
					ts=unix_timestamp(), 
					expire=" . (time()+$this->sessLifeTime) . " 
					WHERE session_id='" . addslashes($session_id)."'";
		} else {
			$query="INSERT INTO sessions SET 
					ip='".(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '' )."',
					session_id='".addslashes($session_id)."', 
					data='".addslashes($sess_data)."', 
					user_id='".$user_id."', 
					ts=unix_timestamp(), 
					expire=" . (time()+$this->sessLifeTime);
		}
//		echo $query . "<hr>";
		$sql->Insert($query);
		return true;
	}
	
	public function destroy($session_id) {
		$sql = Mysql::getInstance();
		$query="DELETE FROM sessions WHERE session_id = '".$session_id."'";
		$sql->Delete($query);
		return true;
	}
	
	public function gc($maxlifetime) {
		$sql = Mysql::getInstance();
		$query="DELETE FROM sessions WHERE expire < '".(time() - $maxlifetime)."'";
		$sql->Delete($query);
	}
}

$session = new Session();

?>