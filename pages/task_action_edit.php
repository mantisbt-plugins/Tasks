<?PHP
$reqVar = '_' . $_SERVER['REQUEST_METHOD'];
$form_vars = $$reqVar;
$edit_id = $form_vars['edit_id'] ;
$bug_id		= $form_vars['id'] ;
require_once( '../../../core.php' );


// get current values
$query = "SELECT * FROM {plugin_Tasks_defined} WHERE task_id = $edit_id ";
$result = db_query($query);
$row = db_fetch_array( $result );
$duedate=$row['task_due'];
$handler=$row['task_handler'];
$description=$row['task_desc'];
$current_date = explode ("-", $duedate);
$title=$row['task_title'];
$task_due= $row['task_due'];
$group = $row['task_group'];
$allocate_level = config_get( 'plugin_Tasks_tasks_allocate_threshold' );
# do we allow to allocate to group?
$use_groups		= config_get( 'plugin_Tasks_tasks_assign_group' );

?>
		<?php echo 'Tasks : ' . lang_get( 'task_edit_comments' ). ' => Issue-ID:  '.$bug_id.' => '.$description?>
<form name="taskediting" method="post" action="../../../plugins/Tasks/pages/task_action_edit2.php">
<input type="hidden" name="edit_id" value="<?php echo $edit_id;  ?>">
<input type="hidden" name="id" value="<?php echo $bug_id;  ?>">
<center>
<tr><td>
<?php echo lang_get( 'task_title' ) ?> 
</td>
<td>
<?php echo lang_get( 'task_due' ) ?> 
</td>
<td>
<?php echo lang_get( 'task_handler' ) ?>
</td>
<td>
<?php echo lang_get( 'task_desc' ) ?>
</td>
</tr>
<tr>
<td>
<textarea name="task_title" rows="3" cols="50"><?php echo $title;  ?></textarea>
</td>
<td>

<?php
	$t_date_to_display = $task_due; 

/*	echo '<input ' . helper_get_tab_index() . ' type="text" id="task_due1" name="task_due" class="datetimepicker input-sm" ' .
			'data-picker-locale="' . lang_get_current_datetime_locale() .
				'" data-picker-format="' . config_get( 'datetime_picker_format' ) . '" ' .
				'size="8" maxlength="16" value="' . $t_date_to_display . '" />' ?>
			<i class="fa fa-calendar fa-xlg datetimepicker"></i> 
*/
	?>
<input type="text" id="task_due1" name="task_due1" class="datetimepicker input-sm"
						data-picker-locale="<?php echo lang_get_current_datetime_locale() ?>"
						data-picker-format="<?php echo config_get( 'datetime_picker_format' ) ?>"
						size="8" value="<?php echo $t_date_to_display ?>" />
					<i class="fa fa-calendar fa-xlg datetimepicker"></i>

</td>
<td>
<?php

if ( ON == $use_groups ) {
	echo '<select name="task_group">';
	$sql9 = "select distinct a.group_id,group_name from {plugin_Usergroups_groups} a,{plugin_Usergroups_usergroup} b where a.group_id=b.group_id order by group_name"; 
	$result9 = db_query($sql9);
	echo '<option value="0"';
	echo '> </option>' . "\n"; 
	while ($row9 = db_fetch_array($result9)) {	
		$t_id = $row9['group_id'];
		$t_name = $row9['group_name'];
		echo '<option value="' . $t_id . '"';
		check_selected( $group, $t_id ); 
		echo '>' . $t_name . '</option>' . "\n"; 
	}
	echo '</select>';
	echo '<br><br>';

} 
//$project_id=0;
$handler=intval($handler);
echo '<select name="task_handler">';
echo '<option value="0"';
echo '> </option>' . "\n"; 
print_assign_to_option_list( $handler, $project_id ,$allocate_level);
echo '</select>';
?>
</td>
<td>
<textarea name="task_desc" rows="5" cols="30"><?php echo $description;  ?></textarea>
</td>
</tr><tr>
<td><input name="Update" type="submit" value="Update"></td>
<td><input type="button" value="Cancel" onclick="self.close()"></td>
</tr>
</center>
</form>
