<?php
class TasksPlugin extends MantisPlugin {

public string $nonce;
	function register() {
		$this->name        = 'Tasks';
		$this->description = lang_get( 'tasks_plugin_desc' );
		$this->version     = '3.31';
		$this->requires    = array('MantisCore'       => '2.0.0',);
		$this->author      = 'Cas Nuy';
		$this->contact     = 'Cas-at-nuy.info';
		$this->url         = 'https://github.com/mantisbt-plugins/Tasks';
		$this->page		   = 'config';
		$this->nonce = crypto_generate_uri_safe_nonce( 16 );
	}


 	function init() {
		event_declare('EVENT_MYVIEW');
		event_declare('EVENT_VIEW_BUG_DETAILS2');
		event_declare('EVENT_RESOLVE_BUG');
		event_declare('EVENT_CLOSE_BUG');

	 	plugin_event_hook( 'EVENT_VIEW_BUG_EXTRA', 'tasks_form1' );
//		plugin_event_hook( 'EVENT_VIEW_BUG_DETAILS2', 'tasks_form1' );
		plugin_event_hook( 'EVENT_REPORT_BUG', 'CreateTasks' );
		plugin_event_hook( 'EVENT_UPDATE_BUG', 'CreateTasks2' );
		plugin_event_hook( 'EVENT_RESOLVE_BUG', 'CheckTasks' );
		plugin_event_hook( 'EVENT_CLOSE_BUG', 'CheckTasks' );
		plugin_event_hook( 'EVENT_BUG_DELETED', 'DeleteTasks' );
		$showmenu =  plugin_config_get( 'tasks_show_menu' );
		if (ON === $showmenu){
			plugin_event_hook( 'EVENT_MENU_MAIN', 'tasks_menu1' );
		} else {
			plugin_event_hook( 'EVENT_MYVIEW', 'tasks_view' );
		}
		plugin_event_hook( 'EVENT_MENU_MANAGE', 'tasks_menu2' );
		
	}

	function schema() {
		return array(
			array( 'CreateTableSQL', array( plugin_table( 'autodefined' ), "
						autotask_id			I       NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
						project_id			I       DEFAULT NULL,
						category_id			I       DEFAULT NULL,
						autotask_handler	I		DEFAULT NULL,
						autotaskcat_id		I		DEFAULT NULL,
						autotask_title		C(50)	,
						autotask_desc		C(250)	,
						autotask_due		I		DEFAULT NULL,
						autotask_status		I2		NOTNULL DEFAULT '10'
					" ) ),
			array( 'CreateTableSQL', array( plugin_table( 'defined' ), "
						task_id				I       NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
						bug_id				I       DEFAULT NULL,
						task_user			I       DEFAULT NULL,
						task_handler		I		DEFAULT NULL,
						taskcat_id			I		DEFAULT NULL,
						task_title			C(250)	,
						task_desc			C(250)	,
						task_response		C(250)	,
						task_created		T		DEFAULT NULL,
						task_changed		T		DEFAULT NULL,
						task_due			T		NOTNULL DEFAULT '0000-00-00 00:00:00',
						task_completed		T		NOTNULL DEFAULT '0000-00-00 00:00:00',
						task_completed_by	I		DEFAULT NULL,
						task_group			I		DEFAULT NULL,
						task_time			D(15,2)	DEFAULT NULL
						" ) ),
			array( 'CreateTableSQL', array( plugin_table( 'cat' ), "
						taskcat_id			I		NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
						project_id			I		DEFAULT NULL,
						taskcat_title		C(50)	
						" ) ),
		);
	}
	
	function config() {
		return array(
			'tasks_delete_threshold'	=> ADMINISTRATOR,
			'tasks_edit_threshold'		=> ADMINISTRATOR,
			'tasks_finish_threshold'	=> DEVELOPER,
			'tasks_update_threshold'	=> DEVELOPER,
			'tasks_add_threshold'		=> DEVELOPER,
			'tasks_allocate_threshold'	=> DEVELOPER,
			'tasks_view_threshold'		=> VIEWER,
			'tasks_admin_threshold'		=> ADMINISTRATOR,
			'tasks_mail'				=> OFF,
			'tasks_history'				=> OFF,
			'tasks_mail_finish'			=> OFF,
			'tasks_show_menu'			=> ON,
			'tasks_check'				=> OFF,
			'tasks_auto_start_status'	=> NEW_,
			'tasks_update_bug'			=> ON,
			'tasks_assign_group'		=> OFF,
			'tasks_hour_rate'			=> '50',
			);
	}

 	function tasks_form1() {
		 include 'plugins/Tasks/pages/tasks_form.php';
	}

 	function tasks_menu1() {
		$links = array();
		$links[] = array(
		'title' => lang_get("plugin_tasks_mytasks"),
		'url' => plugin_page("my_tasks.php", true)
		);
		return $links;
	}

	function tasks_menu2() {
 		 return array('<a href="'. plugin_page( 'all_tasks.php' ) . '">' . lang_get( 'plugin_tasks_alltasks' ) . '</a>' );
	}

 	function tasks_view() {
 		 include 'plugins/Tasks/pages/myview_tasks.php';
	}

	function CreateTasks($p_event,$p_bug_data,$p_bug_id) {
		$project			= $p_bug_data->project_id ;
		$category			= $p_bug_data->category_id ;
		$status				= $p_bug_data->status ;
		// do we have tasks for this project/category/status combination ??
		$query		= "select * from {plugin_Tasks_autodefined} where project_id=$project and category_id=$category and autotask_status = $status";
		$result		= db_query($query);
		$count		= db_num_rows($result) ;

		// if not, do we have tasks for this project/Status ??
		if ($count <1){
				$query		="select * from {plugin_Tasks_autodefined} where project_id=$project and category_id=0 and autotask_status = $status";
				$result		= db_query($query);
				$count		= db_num_rows($result) ;
				// if not, do we have tasks in general for this status ??
				if ($count <1){
					$query		="select * from {plugin_Tasks_autodefined} where project_id=0 and category_id=0 and autotask_status = $status";
					$result		= db_query($query);
					$count		= db_num_rows($result) ;
				}
		}
		if ($count >0){
			# $user = admin account
			$user=1;
			# retrieve bug_id
			$bug_id = $p_bug_id;
			# should event be logged in the project
			$create_his		= config_get( 'plugin_Tasks_tasks_history' );
			# should mail be send to assignee
			$create_mail	= config_get( 'plugin_Tasks_tasks_mail' );
			// Create tasks
			while ($row = db_fetch_array($result)) {
				// step 0 sanitize data
				$desc		= htmlentities(html_entity_decode($row['autotask_desc'] ),ENT_COMPAT,'UTF-8');
				$save_desc = db_prepare_string($desc);
				$title		= htmlentities(html_entity_decode($row['autotask_title']),ENT_COMPAT,'UTF-8');
				// step 1 do we already have this task for this issue
				$sql = "select * from  {plugin_Tasks_defined} where bug_id =$bug_id and task_title ='$title' ";
				$result_sql		= db_query($sql);
				$count_sql		= db_num_rows($result_sql) ;
				if ($count_sql>0){
					continue;
				}
				// step2 calculate duedate
				$days= $row['autotask_due'];
				$bookdate = mktime(0, 0, 0, date('m'),date('d'),date('Y'));
				$i = 1;
				while ($i <= $days) {
					$bookdate += 86400; // Add a day.
					$date_info  = getdate( $bookdate );
					if (($date_info["wday"] == 0) or ($date_info["wday"] == 6) )  {
						$bookdate += 86400; // Add a day.
						continue;
					}
					$i++;
				}
				$bookdate2  = date("Y", $bookdate);
				$bookdate2 .= "-";
				$bookdate2 .= date("m", $bookdate);
				$bookdate2 .= "-";
				$bookdate2 .= date("d", $bookdate);
				// save the task
				$handler=$row['autotask_handler'];
				$taskcat_id=$row['autotaskcat_id'];
				$query = "INSERT INTO {plugin_Tasks_defined} ( bug_id,task_user, task_handler,taskcat_id,task_title,task_desc,task_created,task_due,task_changed )
					VALUES (  '$bug_id',$user, '$handler','$taskcat_id', '$title', '$save_desc',  NOW(), '$bookdate2', NOW())";
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
					$body .= $bookdate2;
					$resultmail = email_bug_reminder( $handler,$bug_id, $body );
				}
			}
		}
	}

function CreateTasks2($p_event,$p_bug_data,$p_bug_data2) {
		$project			= $p_bug_data2->project_id ;
		$category			= $p_bug_data2->category_id ;
		$status				= $p_bug_data2->status ;
		$p_bug_id			= $p_bug_data2->id ;
		// do we have tasks for this project/category/status combination ??
		$query		= "select * from {plugin_Tasks_autodefined} where project_id=$project and category_id=$category and autotask_status = $status";
		$result		= db_query($query);
		$count		= db_num_rows($result) ;

		// if not, do we have tasks for this project/Status ??
		if ($count <1){
				$query		="select * from {plugin_Tasks_autodefined} where project_id=$project and category_id=0 and autotask_status = $status";
				$result		= db_query($query);
				$count		= db_num_rows($result) ;
				// if not, do we have tasks in general for this status ??
				if ($count <1){
					$query		="select * from {plugin_Tasks_autodefined} where project_id=0 and category_id=0 and autotask_status = $status";
					$result		= db_query($query);
					$count		= db_num_rows($result) ;
				}
		}
		if ($count >0){
			# $user = admin account
			$user=1;
			# retrieve bug_id
			$bug_id = $p_bug_id;
			# should event be logged in the project
			$create_his		= config_get( 'plugin_Tasks_tasks_history' );
			# should mail be send to assignee
			$create_mail	= config_get( 'plugin_Tasks_tasks_mail' );
			// Create tasks
			while ($row = db_fetch_array($result)) {
				// step 0 sanitize data
				$desc		= htmlentities(html_entity_decode($row['autotask_desc'] ),ENT_COMPAT,'UTF-8');
				$save_desc = db_prepare_string($desc);
				$title		= htmlentities(html_entity_decode($row['autotask_title']),ENT_COMPAT,'UTF-8');
				// step 1 do we already have this task for this issue
				$sql = "select * from  {plugin_Tasks_defined} where bug_id =$bug_id and task_title ='$title' ";
				$result_sql		= db_query($sql);
				$count_sql		= db_num_rows($result_sql) ;
				if ($count_sql>0){
					continue;
				}
				// step2 calculate duedate
				$days= $row['autotask_due'];
				$bookdate = mktime(0, 0, 0, date('m'),date('d'),date('Y'));
				$i = 1;
				while ($i <= $days) {
					$bookdate += 86400; // Add a day.
					$date_info  = getdate( $bookdate );
					if (($date_info["wday"] == 0) or ($date_info["wday"] == 6) )  {
						$bookdate += 86400; // Add a day.
						continue;
					}
					$i++;
				}
				$bookdate2  = date("Y", $bookdate);
				$bookdate2 .= "-";
				$bookdate2 .= date("m", $bookdate);
				$bookdate2 .= "-";
				$bookdate2 .= date("d", $bookdate);
				// save the task
				$handler=$row['autotask_handler'];
				$taskcat_id=$row['taskcat_id'];
				$query = "INSERT INTO {plugin_Tasks_defined} ( bug_id,task_user, task_handler,taskcat_id,task_title,task_desc,task_created,task_due,task_changed )
					VALUES (  '$bug_id',$user, '$handler','$taskcat_id', '$title', '$save_desc',  NOW(), '$bookdate2', NOW())";
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
					$body .= $bookdate2;
					$resultmail = email_bug_reminder( $handler,$bug_id, $body );
				}
			}
		}
	}
	function CheckTasks($p_event,$p_bug_id) {
		$checktasks =  plugin_config_get( 'tasks_check' );
		if (ON == $checktasks){
			$query = "select * from {plugin_Tasks_defined} where bug_id=$p_bug_id and task_completed = '0000-00-00 00:00:00' " ;
			$results = db_query($query) ;
			$count= db_num_rows($results);
			if ($count>0){
				trigger_error( 'ERROR_BUG_OPEN_TASKS', ERROR );
			}
		}
		return;
	}
	
	function DeleteTasks($p_event,$p_bug_id) {
		$query = "delete from {plugin_Tasks_defined} where bug_id=$p_bug_id" ;
		$results = db_query($query) ;
		return;
	}


}
