<?PHP
// require_once( '../../../core.php' );
$reqVar		= '_' . $_SERVER['REQUEST_METHOD'];
$form_vars	= $$reqVar;
$update_id	= $form_vars['update_id'] ;
$id			= $form_vars['id'] ;
$query 		= "SELECT * FROM {plugin_Tasks_defined} WHERE task_id = $update_id ";
$result 	= db_query($query);
$row 		= db_fetch_array( $result );
$response	= $row['task_response'] ;
$desc 		= $row['task_desc'];
$user 		= auth_get_current_user_id();
$time		= $row['task_time'] ;
layout_page_header();
layout_page_begin();
?>
<center>		<?php echo 'Tasks : ' . lang_get( 'task_update_comments' ). ' => Issue-ID:  '.$id.' => '.$desc?></center>
<form name="taskupdating" method="post" action="plugin.php?page=Tasks/task_action_update2.php">
<input type="hidden" name="update_id" value="<?php echo $update_id;  ?>">
<input type="hidden" name="id" value="<?php echo $id;  ?>">
<td><div align="center">
<textarea name="task_response" rows="12" cols="80"><?php echo $response;  ?></textarea>
</td>
<br><br><br>
<?php echo lang_get('task_time'); ?>
<br>
<input type="text" size="10" maxlength="10" name="task_time" value="<?php echo $time; ?>">
</div>
<center>
<?php
if (access_has_global_level( config_get( 'plugin_Tasks_tasks_finish_threshold' )) OR ($row["task_handler"] == $user) ){ 
	?>
	<br><br>
	</tr>
	<input type="checkbox" name="Action" value="Close" /> <?php echo lang_get( 'task_finish' ) ?>
	<tr><br><br>
	<?php
}
?>
<input type="checkbox" name="Stop" value="Update" /> <?php echo lang_get( 'task_update' ) ?>
&nbsp;&nbsp;&nbsp;
<td><input name="Update" type="submit" value="Continue"></td>
</tr>
</form>
<?PHP
layout_page_end();
