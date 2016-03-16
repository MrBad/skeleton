<?php
namespace Classes;

use Classes\Validator;

/**
 * Model class ver 0.30
 * 		 - 23 nov 2015 - added required=>false; in validator
 *        - 23 oct 2015 - added lazy loading by __get magic function; on production recursive should be false
 *                    - added public $hasAndBelongsToMany = array(
 *                            'users'=>[
 * 'reversedByTable' => 'users_locations'
 * ]
 * );
 *        - 27 sep 2015 - fixed NULL values on Update.
 *      - 25 sep 2015 - fixed hasAndBelongsToMany id, added pushErrors, validate can validate model without $data, Save() can save this model without $data as param
 *        - 09 jun 2011 - added fetch_rel_sup for hasAndBelongsToMany when rel_table has extra fields
 *        - 08 jun 2011 - modified hasAndBelongsToMany bug
 *    - 23 may 2010 - added pagingType support
 *        - 3     nov 2008 - now supports truly belongsTo and hasOne with dependency and fields
 *        - 13 oct 2008 - added postSave method
 *        - 17 sept 2008 - added preSave method
 *
 * TODO - solve id-parent id relations
 *
 */
class Model
{

	/**
	 * @var array
	 */
	static private $cached_fields = [];
	/**
	 * @var array
	 */
	public $fields = array();
	/**
	 * @var int
	 */
	public $pk = 0;
	/**
	 * @var int
	 */
	public $id = 0;
	/**
	 * @var bool
	 */
	public $recursive = false;
	/**
	 * @var string
	 */
	public $pages = '';
	/**
	 * @var int
	 */
	public $items = 0;
	/**
	 * @var array
	 */
	public $validate = array();
	/**
	 * @var array
	 */
	public $validates_errors = array();
	/**
	 * @var int
	 */
	public $pagingType = 1;
	/**
	 * @var string
	 */
	public $view_name;

	/**
	 * @var string
	 */
	protected $table_name;
	/**
	 * @var array
	 */
	protected $hasMany = array();
	/**
	 * @var array
	 */
	protected $hasAndBelongsToMany = array();
	/**
	 * @var array
	 */
	protected $hasOne = array();
	/**
	 * @var array
	 */
	protected $belongsTo = array();


	/**
	 * Constructor
	 */
	public function __construct()
	{
//		$this->validator = new Validator();
		$childClass = get_class($this); // Who called me //
		$table_name = Inflector::tableize(preg_replace('/^([^\\\\]+\\\\)([a-z0-9\-\_]+)$/i', '\2', $childClass));

		if (empty($this->fields)) {
			$this->setTableName($table_name);
		}
//		if(empty($this->fields) && $table_name != 'homepages' && $table_name != 'errors') {
//			trigger_error('no table selected in ' . $childClass . "\n Use <div>\$this->setTableName('$table_name');</div>", E_USER_ERROR);
//		}
	}

	/**
	 * Seteaza numele tabelei
	 *
	 * @param string $tableName
	 * @return bool false if table does not exists
	 */
	protected function setTableName($tableName)
	{
		$this->table_name = $tableName;
		$ret = $this->describe_db();
		return $ret;
	}

	/**
	 * Extrage si seteaza $this->fields din DB / cache
	 *
	 * @return bool
	 */
	private function describe_db()
	{
		$cfg = Config::getInstance();
		$sql = Mysql::getInstance();
		if ($this->table_name == 'homepages' || $this->table_name == 'errors') {
			return false;
		}
		if (empty($this->fields)) {
			if (!empty(Model::$cached_fields[$this->table_name])) {
//				echo "Hit from memory cache {$this->table_name}<hr>";
				$arr = unserialize(Model::$cached_fields[$this->table_name]);
				$this->fields = $arr['fields'];
				$this->pk = $arr['pk'];
			} else if (!is_file($cfg->get('root') . $cfg->get('tmp') . $cfg->get('cache') . $cfg->get('describes') . $this->table_name) || $cfg->get('debug')) {
				$query = "DESCRIBE " . addslashes($this->table_name);
				if ($sql->Query($query)) {
					$fields = $sql->fetchAll();
					foreach ($fields as $field) {
						if ($field->Key == 'PRI') {
							$this->pk = $field->Field;
						}
						$this->{$field->Field} = '';
						array_push($this->fields, $field->Field);
					}

					if ($this->pk != 'id') {
						trigger_error("Model::describe_db() - Primary key is not named id!", E_USER_WARNING);
						return false;
					}
					file_put_contents($cfg->get('root') . $cfg->get('tmp') . $cfg->get('cache') . $cfg->get('describes') . $this->table_name, serialize(array('fields' => $this->fields, 'pk' => $this->pk)));
				} else {
					return false;
				}
			} else {
				$arr = unserialize(file_get_contents($cfg->get('root') . $cfg->get('tmp') . $cfg->get('cache') . $cfg->get('describes') . $this->table_name));
				$this->fields = $arr['fields'];
				$this->pk = $arr['pk'];
			}
		} else {
//			Utils::pr(debug_backtrace(false));
			trigger_error('Duplicate describe_db for Model ' . Inflector::classify($this->table_name) . ', check deprecated LoadModel method', E_USER_WARNING);
		}
		Model::$cached_fields[$this->table_name] = serialize(array('fields' => $this->fields, 'pk' => $this->pk));
		return true;
	}

	/**
	 * @param array $data
	 * @return array|false
	 */
	public function preSave($data)
	{
		return $data;
	}

	/**
	 * @param array $data
	 * @return array|false
	 */
	public function postSave($data)
	{
		return $data;
	}

	/**
	 * @param array $data
	 * @return array|false
	 */
	public function preInsert($data)
	{
		return $data;
	}

	/**
	 * @param array $data
	 * @return array|false
	 */
	public function postInsert($data)
	{
		return $data;
	}

	/**
	 * @param array $data
	 * @return array|false
	 */
	public function preUpdate($data)
	{
		return $data;
	}

	/**
	 * @param array $data
	 * @return array|false
	 */
	public function postUpdate($data)
	{
		return $data;
	}

	public function __destruct()
	{
	}

	/**
	 * @param $id
	 * @return static
	 */
	public function getById($id)
	{
		$ret = $this->__call('getById', array($id));
		if ($ret) {
			return array_shift($ret);
		}

		return false;
	}

	/**
	 * Magic Functions getByPARAM($PARAM='value') - Intoarce o lista de obiecte de acest tip
	 *
	 * @param string $name MethodName
	 * @param mixed $value
	 * @return static[] of this object
	 */
	public function  __call($name, $value)
	{

		$sql = Mysql::getInstance();
		$ret = array();

		$match = array();

		if (preg_match("'^getBy(.*)$'", $name, $match)) {

			$rel_id = Inflector::underscore($match[1]);
			$id = array_pop($value);
			$query = "SELECT " . $this->composeSelectedFields() . " "
				. "FROM " . $this->composeFrom() . " "
				. "WHERE " . $this->table_name . "." . $rel_id . "='" . addslashes($id) . "'";

//			echo $query . "<hr>";

			$arr = array();
			if ($sql->Query($query)) {
				for ($i = 0; $i < $sql->rows; $i++) {
					$sql->GetRow($i);
					$arr[] = $sql->data;
				}
			}
			foreach ($arr as $i => $row) {
				$class = '\\Models\\' . Inflector::classify($this->table_name);
				$ret[$i] = new $class;
				$ret[$i]->fields = $this->fields;
				$ret[$i]->pk = $this->pk;
				$ret[$i]->table_name = $this->table_name;
				foreach ($row as $key => $value) {
					$ret[$i]->{$key} = $value;
				}
			}


			//
			//	Recurse child objects
			//
			if ($this->recursive && !empty($this->hasMany)) {
				for ($i = 0; $i < count($ret); $i++) {
					foreach ($this->hasMany as $value) {
						$value = Inflector::singularize($value);
						$value = Inflector::classify($value);
						$modelName = '\\Models\\' . $value;
						$subModel = new $modelName;
						$method_name = 'getBy' . Inflector::camelize(Inflector::singularize($this->table_name)) . Inflector::camelize($this->pk);
						$value = Inflector::pluralize($value);
						$ret[$i]->{$value} = call_user_func(array(&$subModel, $method_name), $ret[$i]->id);
					}
				}
			}

			if ($this->recursive && !empty($this->hasAndBelongsToMany)) {
				for ($i = 0; $i < count($ret); $i++) {
					foreach ($this->hasAndBelongsToMany as $key => $value) {
						$fetch_rel_sup = false;
						$rel_table = '';
						$sup_fields = array();
						if (is_array($value)) {
							$rel = $value;
							$value = $key;
							if (isset($rel['fetch_rel_sup'])) {
								$sup_fields = $rel['fetch_rel_sup'];
								$fetch_rel_sup = true;
							}
							if (isset($rel['reversedByTable'])) {
								$rel_table = $rel['reversedByTable'];
							}
						}

						$rel_table = empty($rel_table) ? $this->table_name . '_' . $value : $rel_table;

						if ($fetch_rel_sup) {
							$query = "SELECT " . Inflector::singularize($value) . "_id as rel_id," . implode(',', $sup_fields);
							$query .= " FROM $rel_table
									WHERE " . Inflector::singularize($this->table_name) . "_id=" . $ret[$i]->id;


							$rel_ids = array();
							$extra_fields = array();
							if ($sql->Query($query)) {
								for ($j = 0; $j < $sql->rows; $j++) {
									$sql->GetRow($j);
									$rel_id = $sql->data->rel_id;
									$rel_ids[] = $sql->data->rel_id;
									foreach ($sql->data as $k => $v) {
										if ($k != 'rel_id') {
											$extra_fields[$k][$rel_id] = $v;
										}
									}
								}
							}
						} else {

							$query = "SELECT " . Inflector::singularize($value) . "_id as rel_id FROM $rel_table WHERE " . Inflector::singularize($this->table_name) . "_id=" . $ret[$i]->id;

							$rel_ids = array();
							if ($sql->Query($query)) {
								for ($j = 0; $j < $sql->rows; $j++) {
									$sql->GetRow($j);
									if ($sql->data->rel_id) {
										$rel_ids[] = $sql->data->rel_id;
									}
								}
							}
						}

						$ret[$i]->{Inflector::pluralize(Inflector::classify($value))} = array();
						if (empty($rel_ids)) {
							continue;
						}
						$value = Inflector::classify($value);
						$modelName = '\\Models\\' . $value;
						$subModel = new $modelName;
						$value = Inflector::pluralize($value);
						$subModel->recursive = isset($rel['reversedByTable']) ? false : $this->recursive;
						$ret[$i]->{$value} = call_user_func(array(&$subModel, 'getAll'), 0, 0, null, "AND " . Inflector::tableize($value) . ".id IN(" . implode(', ', $rel_ids) . ")");

						if (!empty($extra_fields)) {
							foreach ($ret[$i]->{$value} as $k => $v) {
								$id = $v->id;
								foreach ($extra_fields as $field_name => $field_value) {
									$ret[$i]->{$value}[$k]->{$field_name} = $field_value[$id];
								}
							}
						}
					}
				}
			}
		} else {
//			echo $name . "<hr/>";
		}

		return $ret;
	}

	/**
	 * Compune campurile ce se vor selecta in query in functie de asocierea dintre obiecte
	 *
	 * @return string
	 */
	private function composeSelectedFields()
	{

		$str = '';

		foreach (array_merge($this->hasOne, $this->belongsTo) as $key => $value) {

			$rel_table = Inflector::tableize(is_array($value) ? $key : $value);

			if (is_array($value)) {
				if (isset($value['fields'])) {
					foreach ($value['fields'] as $item) {
						$str .= $rel_table . "." . $item . ", ";
					}
				} else {
					$str .= $rel_table . ".*, ";
				}
			} else {
				$str .= $rel_table . ".*, ";
			}
		}

		foreach ($this->fields as $field) {
			$str .= $this->table_name . "." . $field . ", ";
		}
		$str = substr($str, 0, -2);
		return $str;
	}


	/**
	 * Compose the FROM fields
	 * @return string
	 */
	private function composeFrom()
	{

		$str = ' ' . $this->table_name . ' ';

		foreach ($this->hasOne as $key => $value) {

			$rel_table = Inflector::tableize(is_array($value) ? $key : $value);
			$rel = "INNER";
			if (is_array($value)) {
				if (isset($value['dependent']) && $value['dependent'] == false) {
					$rel = "LEFT";
				}
			}
			$str .= "$rel JOIN " . $rel_table . " ON " . $this->table_name . ".id = " . $rel_table . "." . Inflector::singularize($this->table_name) . "_id ";
		}

		foreach ($this->belongsTo as $key => $value) {

			$rel_table = Inflector::tableize(is_array($value) ? $key : $value);
			$rel = "INNER";
			if (is_array($value)) {
				if (isset($value['dependent']) && $value['dependent'] == false) {
					$rel = "LEFT";
				}
			}
			$str .= "$rel JOIN " . $rel_table . " ON " . $this->table_name . "." . Inflector::singularize($rel_table) . "_id = " . $rel_table . ".id ";
		}
		return $str;

	}

	/**
	 * Intoarce o lista de obiecte de acest tip
	 *
	 * @param int $page start from page
	 * @param int $items_per_page number of items to limit query to
	 * @param string $order ORDER BY to add on query
	 * @param string $filter AND conditions to add to query
	 * @param bool $use_pagination
	 * @return static[]
	 */
	public function getAll($page = 0, $items_per_page = 0, $order = '', $filter = '', $use_pagination = true)
	{

		$sql = Mysql::getInstance();
		$ret = array();

		$query = "SELECT " . $this->composeSelectedFields() . " "
			. "FROM " . $this->composeFrom() . " "
			. "WHERE 1=1";

//		$order = $order=='' ? "ORDER BY ".$this->table_name.".id DESC" : $order;
		$order = $order == '' ? "ORDER BY " . $this->table_name . ".id" : $order;

		$query .= ' ' . $filter;
		$query .= ' ' . $order;

		if ($items_per_page > 0 && $use_pagination) {
			$paging = new Paging();
			$paging->setItemsPerPage($items_per_page);
			$paging->setQuery($query);
			$paging->setPage($page);
			$this->pages = $paging->getPagesStr($this->pagingType);
			$this->items = $paging->items;
			$query = $paging->getQuery();
		} else {
			if ($items_per_page > 0) {
				$query .= " LIMIT " . (int)$items_per_page;
			}
		}
//		echo $query . "\n";
		$arr = array();
		if ($sql->Query($query)) {
			$sql->SetFetchType(FETCH_OBJ);
			for ($i = 0; $i < $sql->rows; $i++) {
				$sql->GetRow($i);
				$arr[] = $sql->data;
			}
		}
//				$ret[$i] = new $this;

		foreach ($arr as $i => $row) {
			$ret[$i] = new $this;
			$ret[$i]->fields = $this->fields;
			$ret[$i]->pk = $this->pk;
			$ret[$i]->table_name = $this->table_name;
			foreach ($row as $key => $value) {
				$ret[$i]->{$key} = $value;
			}
		}


		//
		//	Recurse child objects
		//	
		if ($this->recursive && !empty($this->hasMany)) {
			for ($i = 0; $i < count($ret); $i++) {
				foreach ($this->hasMany as $value) {
					$value = Inflector::singularize($value);
//					require_once($cfg->get('root') . $cfg->get('models') . strtolower($value) . ".php");
					$value = Inflector::classify($value);
					$subm = '\\Models\\' . $value;
					$submodel = new $subm;

//					$submodel->setTableName(Inflector::tableize($value));

					$method_name = 'getBy' . Inflector::camelize(Inflector::singularize($this->table_name)) . Inflector::camelize($this->pk);
					$value = Inflector::pluralize($value);
//					$ret[$i]->{$value} = call_user_method($method_name, $submodel, $ret[$i]->id);
					$ret[$i]->{$value} = call_user_func(array(&$submodel, $method_name), $ret[$i]->id);
				}
			}
		}

		if ($this->recursive && !empty($this->hasAndBelongsToMany)) {

			for ($i = 0; $i < count($ret); $i++) {
				foreach ($this->hasAndBelongsToMany as $key => $value) {
					$rel_table = '';
					$fetch_rel_sup = false;
					$sup_fields = array();
					if (is_array($value)) {
						$rel = $value;
						$value = $key;
						if (isset($rel['fetch_rel_sup'])) {
							$sup_fields = $rel['fetch_rel_sup'];
							$fetch_rel_sup = true;
						}
						if (isset($rel['reversedByTable'])) {
							$rel_table = $rel['reversedByTable'];
						}
					}


					$rel_table = empty($rel_table) ? $this->table_name . '_' . $value : $rel_table;

					if ($fetch_rel_sup) {
						$query = "SELECT " . Inflector::singularize($value) . "_id as rel_id," . implode(',', $sup_fields);
						$query .= " FROM $rel_table
									WHERE " . Inflector::singularize($this->table_name) . "_id=" . $ret[$i]->id;
						$rel_ids = array();
						$extra_fields = array();
						if ($sql->Query($query)) {
							for ($j = 0; $j < $sql->rows; $j++) {
								$sql->GetRow($j);
								$rel_id = $sql->data->rel_id;
								$rel_ids[] = $sql->data->rel_id;
								foreach ($sql->data as $k => $v) {
									if ($k != 'rel_id') {
										$extra_fields[$k][$rel_id] = $v;
									}
								}
							}
						}
					} else {
						$query = "SELECT " . Inflector::singularize($value) . "_id as rel_id FROM $rel_table WHERE " . Inflector::singularize($this->table_name) . "_id=" . $ret[$i]->id;
						$rel_ids = array();
						if ($sql->Query($query)) {
							for ($j = 0; $j < $sql->rows; $j++) {
								$sql->GetRow($j);
								if ($sql->data->rel_id) {
									$rel_ids[] = $sql->data->rel_id;
								}
							}
						}
					}

					$ret[$i]->{Inflector::pluralize(Inflector::classify($value))} = array();
					if (empty($rel_ids)) {
						continue;
					}
//						require_once($cfg->get('root') . $cfg->get('models') . strtolower(Inflector::singularize($value)) . ".php");
					$value = Inflector::classify($value);
					$vl = '\\Models\\' . $value;
					$subModel = new $vl;
					$subModel->recursive = isset($rel['reversedByTable']) ? false : $this->recursive;
//						$subModel->setTableName(Inflector::tableize($value));

					$value = Inflector::pluralize($value);
//						$ret[$i]->{$value} = (call_user_method('getAll', $submodel, 0, 0, null, "AND id IN(".implode(', ', $rel_ids).")"));
					$ret[$i]->{$value} = (call_user_func(array(&$subModel, 'getAll'), 0, 0, null, "AND " . Inflector::tableize($value) . ".id IN(" . implode(', ', $rel_ids) . ")"));

					if (!empty($extra_fields)) {
						foreach ($ret[$i]->{$value} as $k => $v) {
							$id = $v->id;
							foreach ($extra_fields as $field_name => $field_value) {
								if (!empty($field_value[$id])) {
									$ret[$i]->{$value}[$k]->{$field_name} = $field_value[$id];
								}
							}
						}
					}
				}
			}
		}
		return $ret;
	}

	/**
	 * Intoarce Paginarea
	 *
	 * @return string
	 */
	public function getPages()
	{
		return $this->pages;
	}

	/**
	 * Salveaza datele
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function Save(&$data = [])
	{
		$sql = Mysql::getInstance();
		$ret = true;
		// for model->Save() without data -> save the model
		if (empty($data)) {
			foreach ($this->fields as $field) {
				$data[$field] = $this->{$field};
			}
		}

		if ($this->validate($data)) {

			if (method_exists($this, 'preSave')) {
				$data = call_user_func(array(&$this, 'preSave'), $data);
				if (!$data) {
					return false;
				}
			}
			// Insert
			if (!isset($data[$this->pk]) || $data[$this->pk] == 0) {
				if (method_exists($this, 'preInsert')) {
					$data = call_user_func([&$this, 'preInsert'], $data);
					if (!$data) {
						return false;
					}
				}

				$query = "INSERT INTO {$this->table_name} SET ";
				foreach ($data as $key => $value) {
					if ($key == $this->pk) {
						continue;
					}
					if (!in_array($key, $this->fields)) {
						continue;
					}
					if (property_exists($this, $key) && $this->$key === null && ($value == '')) {
						$query .= "`" . $key . "`= NULL, ";
					} else {
						$query .= "`" . $key . "`='" . addslashes($value) . "', ";
					}
				}
				$query = substr($query, 0, -2);;
				$ret = $sql->Insert($query);
				$data['id'] = $this->id = $sql->InsertID();

				if ($ret && method_exists($this, 'postInsert')) {
					$data = call_user_func([&$this, 'postInsert'], $data);
				}
			} // Update
			else {
				if (method_exists($this, 'preUpdate')) {
					$data = call_user_func([&$this, 'preUpdate'], $data);
					if (!$data) {
						return false;
					}
				}
				$query = "UPDATE `" . $this->table_name . "` SET ";

				foreach ($data as $key => $value) {
					if ($key == $this->pk) {
						continue;
					}
					if (!in_array($key, $this->fields)) {
						continue;
					}

					if ($value === null) {
						$query .= "`" . $key . "`= NULL, ";
					} else {
						$query .= "`" . $key . "`='" . addslashes($value) . "', ";
					}
				}
				$query = substr($query, 0, -2);
				$query .= " WHERE `" . $this->pk . "` = '" . addslashes($data[$this->pk]) . "'";
				$ret = $sql->Update($query);

				if ($ret && method_exists($this, 'postUpdate')) {
					$data = call_user_func([&$this, 'postUpdate'], $data);
				}
			}
		} else {
			if (method_exists($this, 'postSave') && $this->id > 0) { // salveaza restul obiectelor, daca e editare, chiar daca nu e valid
				call_user_func(array(&$this, 'postSave'), $data);
			}
			return false;
		}
		if ($ret && method_exists($this, 'postSave')) {
			return $data = call_user_func(array(&$this, 'postSave'), $data);
		}

		return $ret;
	}


	/**
	 * @param array $data |$this
	 * @return bool
	 */
	public function validate($data = [])
	{
		// for model->validate() without data validate fields
//		Utils::pr ($data); Utils::pr(get_class($this)); Utils::pr(debug_backtrace(true));
		if (empty($data)) {
			foreach ($this->fields as $field) {
				$data[$field] = $this->{$field};
			}
		}

		$validator = new Validator();
		//$this->validates_errors = array();
		foreach ($this->validate as $param => $rules) {

			if (!is_array($rules)) {
				trigger_error("Model::validate() - validator for $param should be an array", E_USER_WARNING);
				return false;
			}
			$rules = array_reverse($rules);
			if(isset($rules['required'])) {
				if($rules['required'] === false && empty($data[$param])) {
					continue;
				}
				unset($rules['required']);
			}
			if(isset($rules['groups'])) {
				unset($rules['groups']);
			}
			foreach ($rules as $rule) {

				if (!isset($rule['rule']) || !isset($rule['message'])) {
					trigger_error("Model::validate() - rules for $param should be an array like array('rule'=>\$rule, 'message'=>\$message)", E_USER_WARNING);
					return false;
				}

				$res = true;
				$message = $rule['message'];        // the message if false
				$method = is_array($rule['rule']) ? array_shift($rule['rule']) : $rule['rule'];

				//
				//	Daca este o metoda definita in Model
				//
				if (method_exists($this, $method)) {
//					echo "Calling Model::$method()";

					$method_params = array($param, $data[$param]);
					foreach ($rule['rule'] as $key => $value) {
						if (isset($data[$value])) {
							$rule['rule'][$key] = $data[$value];
						}
					}
					$method_params = array_merge($method_params, $rule['rule']);
					$res = call_user_func_array(array(&$this, $method), $method_params);
				}

				//
				//	Daca este o metoda definita in View
				//
				elseif (method_exists($validator, $method)) {
					//echo "Calling Validator::$method()";
					$method_params = array();
					@array_push($method_params, $data[$param]);
					$method_params = array_merge($method_params, $rule['rule']);
					$res = call_user_func_array(array(&$validator, $method), $method_params);
				}

				//
				//	Daca este un string - regexp
				//
				elseif (is_string($rule['rule'])) {
//					echo "Regexp {$rule['rule']}";
					$res = preg_match($rule['rule'], $data[$param]);
					if ($res === false) {
						trigger_error("Model::validate() - Invalid pattern " . $rule['rule'], E_USER_ERROR);
						return false;
					}
				}

				//
				//	Nedefinita, normal nu ar trebui sa ajunga aici
				//	
				else {
					trigger_error("Model::validate() - Unknown handler Validator::$method($param)", E_USER_ERROR);
					return false;
				}

				if (!$res) {
					$this->validates_errors[$param] = $message;
				}
			}
		}
		return empty($this->validates_errors) ? true : false;
	}


	/**
	 * Sterge inregistrarea cu acest id
	 * @param int $id
	 * @param bool $cascadate
	 * @return bool
	 */
	public function Delete($id = 0, $cascadate = false)
	{
		$sql = Mysql::getInstance();
		if ($id == 0) {
			$id = $this->id;
		}
		if ($id > 0) {
			$query = "DELETE " . $this->table_name;

			if ($this->hasOne) {
				foreach ($this->hasOne as $field) {
					$rel_table = Inflector::tableize($field);
					$query .= ", `" . $rel_table . "` ";
				}
			}

			$query .= " FROM `" . $this->table_name . "` ";

			if ($this->hasOne) {
				foreach ($this->hasOne as $field) {
					$rel_table = Inflector::tableize($field);
					$query .= ", `" . $rel_table . "` ";
				}
			}

			$query .= " WHERE `id`='" . addslashes($id) . "'";

			if ($this->hasOne) {
				foreach ($this->hasOne as $field) {
					$rel_table = Inflector::tableize($field);
					$query .= " AND " . $this->table_name . ".id = " . $rel_table . "." . Inflector::singularize($this->table_name) . "_id ";
				}
			}
			$sql->Delete($query);

			if ($this->hasMany && $cascadate) {
				foreach ($this->hasMany as $table) {
					$query = "DELETE FROM {$table} WHERE " . Inflector::singularize($this->table_name) . "_id=" . (int)$id;
					$sql->Delete($query);
				}
			}
		}
		return true;
	}

	/**
	 * Describer
	 *
	 * @return array
	 */
	public function describe()
	{
		$mirror = clone($this);
		Model::filter($mirror);
		return $mirror;
	}

	/**
	 * @param object|array $obj
	 */
	public static function filter($obj)
	{
		$to_unset = ['hasMany', 'hasAndBelongsToMany', 'hasOne', 'belongsTo', 'validate', 'validates_errors', 'table_name',
			'view_name', 'pages', 'items', 'pagingType',
			'pk', 'recursive', 'fields', 'validate_passwd_lost'];

		foreach ($obj as $key => $val) {
			if (in_array($key, $to_unset)) {
				unset($obj->$key);
			}
			if (is_array($val)) {
				foreach ($val as $idx => $item) {
					if (is_object($item)) {
						Model::filter($item);
					}
				}
			}
			if (is_object($val)) {
				Model::filter($val);
			}
		}

	}

	/**
	 * Check if a field is unique in the Datbase
	 *
	 * @param string $fieldName
	 * @param string|int $value
	 * @param int $id
	 * @return boolean
	 */
	public function isUnique($fieldName, $value, $id = 0)
	{

		$sql = Mysql::getInstance();
		$id = (int)$id;

		$query = "SELECT count(1) FROM " . $this->table_name . "
				WHERE `$fieldName`='" . addslashes($value) . "'";

		if ($id > 0) {
			$query .= "AND id!=" . $id;
		}

		$items = $sql->QueryItem($query);
		return $items > 0 ? false : true;
	}

	/**
	 * @param array $errors
	 */
	public function pushErrors($errors)
	{
		$this->validates_errors = array_merge($this->validates_errors, $errors);
	}


	/**
	 * @param $param
	 * @return bool|static
	 */
	public function __get($param)
	{
//		echo "In Get $param\n";
		$sql = Mysql::getInstance();
		$thisClassName = Inflector::classify($this->table_name);
		$bt = debug_backtrace(false);
//		echo "<div class=pure-u-1>";
//		echo "Called from {$bt[0]['file']}, line {$bt[0]['line']}, __get({$bt[0]['args'][0]})<br/>";
//		echo "Lazy loading $thisClassName->$param<hr/></div>";
//		$ret = [];

		$lcParam = Inflector::tableize($param);
		$found = false;
		foreach ($this->hasMany as $value) {
			if ($value == $lcParam) {
				$found = true;
				break;
			}
		}
		if ($found) {
			$className = '\\Models\\' . Inflector::classify($lcParam);
			$model = new $className;
			$model->recursive = false;
			$methodName = 'getBy' . Inflector::classify($this->table_name) . Inflector::camelize($this->pk);
			$this->$param = call_user_func(array(&$model, $methodName), $this->id);
//			echo "Returning ".count($this->$param);
			return ($this->$param);
		}

		$rule = $lcParam;
		foreach ($this->hasAndBelongsToMany as $key => $value) {
			if (is_array($value)) {
				if ($lcParam == $key) {
					$rule = $value;
					$found = true;
					break;
				}
			} else {
				if ($lcParam == $value) {
					$found = true;
					break;
				}
			}
		}
		if ($found) {
			$fetch_rel_sup = false;
			$rel_table = '';
			if (isset($rule['fetch_rel_sup'])) {
				$fetch_rel_sup = true;
			}
			if (isset($rule['reversedByTable'])) {
				$rel_table = $rule['reversedByTable'];
			}
			$rel_table = empty($rel_table) ? $this->table_name . '_' . $lcParam : $rel_table;

			//
			//	Seems it's faster to use an extra query to obtain id's and do an `AND var in (implode ids)` than do an `AND var in (SELECT...)`
			//		These is because sometimes you can join first table with hundreds of rows to this sub-select and result stinks

//			$subQuery = "AND " . $lcParam . ".id IN (SELECT " . Inflector::singularize($lcParam) . "_id as rel_id FROM $rel_table WHERE " . Inflector::singularize($this->table_name) . "_id=" . $this->id . ")";
//			echo $subQuery;

			$q = "SELECT " . Inflector::singularize($lcParam) . "_id as rel_id FROM $rel_table WHERE " . Inflector::singularize($this->table_name) . "_id=" . $this->id;
			$ids = [];
			if($sql->Query($q)) {
				for($i = 0; $i < $sql->rows; $i++) {
					$sql->GetRow($i);
					$ids[] = $sql->data->rel_id;
				}
			}

			$modelName = '\\Models\\' . Inflector::classify($lcParam);
			$model = new $modelName;
			$model->recursive = false;

			if(!empty($ids)) {
				$subQuery = "AND " . $lcParam . ".id IN (".implode(',',$ids).")";
				$this->$param = (call_user_func([&$model, 'getAll'], 0, 0, null, $subQuery));
			} else {
				$this->$param = [];
			}

			if ($fetch_rel_sup) {

				$sup_fields = $rule['fetch_rel_sup'];
				$query = " SELECT " . Inflector::singularize($lcParam) . "_id as rel_id, " . implode(',', $sup_fields);
				$query .= " FROM $rel_table WHERE " . Inflector::singularize($this->table_name) . "_id=" . $this->id;
				$rel_ids = array();
				$extra_fields = array();
				if ($sql->Query($query)) {
					for ($i = 0; $i < $sql->rows; $i++) {
						$sql->GetRow($i);
						$rel_id = $sql->data->rel_id;
						$rel_ids[] = $sql->data->rel_id;
						foreach ($sql->data as $k => $v) {
							if ($k != 'rel_id') {
								$extra_fields[$k][$rel_id] = $v;
							}
						}
					}
				}

				if (!empty($extra_fields)) {
					foreach ($this->$param as $k => $obj) {
						$id = $obj->id;
						foreach ($extra_fields as $field_name => $field_value) {
							if (!empty($field_value[$id])) {
								$this->{$param}[$k]->$field_name = $field_value[$id];
							}
						}
					}
				}
			}
			return $this->$param;
		}

		$class = Inflector::classify($lcParam);
		foreach ($this->belongsTo as $className => $value) {
			if ($className == $class) {
				$found = true;
				break;
			}
		}
		if ($found) {
			$className = '\\Models\\' . $class;
			$model = new $className;
			$model->recursive = false;
			$id = Inflector::underscore($class) . '_id';
			$methodName = 'getById';
			$this->$param = call_user_func(array(&$model, $methodName), $this->$id);
			return ($this->$param);
		}

//		if(property_exists($this, $param)) {
//			return $this->$param;
//		}

		trigger_error("Cannot get $thisClassName->$param, \n in file {$bt[0]['file']}, line {$bt[0]['line']}", E_USER_WARNING);
		return false;

	}

	public function isValid()
	{
		return empty($this->validates_errors) ? true:false;
	}
	public function pushValidateRules($arr) {
		$this->validate = array_merge($this->validate, $arr);
	}
//	public function __set($fieldName, $value)
//	{
//		//echo "In Set $fieldName\n";
//		//Utils::vd($value);
//		$this->$fieldName = $value;
//	}
}
