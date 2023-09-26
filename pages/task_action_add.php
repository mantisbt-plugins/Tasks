<?PHP
require_once( config_get( 'plugin_path' ) . 'Tasks' . DIRECTORY_SEPARATOR . 'Tasks_api.php' );
$user 			= gpc_get_int( 'user' );
$bug_id			= gpc_get_int( 'bug_id' );
# should event be logged in the project
$create_his		= config_get( 'plugin_Tasks_tasks_history' );
# should mail be send to assignee
$create_mail	= config_get( 'plugin_Tasks_tasks_mail' );
$use_groups		= config_get( 'plugin_Tasks_tasks_assign_group' );
$group = 0;
# Adding task
$handler	= $_REQUEST['task_handler'];
if ( ON == $use_groups ) {
	$group		= $_REQUEST['task_group'];
}
$taskcat	= (int)$_REQUEST['cat_id'];
$title		= htmlentities($_REQUEST['task_title'],ENT_QUOTES,'UTF-8');
$desc		= htmlentities($_REQUEST['task_desc'],ENT_QUOTES,'UTF-8');
$task_due 	= date('Y-m-d H:i', strtotime($_REQUEST["task_due"]));
// perform some checks if all required data has been submitted
if (( $handler ==0) and ($group == 0)){
	trigger_error( 'ERROR_TASKS_NOHANDLER', ERROR );
}
if (( $handler >0) and ($group > 0)){
	trigger_error( 'ERROR_TASKS_ONEHANDLER', ERROR );
}
if (empty($title)) {
	trigger_error( 'ERROR_BUG_EMPTY_TITLE', ERROR );
}
if (empty($desc)) {
	trigger_error( 'ERROR_BUG_EMPTY_DESC', ERROR );
}
if (empty($task_due)) {
	trigger_error( 'ERROR_BUG_EMPTY_DATE', ERROR );
}
// all ok, so add to database
$save_desc = db_prepare_string($desc);
$query = "INSERT INTO {plugin_Tasks_defined} ( bug_id,task_user, task_handler,task_title,task_desc,task_created,task_due,task_changed,taskcat_id,task_group )
 	VALUES (  '$bug_id','$user', '$handler', '$title', '$save_desc',  NOW(), '$task_due', NOW(), '$taskcat','$group')";
if(!db_query($query)){
	trigger_error( 'ERROR_DB_QUERY_FAILED', ERROR );
}
if ( ON == $create_his ) {
	history_log_event_direct( $bug_id, 'Tasks',$title, "Added", $user );
}
# email send to handler of task
if ( ON == $create_mail ) {
	$body  = lang_get( 'tasks_body' ). " \n\n";
	$body .= $title. " \n\n";
	$body .= lang_get( 'tasks_date' ). " \n\n";
	$body .= $task_due;
	if ($handler>0){
		$result = email_task_reminder( $handler,$bug_id, $body );
	} else {
		//  this task has been assigned to a group
		// First check if we are allowed to send to group mail and if mail address exists
		// If not, we need to send email to each member
		$ugrp_table	= plugin_table('usergroup','Usergroups');
		# can mail be send to groupmail
		$mail_group		= config_get( 'plugin_Usergroups_mail_group' );
		if ( ON == $mail_group ) {
			$sql = "select group_mail from {plugin_Usergroups_groups} where group_id=$group";
			$res = db_query($sql);
			while ($row = db_fetch_array($res)) {
				$mail =$row['group_mail'];
			}
		}
		if (trim($mail)<>""){
			$result = email_task_reminder2( $mail,$bug_id, $body );
		} else {
			$sql = "select user_id from {plugin_Usergroups_groups} where group_id=$group";
			$res = db_query($sql);
			while ($row = db_fetch_array($res)) {
				$handler=$row['user_id'];
				if (check_user_in_group($handler)){
					$result = email_task_reminder( $handler,$bug_id, $body );
				}
			}
		}
	}
}

print_header_redirect( 'view.php?id='.$bug_id.'' );
