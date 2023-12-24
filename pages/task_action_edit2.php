<?PHP
$go = gpc_get_bool( 'Stop' );
$bug_id			= gpc_get_int( 'id' );
if ( $go ) {
	$edit_id			= gpc_get_int( 'edit_id' );
	$title		= htmlentities($_REQUEST['task_title'],ENT_COMPAT,'UTF-8');
	$desc		= htmlentities($_REQUEST['task_desc'],ENT_COMPAT,'UTF-8');
	$handler	= $_REQUEST['task_handler'];
	$use_groups		= config_get( 'plugin_Tasks_tasks_assign_group' );
	if ( ON == $use_groups ) {
		$group		= $_REQUEST['task_group'];
	} else {
		$group=0;
	}
	$task_due 	= $_REQUEST["task_due1"];

	// first check submitted data
	if ( ( $handler ==0) and ($group == 0) ){
		trigger_error( 'ERROR_TASKS_NOHANDLER', ERROR );
	}
	if ( ( $handler >0) and ($group > 0) ){
		trigger_error( 'ERROR_TASKS_ONEHANDLER', ERROR );
	}
	if ( empty($task_due ) ) {
		trigger_error( 'ERROR_BUG_EMPTY_DATE', ERROR );
	}
	if ( empty($title) ) {
		trigger_error( 'ERROR_BUG_EMPTY_TITLE', ERROR );
	}
	if ( empty( $desc ) ) {
		trigger_error( 'ERROR_BUG_EMPTY_DESC', ERROR );
	}
	if (empty($group)) {
		$group=0;
	}
	# should event be logged in the project
	$create_his		= config_get( 'plugin_Tasks_tasks_history' );
	# Updating task
	// get current values
	$query = "SELECT * FROM {plugin_Tasks_defined} WHERE task_id = $edit_id ";
	$result = db_query($query);
	$row = db_fetch_array( $result );
	// perform update
	$save_desc = db_prepare_string($desc);
	$save_title = db_prepare_string($title);
	$query = "UPDATE {plugin_Tasks_defined} set task_changed=NOW(),task_title='$save_title',task_desc='$save_desc',task_handler=$handler,task_due='$task_due',task_group=$group  WHERE task_id = $edit_id";
	if(!db_query($query)){ 
		trigger_error( 'ERROR_DB_QUERY_FAILED', ERROR );
	}		
	if ( ON == $create_his ) {
		history_log_event_direct( $bug_id, 'Tasks-Edit', db_prepare_string($row['task_title']), $title, $user );
	}
}
print_header_redirect( 'view.php?id='.$bug_id );