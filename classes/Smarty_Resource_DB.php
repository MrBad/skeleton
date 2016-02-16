<?php
namespace Classes;
use \Smarty_Resource_Custom;

class Smarty_Resource_DB extends Smarty_Resource_Custom
{
	protected function fetch($tpl_name, &$source, &$mtime)
	{
		$sql = Mysql::getInstance();
		$query = "SELECT `source`, `last_ts` FROM mail_templates WHERE name='" . addslashes($tpl_name) . "'";
		$row = $sql->QueryObject($query);
		if ($row) {
			$source = $row->source;
			$mtime = (int) $row->last_ts;
			return true;
		} else {
			$source = null;
			$mtime = null;
		}
	}

	protected function fetchTimestamp($tpl_name)
	{
		$sql = Mysql::getInstance();
		$query = "SELECT last_ts FROM mail_templates WHERE name='" . addslashes($tpl_name) . "'";
		$mtime = (int) $sql->QueryItem($query);
		return $mtime;
	}

}