<?PHP
// require_once( '../../../core.php' );
$edit_id			= gpc_get_int( 'edit_id' );
$title		= htmlentities($_REQUEST['taskcat_title'],ENT_COMPAT,'UTF-8');
$project = $_REQUEST['project'];
// does the new value already exist ??
$query= "select * from {plugin_Tasks_cat} where project_id='$project' and upper(trim(taskcat_title))=upper(trim('$title'))";
$result = db_query($query);
$res2=db_num_rows($result);
if ($res2 == 0){
	// perform update
	$save_title = db_prepare_string($title);
	$query = "UPDATE {plugin_Tasks_cat} set project_id=$project , taskcat_title='$save_title'  WHERE taskcat_id = $edit_id";
	if(!db_query($query)){ 
		trigger_error( 'ERROR_DB_QUERY_FAILED', ERROR );
	}		
}
print_header_redirect( 'plugin.php?page=Tasks/task_categories' );