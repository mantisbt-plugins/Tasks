<?PHP
$project = $_REQUEST['project'];
$title		= htmlentities($_REQUEST['taskcat_title'],ENT_COMPAT,'UTF-8');
// first check if entry already exists
$query= "select * from {plugin_Tasks_cat} where project_id='$project' and upper(trim(taskcat_title))=upper(trim('$title'))";
$result = db_query($query);
$res2=db_num_rows($result);
if ($res2 === 0){
	$query = "INSERT INTO {plugin_Tasks_cat} ( project_id,taskcat_title ) VALUES ( '$project', '$title' )";
	if(!db_query($query)){
	trigger_error( 'ERROR_DB_QUERY_FAILED', ERROR );
}
}
print_header_redirect( 'plugin.php?page=Tasks/task_categories' );