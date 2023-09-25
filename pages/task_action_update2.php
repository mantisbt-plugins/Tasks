<?PHP
require_once( '../../../core.php' );
$update_id			= gpc_get_int( 'update_id' );
$response		= htmlentities($_REQUEST['task_response'],ENT_COMPAT,'UTF-8');
$bug_id			= gpc_get_int( 'id' );
$time = $_REQUEST['task_time'];
$action = gpc_get_bool( 'Action' );
# should event be logged in the project
$create_his		= config_get( 'plugin_Tasks_tasks_history' );
# should event be logged in the project
$update_issue	= config_get( 'plugin_Tasks_tasks_update_bug' );
# Updating task
// get current values
$query = "SELECT * FROM {plugin_Tasks_defined}  WHERE task_id = $update_id ";
$result = db_query($query);
$row = db_fetch_array( $result );
// perform update
$save_response = db_prepare_string($response);
$query = "UPDATE {plugin_Tasks_defined} set task_changed=NOW(),task_response='$save_response', task_time='$time' WHERE task_id = $update_id";
if(!db_query($query)){ 
	trigger_error( ERROR_DB_QUERY_FAILED, ERROR );
}		
if ( ON == $create_his ) {
	history_log_event_direct( $bug_id, 'Tasks-Response', db_prepare_string($row['task_response']), $response, $user );
}
if ($action ) {
	require_once( config_get( 'plugin_path' ) . 'Tasks' . DIRECTORY_SEPARATOR . 'Tasks_api.php' );  
	$query = "SELECT * FROM {plugin_Tasks_defined} WHERE task_id = $update_id ";
	$result = db_query($query);
	$row = db_fetch_array( $result );
	$bugid = $row['bug_id'];
	$answer =trim($row['task_response']);
	if ($answer == ""){
		trigger_error( 'ERROR_BUG_EMPTY_TASKS', ERROR );
	}
	// perform update
	$finuser = auth_get_current_user_id();
	$query = "UPDATE {plugin_Tasks_defined} set task_changed=NOW(),task_completed=NOW(),task_completed_by= $finuser WHERE task_id = $update_id";        
	if(!db_query($query)){ 
		trigger_error( 'ERROR_DB_QUERY_FAILED', ERROR );
	}
	if ( ON == $update_issue ) {
		$query = "UPDATE {bug} SET last_updated= " . db_param() . "  WHERE id=" . db_param();
		db_query( $query, Array( db_now(), $bug_id ) ); 
	}
	# email send to handler of issue
	# should mail be send to assignee
	$create_mail_finish	= config_get( 'plugin_Tasks_tasks_mail_finish' );
	if ( ON == $create_mail_finish ) {
		// get handler from original issue
		$sql = "select handler_id from {bug} where id=$bugid";
		$result2 = db_query($sql);
		$row2 = db_fetch_array( $result2 );
		$body  = lang_get( 'tasks_body_finish' ). " \n\n";
		$body .= $row['task_title']. " \n\n";
		$handler = $row2['handler_id'];
		$result = email_task_reminder( $handler,$bugid, $body );
	}	

	if ( ON == $create_his ) {
		history_log_event_direct( $bug_id, 'Tasks', $row['task_title'], 'Finished', $user );
	}
}
?>
<SCRIPT LANGUAGE="JavaScript">
<!--hide
window.close();
if (window.opener && !window.opener.closed) {
window.opener.location.reload();
} 
//-->
</SCRIPT>
