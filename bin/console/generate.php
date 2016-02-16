<?php

	use Classes\Inflector;
	use Classes\Router;
	use Classes\Utils;
	//
	//	Generate generic files 4 the MVC
	//			
	//		ver 0.2 - 28/09/2008 - fixed belongsTo in model, {$pages} in index.tpl, precontrollers


	if(!defined('ROOT')) {
		define('ROOT', realpath(dirname(__FILE__).'/../../') . '/');
	}
	require ROOT . 'include/conf.php';

//	require_once($cfg->get('root') . $cfg->get('classes') . 'Router.php');
	
	if ($argc != 2) {
		die("\nUsage: " . $argv[0] . " [table_name]\n");
	}
	

	if (!is_dir($cfg->get('root') . $cfg->get('models'))) {
		mkdir($cfg->get('root') . $cfg->get('models'), 0755);
	}
	if (!is_dir($cfg->get('root') . $cfg->get('controllers'))) {
		mkdir($cfg->get('root') . $cfg->get('controllers'), 0755);
	}
	if (!is_dir($cfg->get('root') . $cfg->get('views'))) {
		mkdir($cfg->get('root') . $cfg->get('views'), 0755);
	}
	
	
	$table_name = strtolower($argv[1]);
	
	$model_name = Inflector::camelize(Inflector::singularize($table_name));

	$model_file = ucfirst($model_name) . '.php';


	$controller_name = Inflector::camelize($table_name);

//	$controller_file = $table_name . ".php";
//	$controller_file = Inflector::variablize($controller_name) . ".php";
	$controller_file = ucfirst($controller_name) . ".php";


//	$view_relative_path = 'main/' . $table_name . '/';
	$view_relative_path = 'main/' . Inflector::variablize($controller_name). '/';
	$actions = array('index', 'add', 'edit', 'delete', 'view');
	
	$admin_prefix = Router::DEFAUL_ADMIN_RUTE . "_";

//	$model = new Model();
//	$ret = $model->setTableName($table_name);

	$sql->SetFetchType(FETCH_ASSOC);
	$query = "DESCRIBE $table_name";
	$fields = array();
	if ($sql->Query($query)) {
		for ($i=0; $i < $sql->rows; $i++) {
			$sql->GetRow($i);
			$type = preg_split("/\\(/", $sql->data['Type']);
//			Utils::pr($type);
			$fields[] = array(
				'name' => $sql->data['Field'],
				'type' => $type[0],
			);	
		}
	} else {
		die($sql->LastError());
	}
	
//	Utils::pr($fields);
	
	//if (!$ret) {
	//	die("No such table $table_name\n");
	//}
	
	//
	//	Model
	//
	if (! is_file($cfg->get('root') . $cfg->get('models') . $model_file)) {

		$str ="<?php
namespace Models;

use Classes\\Model;

//
//	$model_name Model
//
class $model_name extends Model
{

";


		foreach ($fields as $field) {

			switch($field['type']) {

				case 'int':
				case 'smallint':
				case 'tinyint':
					$type = 'int';
					break;

				case 'varchar':
				case 'text':
				case 'char':
					$type = 'string';
					break;
				case 'enum':
					$type = 'boolean'; // most of the time
					break;
				default:
					$type = $field['type'];
			}
			$str .= "	/** @var {$type} \${$field['name']} */\n";
			$str .= "	public \${$field['name']};\n\n";
		}
		$str.="

	protected \$hasMany = [];
	protected \$hasAndBelongsToMany = [];
	protected \$hasOne = [];
	protected \$belongsTo = [];

	public \$validate = [";
		foreach ($fields as $field) {
			$fieldName = $field['name'];
			$fieldType = $field['type'];
			switch($field['type']) {

				case 'int':
				case 'smallint':
				case 'tinyint':
					$fieldType = 'int';
					break;

				case 'varchar':
				case 'text':
				case 'char':
					$fieldType = 'string';
					break;
				case 'enum':
					$fieldType = 'boolean'; // most of the time
					break;
				default:
					$fieldType = $field['type'];
			}
			if ($field['name'] == 'id') { // forced
				continue;
			}
			
			$str.="

		'$fieldName'=>[
			[
				'rule'=>VALID_NOT_EMPTY, 
				'message'=>'".Inflector::humanize($fieldName)." cannot be left blank'
			],";
			if($fieldType=='int') {
				$str .= " [
				'rule'=>VALID_INTEGER,
				'message'=>'".Inflector::humanize($fieldName)." is not an integer'
			],";
			}

			if($fieldType=='float') {
				$str .= " [
				'rule'=>VALID_NUMERIC,
				'message'=>'".Inflector::humanize($fieldName)." is not a number'
			],";
			}
			$str .= "
			'required'=>true,
			'groups'=>['default','a_group'], ";
			$str .= "
		],";
		} // end foreach
		$str .="
	];";
		
		$str .= "
	public function __construct()
	{
		parent::__construct();
	}

	function testFunc(\$fieldname, \$value)
	{
		//echo \$fieldname . '=>' . \$value<hr>;
		return true;
	}		
";
		$str .= "
}

";
		file_put_contents($cfg->get('root') . $cfg->get('models') . $model_file, $str);
	} else {
		echo $cfg->get('root') . $cfg->get('models') . $model_file . " allready exists\n";
	}
	
	
	//
	//	Controller
	//
	if (! is_file($cfg->get('root') . $cfg->get('controllers') . $controller_file)) {
		
		$str="<?php
namespace Controllers;

use Classes\\Controller;
use Classes\\Utils;

//
//	$controller_name Controller 
//
class $controller_name extends Controller
{
	

	/**
	 *	Controllers to be before this controller
	 */	
	public \$pre_controllers = array (
//		array ('controller_name'=>'action'),
	);
	
	/**
	 *	Controllers to be loaded after this controller
	 */	
	public \$post_controllers = array (
//		array ('controller_name'=>'action'),
	);
";

		
		// index //
		$str .= "
		
		
	/**
	 * List ".strtolower($controller_name)."
	 */
	public function admin_index()
	{
		global \$cfg;
		\$".Inflector::variablize($controller_name)." = \$this->model->getAll(\$this->page, \$cfg->get('items_per_page'));
		\$this->view->assign_by_ref('".Inflector::variablize($controller_name)."', \$".Inflector::variablize($controller_name).");
		\$this->view->assign('pages', \$this->model->getPages());
	}
";
		// add //
		$str .="
		
		
	/**
	 * Add a ".Inflector::singularize(strtolower($controller_name))."
	 */
	public function admin_add()
	{
		if (! empty(\$this->data)) {
			if (! \$this->model->Save(\$this->data)) {
				\$this->view->riseError('Cannot save data');
			} else {
				\$this->view->showMessage('Data saved');
			}	
		}	
	}
";
		// edit //
		$str .="
		
		
	/**
	 * Edit a ".Inflector::singularize(strtolower($controller_name))."
	 */
	public function admin_edit()
	{
		\$this->model->recursive=false;
		if (! empty(\$this->data)) {
			if (! \$this->model->Save(\$this->data)) {
				\$this->view->riseError('Cannot save');
			} else {
				Utils::Redirect('/admin/".Inflector::variablize($controller_name)."/');
			}
		} else {
			\$".Inflector::singularize(Inflector::variablize($controller_name))." = \$this->model->getById(\$this->params->id);
			if (\$".Inflector::singularize(Inflector::variablize($controller_name)).") {
				//\$".Inflector::singularize(Inflector::variablize($controller_name))." = array_shift(\$".Inflector::singularize(Inflector::variablize($controller_name)).");
				\$this->view->assign_by_ref('".Inflector::singularize(Inflector::variablize($controller_name))."', \$".Inflector::singularize(Inflector::variablize($controller_name)).");
			}
		}
	}
";
		
		// delete //
		$str .= "
		
		
	/**
	 * Delete a " . (($model_name))."
	 */
	public function admin_delete()
	{
		\$this->model->Delete(\$this->params->id);
		Utils::Redirect('/admin/".Inflector::variablize($controller_name)."/');
	}
";
		
		// view //
		$str .= "
		
		
	/**
	 * View a ".($model_name)."
	 */
	public function admin_view()
	{
		if (! empty(\$this->params->id)) {
			\$".lcfirst($model_name)." = \$this->model->getById(\$this->params->id);
			\$this->view->assign('".lcfirst($model_name)."', \$".lcfirst($model_name).");
		}
	}
";
		$str .="
}
";
		file_put_contents($cfg->get('root') . $cfg->get('controllers') . $controller_file, $str);
	} else {
		echo $cfg->get('root') . $cfg->get('controllers') . $controller_file . " allready exists\n";
	}
	
	
	//
	//	Views
	//
	if (! is_dir($cfg->get('root') . $cfg->get('views') . $view_relative_path)) {
		mkdir($cfg->get('root') . $cfg->get('views') . $view_relative_path, 0755);
	} else {
		echo "Directory " . $cfg->get('root') . $cfg->get('views') . $view_relative_path . " allready exists\n";
	}

	// admin_index //
	if (!is_file($cfg->get('root') . $cfg->get('views') . $view_relative_path . 'admin_index.tpl')) {
		$str = "<h1>".Inflector::pluralize($model_name)."</h1>\n\n";

        $str .= "<div class=\"pure-menu pure-menu-horizontal contextmenu\">\n";
        $str .= "\t<ul class=\"pure-menu-list\">\n";
        $str .= "\t\t<li class=\"pure-menu-item\"><a href=\"/admin/".Inflector::variablize($controller_name)."/add/\" class=\"pure-menu-link\">Add</a></li>\n";
		$str .= "\t</ul>\n";
		$str .= "</div>\n\n";

        $str .="{\$pages}\n";
        $str .= "<table width=\"100%\" class=\"pure-table pure-table-bordered\">\n";
		$str .= "\t<thead><tr>\n";
		foreach ($fields as $field) {
			$str .= "\t\t<th>" . Inflector::humanize($field['name']) . "</th>\n";
		}
		$str .= "\t\t<th colspan=\"3\">Actions</th>\n";
		$str .= "\t</tr></thead>\n";
		$str .= "\t<tbody>\n";
		$str .= "\t{section name=".substr(strtolower($controller_name), 0, 1)." loop=\$".Inflector::variablize($controller_name)."}\n";
		$str .="\t<tr>\n";
		foreach ($fields as $field) {
			$str .= "\t\t<td>{\$".Inflector::variablize($controller_name)."[".substr(strtolower($controller_name), 0, 1)."]->".$field['name']."}</td>\n";
		}
		$str .="\t\t<td><a href=\"/admin/".Inflector::variablize($controller_name)."/view/{\$".Inflector::variablize($controller_name)."[".substr(strtolower($controller_name), 0, 1)."]->id}/\">View</a></td>\n";
		$str .="\t\t<td><a href=\"/admin/".Inflector::variablize($controller_name)."/edit/{\$".Inflector::variablize($controller_name)."[".substr(strtolower($controller_name), 0, 1)."]->id}/\">Edit</a></td>\n";
		$str .="\t\t<td><a href=\"/admin/".Inflector::variablize($controller_name)."/delete/{\$".Inflector::variablize($controller_name)."[".substr(strtolower($controller_name), 0, 1)."]->id}/\">Delete</a></td>\n";
		$str .= "\t</tr>\n";
		$str .="\t{/section}\n";
		$str .="</tbody>\n";
		$str .="</table>\n";
		$str .="{\$pages}\n";

		file_put_contents($cfg->get('root') . $cfg->get('views') . $view_relative_path . 'admin_index.tpl', $str);
		
	} else {
		echo $cfg->get('root') . $cfg->get('views') . $view_relative_path . "admin_index.tpl allready exists, not overwriting\n";
	}
	
	
	
	// admin_add //
	if (!is_file($cfg->get('root') . $cfg->get('views') . $view_relative_path . 'admin_add.tpl')) {
		$str = "<h1>".Inflector::pluralize($model_name)."</h1>\n\n";
		$str .= "<div class=\"pure-menu pure-menu-horizontal contextmenu\">\n";
		$str .= "\t<ul class=\"pure-menu-list\">\n";
		$str .= "\t\t<li class=\"pure-menu-item\"><a href=\"/admin/".Inflector::variablize($controller_name)."/\" class=\"pure-menu-link\">All</a></li>\n";
		$str .= "\t</ul>\n\n";
		$str .= "</div>\n\n";

		$str .= "<form method=\"post\" action=\"/admin/".Inflector::variablize($controller_name)."/add/\" class=\"pure-form pure-form-aligned\">\n";
		$str .="\t<fieldset>\n";
		$str .="\t\t<legend>Add New ".$model_name."</legend>\n\n";
		$str .="\t\t<div class=\"err\">{\$err}</div>\n";
		$str .="\t\t<div class=\"msg\">{\$msg}</div>\n\n";
		
		foreach ($fields as $field) {
			if ($field['name'] == 'id') {
				continue;
			}
            $str .= "\t\t<div class=\"pure-control-group\">\n";
            $str .= "\t\t\t<label for=\"".$field['name']."\">".Inflector::humanize($field['name']).":</label>\n";
			if ($field['type'] == 'text') {
				$str .= "\t\t\t<textarea name=\"data[".$field['name']."]\" id=\"".$field['name']."\">{\$".$field['name']."}</textarea>\n";
			} else {
				$str .= "\t\t\t<input type=\"text\" name=\"data[".$field['name']."]\" id=\"".$field['name']."\" value=\"{\$".$field['name']."}\">\n";
			}
			$str .= "\t\t\t{if \$err_msg.".$field['name']."}<div class=\"err_msg\">{\$err_msg.".$field['name']."}</div>{/if}\n";
			$str .= "\t\t</div>\n\n";
		}
		
		$str .= "\t\t<div class=\"pure-controls\">\n";
        $str .= "\t\t\t<input type=\"submit\" value=\"Add $model_name\" class=\"pure-button pure-button-primary sbmt\">\n";
        $str .= "\t\t</div>\n";

		$str .= "\t</fieldset>\n";
		$str .= "</form>";
		
		file_put_contents($cfg->get('root') . $cfg->get('views') . $view_relative_path . 'admin_add.tpl', $str);
	} else {
		echo $cfg->get('root') . $cfg->get('views') . $view_relative_path . "admin_add.tpl allready exists, not overwriting\n";
	}
	
	
	
	// admin_edit //
	if (!is_file($cfg->get('root') . $cfg->get('views') . $view_relative_path . 'admin_edit.tpl')) {
		$str = "<h1>".Inflector::pluralize($model_name)."</h1>\n\n";

        $str .= "<div class=\"pure-menu pure-menu-horizontal contextmenu\">\n";
        $str .= "\t<ul class=\"pure-menu-list\">\n";
        $str .= "\t\t<li class=\"pure-menu-item\"><a href=\"/admin/".Inflector::variablize($controller_name)."/\" class=\"pure-menu-link\">All</a></li>\n";
        $str .= "\t\t<li class=\"pure-menu-item\"><a href=\"/admin/".Inflector::variablize($controller_name)."/edit/{\$".lcfirst($model_name)."->id}/\" class=\"pure-menu-link\">Edit</a></li>\n";
        $str .= "\t\t<li class=\"pure-menu-item\"><a href=\"/admin/".Inflector::variablize($controller_name)."/delete/{\$".lcfirst($model_name)."->id}/\" class=\"pure-menu-link\">Delete</a></li>\n";
        $str .= "\t</ul>\n";
        $str .= "</div>\n\n";

		$str .= "<form method=\"post\" action=\"/admin/".Inflector::variablize($controller_name)."/edit/{\$".lcfirst($model_name)."->id}/\" class=\"pure-form pure-form-aligned\">\n";
		$str .="\t<fieldset>\n";
		$str .="\t\t<legend>Edit ".$model_name."</legend>\n\n";
		$str .="\t\t<div class=\"pure-controls err\">{\$err}</div>\n";
		$str .="\t\t<div class=\"pure-controls msg\">{\$msg}</div>\n\n";

        foreach ($fields as $field) {
            if ($field['name'] == 'id') {
                continue;
            }
            $str .= "\t\t<div class=\"pure-control-group\">\n";
            $str .= "\t\t\t<label for=\"".$field['name']."\">".Inflector::humanize($field['name']).":</label>\n";
            if ($field['type'] == 'text') {
                $str .= "\t\t\t<textarea name=\"data[".$field['name']."]\" id=\"".$field['name']."\">{\$".lcfirst($model_name). '->' .$field['name']."}</textarea>\n";
            } else {
                $str .= "\t\t\t<input type=\"text\" name=\"data[".$field['name']."]\" id=\"".$field['name']."\" value=\"{\$".lcfirst($model_name). '->' .$field['name']."}\">\n";
            }
            $str .= "\t\t\t{if \$err_msg.".$field['name']."}<div class=\"err_msg\">{\$err_msg.".$field['name']."}</div>{/if}\n";
            $str .= "\t\t</div>\n\n";
        }


        $str .= "\t\t<div class=\"pure-controls\">\n";
        $str .= "\t\t<input type=\"hidden\" name=\"data[id]\" value=\"{\$".lcfirst($model_name)."->id}\">\n";
        $str .= "\t\t\t<input type=\"submit\" value=\"Save $model_name\" class=\"pure-button pure-button-primary sbmt\">\n";
        $str .= "\t\t</div>\n";

        $str .= "\t</fieldset>\n";
		$str .= "</form>";

		file_put_contents($cfg->get('root') . $cfg->get('views') . $view_relative_path . 'admin_edit.tpl', $str);
	} else {
		echo $cfg->get('root') . $cfg->get('views') . $view_relative_path . "admin_edit.tpl already exists, not overwriting\n";
	}
	
	
	// admin_view //
	if (!is_file($cfg->get('root') . $cfg->get('views') . $view_relative_path . 'admin_view.tpl')) {
		$str = "<h1>".Inflector::pluralize($model_name)."</h1>\n\n";

        $str .= "<div class=\"pure-menu pure-menu-horizontal contextmenu\">\n";
        $str .= "\t<ul class=\"pure-menu-list\">\n";
        $str .= "\t\t<li class=\"pure-menu-item\"><a href=\"/admin/".Inflector::variablize($controller_name)."/\" class=\"pure-menu-link\">All</a></li>\n";
        $str .= "\t\t<li class=\"pure-menu-item\"><a href=\"/admin/".Inflector::variablize($controller_name)."/add/\" class=\"pure-menu-link\">Add</a></li>\n";
        $str .= "\t\t<li class=\"pure-menu-item\"><a href=\"/admin/".Inflector::variablize($controller_name)."/edit/{\$".lcfirst($model_name)."->id}/\" class=\"pure-menu-link\">Edit</a></li>\n";
        $str .= "\t\t<li class=\"pure-menu-item\"><a href=\"/admin/".Inflector::variablize($controller_name)."/delete/{\$".lcfirst($model_name)."->id}/\" class=\"pure-menu-link\">Delete</a></li>\n";
        $str .= "\t</ul>\n";
        $str .= "</div>\n\n";

        $str .= "<table width=\"100%\" class=\"pure-table pure-table-bordered\">\n";
        $str .= "<tbody>\n";

		foreach ($fields as $field) {
			$str .= "\t<tr>\n";
			$str .= "\t\t<td>" . Inflector::humanize($field['name']) . "</td>\n";
			$str .= "\t\t<td>{\$".lcfirst($model_name)."->".$field['name']."}</td>\n";
			$str .= "\t</tr>\n";
		}
        $str .= "</tbody>\n";
		$str .="</table>\n";

		file_put_contents($cfg->get('root') . $cfg->get('views') . $view_relative_path . 'admin_view.tpl', $str);
		
	} else {
		echo $cfg->get('root') . $cfg->get('views') . $view_relative_path . "admin_view.tpl allready exists, not overwriting\n";
	}
	
	$cmd = "chown vio:vio ../../* -R";
	exec($cmd);
	$cmd = "chmod 777 ../../tmp/ -R";
	exec($cmd);
	
