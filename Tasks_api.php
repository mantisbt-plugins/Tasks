<?PHP
/**
 * Send a task reminder to each of the given user, or to each user if the first parameter is an array
 * return an array of usernames to which the reminder was successfully sent
 *
 * @param array $p_recipients
 * @param int $p_bug_id
 * @param string $p_message
 * @return null
 * support to be found on github.com/mantisbt-plugins/Tasks
 */
function email_task_reminder( $p_recipients, $p_bug_id, $p_message ) {
	if( !is_array( $p_recipients ) ) {
		$p_recipients = array(
			$p_recipients,
		);
	}
	$t_project_id = bug_get_field( $p_bug_id, 'project_id' );
	$t_sender_id = auth_get_current_user_id();
	$t_sender = user_get_name( $t_sender_id );

	$t_subject = "[[TASK]]";
	$t_subject .= email_build_subject( $p_bug_id );
	$t_date = date( config_get( 'normal_date_format' ) );

	$result = array();
	foreach( $p_recipients as $t_recipient ) {
		lang_push( user_pref_get_language( $t_recipient, $t_project_id ) );
		$t_email = user_get_email( $t_recipient );
		$result[] = user_get_name( $t_recipient );

		if( access_has_project_level( config_get( 'show_user_email_threshold' ), $t_project_id, $t_recipient ) ) {
			$t_sender_email = ' <' . current_user_get_field( 'email' ) . '>';
		} else {
			$t_sender_email = '';
		}
		$t_header = "\n" . lang_get( 'on_date' ) . " $t_date, $t_sender $t_sender_email " . lang_get( 'sent_you_this_reminder_about' ) . ": \n\n";
		$t_contents = $t_header . string_get_bug_view_url_with_fqdn( $p_bug_id, $t_recipient ) . " \n\n$p_message";

		if( ON == config_get( 'enable_email_notification' ) ) {
			email_store( $t_email, $t_subject, $t_contents );
		}

		lang_pop();
	}

	if( OFF == config_get( 'email_send_using_cronjob' ) ) {
		email_send_all();
	}

	return $result;
}
 
// retrieve groupname
 function group_get_name($grp){
	$sql="select group_name from {plugin_Usergroups_groups} where group_id=$grp ";
	$res= db_query($sql);
	$row = db_fetch_array($res);
	return $row['group_name'];
}

// check if user in group has correct level to handle task
function check_user_in_group($userid) {
	if (access_has_project_level(config_get( 'plugin_Tasks_tasks_allocate_threshold' ),null,$userid)){
		return true;
	}else{
		return false;
	}
}

/**
 * Send a task reminder to group mail address
 * @param array $p_mail
 * @param int $p_bug_id
 * @param string $p_message
 * @return null
 */
function email_task_reminder2( $p_mail, $p_bug_id, $p_message ) {
	$t_project_id = bug_get_field( $p_bug_id, 'project_id' );
	$t_sender_id = auth_get_current_user_id();
	$t_sender = user_get_name( $t_sender_id );

	$t_subject = "[[TASK]]";
	$t_subject .= email_build_subject( $p_bug_id );
	$t_date = date( config_get( 'normal_date_format' ) );

	$t_sender_email = ' <' . current_user_get_field( 'email' ) . '>';
	$t_header = "\n" . lang_get( 'on_date' ) . " $t_date, $t_sender $t_sender_email " . lang_get( 'sent_you_this_reminder_about' ) . ": \n\n";
	$t_contents = $t_header . string_get_bug_view_url_with_fqdn( $p_bug_id, $t_recipient ) . " \n\n$p_message";
		if( ON == config_get( 'enable_email_notification' ) ) {
		email_store( $p_mail, $t_subject, $t_contents );
	}

	if( OFF == config_get( 'email_send_using_cronjob' ) ) {
		email_send_all();
	}

	return ;
}
