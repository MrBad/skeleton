<?php

	use Classes\Utils;

	if(! defined('ROOT')) {
		define('ROOT', realpath(dirname(__FILE__).'/../../') . '/');
	}
	require_once(ROOT . 'include/conf.php');

	error_reporting(E_ALL);
	ini_set('display_errors', 'On');
	set_time_limit(0);

	$user = "someuser@somedomain";
	$pass = "somepass";

	$bad_emails = array();
	
	function getComplaints($mbox) {
		global $bad_emails, $sql;
		$uids = imap_search($mbox, "UNSEEN FROM \"complaints@eu-west-1.email-abuse.amazonses.com\"", SE_UID);

		if(!$uids) {
		    return;
		}
		
		foreach ($uids as $uid) {

			$body = imap_body($mbox, $uid, FT_UID);

			if(preg_match("'Original-Rcpt-To:\\s*([^;\n$]+)'si", $body, $match)) {
//				echo $match[1]."\n";
				$bad_emails[] = ($match[1]);
				$email = trim($match[1]);
				$query = "INSERT INTO netflash_nwl.nw_nospam SET email='".addslashes($email)."', is_bounce='0'";
				if($sql->Insert($query)) {
					echo $email . "\n";
					imap_setflag_full($mbox, $uid, "\\Seen", ST_UID);
				}
			};
			die;
		}
	}

	
	$mbox = imap_open ("{netflash.ro:993/imap/ssl/novalidate-cert}INBOX", $user, $pass)
	     or die("can't connect: " . imap_last_error());

	getComplaints($mbox);
	imap_close($mbox);
