<?PHP
require_once( '../../../core.php' );
$delete_id = $_REQUEST['delete_id'] ;
// first check if this entry is still in use
$query= "select * from {plugin_Tasks_defined} where taskcat_id= $delete_id";
$result = db_query($query);
$res2=db_num_rows($result);
if ($res2 >0){
	trigger_error( 'ERROR_BUG_TASKS_CAT', ERROR );
} else {
	# Deleting category
	$query = "DELETE FROM {plugin_Tasks_cat} WHERE taskcat_id = $delete_id";        
	db_query($query);
}
print_header_redirect( '../../../plugin.php?page=Tasks/task_categories' );
