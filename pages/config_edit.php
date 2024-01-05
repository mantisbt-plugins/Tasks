<?php
// authenticate
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );
// Read results
$f_tasks_admin_threshold = gpc_get_int( 'tasks_admin_threshold', [ADMINISTRATOR] );
$f_tasks_add_threshold = gpc_get_int( 'tasks_add_threshold', [DEVELOPER] );
$f_tasks_allocate_threshold = gpc_get_int( 'tasks_allocate_threshold', [DEVELOPER] );
$f_tasks_view_threshold = gpc_get_int( 'tasks_view_threshold', [VIEWER] );
$f_tasks_delete_threshold = gpc_get_int( 'tasks_delete_threshold', [DEVELOPER] );
$f_tasks_edit_threshold = gpc_get_int( 'tasks_edit_threshold', [DEVELOPER] );
$f_tasks_update_threshold = gpc_get_int( 'tasks_update_threshold', [DEVELOPER] );
$f_tasks_finish_threshold = gpc_get_int( 'tasks_finish_threshold', [DEVELOPER] );
$f_tasks_mail = gpc_get_int( 'tasks_mail', OFF );
$f_tasks_mail_finish = gpc_get_int( 'tasks_mail_finish', OFF );
$f_tasks_history = gpc_get_int( 'tasks_history', OFF );
$f_tasks_check = gpc_get_int( 'tasks_check', OFF );
$f_tasks_show_menu = gpc_get_int( 'tasks_show_menu', ON );
$f_tasks_update_bug = gpc_get_int( 'tasks_update_bug', ON );
$f_tasks_assign_group = gpc_get_int( 'tasks_assign_group', OFF );
$f_tasks_hour_rate = gpc_get_string( 'tasks_hour_rate', '50' );
// Cleaning needed?
$f_tasks_clean = gpc_get_int( 'tasks_clean', OFF );

// update results
plugin_config_set( 'tasks_admin_threshold', $f_tasks_admin_threshold );
plugin_config_set( 'tasks_add_threshold', $f_tasks_add_threshold );
plugin_config_set( 'tasks_allocate_threshold', $f_tasks_allocate_threshold );
plugin_config_set( 'tasks_view_threshold', $f_tasks_view_threshold );
plugin_config_set( 'tasks_delete_threshold', $f_tasks_delete_threshold );
plugin_config_set( 'tasks_edit_threshold', $f_tasks_edit_threshold );
plugin_config_set( 'tasks_update_threshold', $f_tasks_update_threshold );
plugin_config_set( 'tasks_finish_threshold', $f_tasks_finish_threshold );
plugin_config_set( 'tasks_history', $f_tasks_history );
plugin_config_set( 'tasks_mail', $f_tasks_mail );
plugin_config_set( 'tasks_mail_finish', $f_tasks_mail_finish );
plugin_config_set( 'tasks_show_menu', $f_tasks_show_menu );
plugin_config_set( 'tasks_check', $f_tasks_check );
plugin_config_set( 'tasks_update_bug', $f_tasks_update_bug );
plugin_config_set( 'tasks_assign_group', $f_tasks_assign_group );
plugin_config_set( 'tasks_hour_rate', $f_tasks_hour_rate );

// do we need to clean defined Tasks
if ( ON === $f_tasks_clean ) {
	// Remove orphaned tasks
	$sql = "delete from {plugin_Tasks_defined} where not exists(select * from {bug} where id=bug_id)";
	$result = db_query($sql) ;

	// Remove tasks without category
	$sql = "delete from {plugin_Tasks_defined} where taskcat_id =0";
	$result = db_query($sql);
}

// redirect
print_header_redirect( plugin_page( 'config',TRUE ) );