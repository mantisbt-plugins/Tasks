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
	
/*	// eightd-plugin related
	if (plugin_is_loaded('EIGHTD')){
		$tasks_table 	= plugin_table("defined","Tasks");
		$taskscat_table = plugin_table("cat","Tasks");
		$eightd_table	= plugin_table("data","EIGHTD");
		
		$user_table = db_get_table( 'mantis_user_table' );

		$query1 = "SELECT * FROM $eightd_table WHERE eightd_bug_id = $bugid ";
		$result1 = db_query($query1);
		$row1 = db_fetch_array($result1);
		$d3data  = $row1['d3data'];
		$d4data  = $row1['d4data'];
		$d5data  = $row1['d5data'];
		$d6data  = $row1['d6data'];
		$d7data  = $row1['d7data'];
		$d8data  = $row1['d8data'];

		# Pull Tasks-Record for the current Bug
		$query = "SELECT a.*,b.taskcat_title,c.realname FROM $tasks_table as a, $taskscat_table as b, $user_table as c WHERE a.task_completed_by=c.id and a.taskcat_id=b.taskcat_id and bug_id = $bugid and task_id = $update_id";
		$result = db_query($query);
		while ($row = db_fetch_array($result)) {
			$desc = html_entity_decode($row["task_desc"]);
			$res = html_entity_decode($row["task_response"]);
			$cat = $row['taskcat_title'];
			$type= substr(trim($cat),0,2);
			$sep =  " ==>> ";
			$change = false;
			$datfin = substr($row['task_completed'],0,10);
			$desc .= " (";
			$desc .= trim($row['realname']);
			$desc .= ")";
			$sep .=  "(";
			$sep .=  $datfin;
			$sep .=  ") ";
			if ($type== 'D3'){
				$change= true;
				$d3data .= '<br>'.$desc.$sep.$res;
			}
			if ($type== 'D4'){
				$change= true;
				$d4data .='<br>'.$desc.$sep.$res ;
			}
			if ($type== 'D5'){
				$change= true;
				$d5data .='<br>'.$desc.$sep.$res ;
			}
			if ($type== 'D6'){
				$change= true;
				$d6data .= '<br>'.$desc.$sep.$res ;
			}
			if ($type== 'D7'){
				$change= true;
				$d7data .= '<br>'.$desc.$sep.$res ;
			}
			if ($type== 'D8'){
				$change= true;
				$d8data .= '<br>'.$desc.$sep.$res ;
			}
			if ($change){
				$d3data= db_prepare_string(htmlentities($d3data));
				$d4data= db_prepare_string(htmlentities($d4data));
				$d5data= db_prepare_string(htmlentities($d5data));
				$d6data= db_prepare_string(htmlentities($d6data));
				$d7data= db_prepare_string(htmlentities($d7data));
				$d8data= db_prepare_string(htmlentities($d8data));
				$query = "update $eightd_table set d3data='$d3data',d4data='$d4data',d5data='$d5data',d6data='$d6data',d7data='$d7data',d8data='$d8data' WHERE eightd_bug_id= $bugid";
				$result = db_query($query);			
			}
		}
	}
	// eightd-plugin 
*/
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