<?PHP
$reqVar = '_' . $_SERVER['REQUEST_METHOD'];
$form_vars = $$reqVar;
$delete_id = $form_vars['delete_id'] ;
# Deleting autotask
$query = "DELETE FROM {plugin_Tasks_autodefined} WHERE autotask_id = $delete_id";        
db_query($query);
print_header_redirect( 'plugin.php?page=Tasks/task_definition' );
