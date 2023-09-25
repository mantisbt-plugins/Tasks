<?PHP
//require_once( '../../../core.php' );
$project = gpc_get_string( 'project');
$category = $_REQUEST['category'];
$taskcategory = $_REQUEST['taskcat_id'];
$handler	= $_REQUEST['autotask_handler'];
$title		= htmlentities($_REQUEST['autotask_title'],ENT_COMPAT,'UTF-8');
$desc		= htmlentities($_REQUEST['autotask_desc'],ENT_COMPAT,'UTF-8');
$duedays = $_REQUEST["autotask_due"];
$status = $_REQUEST["tasks_auto_start_status"];
$save_desc = db_prepare_string($desc);
// perform some checks and trigger error if not as required
if ($handler == 0) {
	trigger_error( 'ERROR_TASKS_NOHANDLER', ERROR );
}
if (empty($title)) {
	trigger_error( 'ERROR_BUG_EMPTY_TITLE', ERROR );
}
if (empty($desc)) {
	trigger_error( 'ERROR_BUG_EMPTY_DESC', ERROR );
}
if (empty($duedays)) {
	trigger_error( 'ERROR_BUG_EMPTY_DATE', ERROR );
}
if (empty($category)) {
	trigger_error( 'ERROR_NO_CATEGORY', ERROR );
}
if (empty($taskcategory)) {
	trigger_error( 'ERROR_NO_TASKCATEGORY', ERROR );
}
// all ok, so add the autotask to the database
$query = "INSERT INTO {plugin_Tasks_autodefined} ( project_id,category_id, autotask_handler,autotaskcat_id,autotask_title,autotask_desc,autotask_due,autotask_status )
  		VALUES (  '$project','$category', '$handler','$taskcategory','$title', '$save_desc',   '$duedays','$status')";
if(!db_query($query)){
	trigger_error( 'ERROR_DB_QUERY_FAILED', ERROR );
}
print_header_redirect( 'plugin.php?page=Tasks/task_definition' );
