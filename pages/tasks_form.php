<?php
########################################################
# Mantis Bugtracker Plugin Tasks
#
# By Cas Nuy  www.nuy.info 2020
# To be used with Mantis 2.x and above
#
########################################################
$user 			= auth_get_current_user_id();
$bug_id			= gpc_get_int( 'id' );

# what is the table for tasks
$tasks_table	= plugin_table('defined');
$taskscat_table	= plugin_table('cat');

# do we allow to allocate to group?
$use_groups		= config_get( 'plugin_Tasks_tasks_assign_group' );
$grp_table	= plugin_table('groups','Usergroups');
$ugrp_table	= plugin_table('usergroup','Usergroups');
require_once( config_get( 'plugin_path' ) . 'Tasks' . DIRECTORY_SEPARATOR . 'Tasks_api.php' );

?>
<div class="col-md-12 col-xs-12">
<div class="space-10"></div>
<div class="form-container" > 
<tr>
<td class="center" colspan="6">
<?php
$colspan=6;
?>
<tr>
<td colspan="<?php echo $colspan ?>" class="row-category"><div align="left"><a name="taskrecord"></a>
</div>
</td>
</tr>
<?php
if ( access_has_bug_level(plugin_config_get( 'tasks_add_threshold' ), $bug_id ) ) {
?>
	<form name="taskadding" method="post" action="plugins/Tasks/pages/task_action_add.php">
	<input type="hidden" name="bug_id" value="<?php echo $bug_id;  ?>">
	<input type="hidden" name="id" value="<?php echo $bug_id;  ?>">
	<input type="hidden" name="user" value="<?php echo $user;  ?>">
	<input type="hidden" name="tasks_table" value="<?php echo $tasks_table;  ?>">
<div class="widget-box widget-color-blue2">
<div class="widget-header widget-header-small">
	<h4 class="widget-title lighter">
		<i class="ace-icon fa fa-text-width"></i>
		<?php echo lang_get( 'tasks' ) . ': ' ?>
	</h4>
</div>
<div class="widget-body">
<div class="widget-main no-padding">
<div class="table-responsive"> 
<table class="table table-bordered table-condensed table-striped"> 	
<tr class="row-category">
<td><div align="center"><?php echo lang_get( 'task_id' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'task_title' ); ?>/<?php echo lang_get( 'task_category' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'task_handler' ); ?>/<?php echo lang_get( 'task_due' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'task_desc' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'task_response' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'task_completed' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'task_actions' ); ?></div></td>
<td>&nbsp;</td>
</tr>

    <tr  >
	<td></td>
    <td nowrap><div align="left">
    <input name="task_title" type="text" size=26 maxlength=50 >
    <br><br>
	<?php
	// create select list based upon project
	// first see if there are project related categories
	// if not, take the general ones
	?>
	<select <?php echo helper_get_tab_index() ?> name="cat_id"  STYLE="width: 200px">
	<?php
	$project =  helper_get_current_project();
	$query="select * from $taskscat_table where project_id=$project order by taskcat_title" ;
	$result = db_query($query);
	$res2=db_num_rows($result);
	if ($res2 == 0){
		$project=0;
		$query="select * from $taskscat_table where project_id=$project order by taskcat_title";
		$result = db_query($query);
	} else {
		$t_id =  0;
		$t_name= ' ';
		echo '<option value="' . $t_id . '"';
		echo '>' . $t_name . '</option>' . "\n";
	}
	while ($row = db_fetch_array($result)) {
			$t_id = $row['taskcat_id'];
			$t_name = $row['taskcat_title'];
			echo '<option value="' . $t_id . '"';
			echo '>' . $t_name . '</option>' . "\n";
	}
	?>
	</select>
    </div>
    </td>

	<td><div align="left">
	<?php
	if ( ON == $use_groups ) {
		echo '<select name="task_group">';
		$sql9 = "select distinct a.group_id,group_name from $grp_table a,$ugrp_table b where a.group_id=b.group_id order by group_name";
		$result9 = db_query($sql9);
		echo '<option value="0"';
		echo '> </option>' . "\n";
		while ($row9 = db_fetch_array($result9)) {
			$t_id = $row9['group_id'];
			$t_name = $row9['group_name'];
			echo '<option value="' . $t_id . '"';
			echo '>' . $t_name . '</option>' . "\n";
		}
		echo '</select>';

	}
	echo '<br>';
	echo '<select name="task_handler">';
	echo '<option value="0"';
	echo '> </option>' . "\n";
	$handler=0;
	print_assign_to_option_list( $handler, $project ,plugin_config_get( 'tasks_allocate_threshold' ));
	echo '</select>';
	?>
    <br><br>

	<?php
	$t_date_to_display = ''; 
	echo '<input ' . helper_get_tab_index() . ' type="text" id="task_due" name="task_due" class="datetimepicker input-sm" ' .
				'data-picker-locale="' . lang_get_current_datetime_locale() .
				'" data-picker-format="' . config_get( 'datetime_picker_format' ) . '" ' .
				'size="20" maxlength="16" value="' . $t_date_to_display . '" />' ?>
			<i class="fa fa-calendar fa-xlg datetimepicker"></i> 
	</td>

    <td><div align="left">
    <textarea name="task_desc" rows="5" cols="30"></textarea>
    </div>

	<td></td>	<td></td>

	<td><input name="<?php echo lang_get( 'task_submit' ) ?>" type="submit" value="<?php echo lang_get( 'task_submit' ) ?>">
	</td>
	<td>	</td>
	</tr>
	</form>
<?php
}
if ( access_has_bug_level( plugin_config_get( 'tasks_view_threshold' ), $bug_id ) ) {
	# Pull all Tasks-Record entries for the current Bug
	$query = "SELECT a.*,b.taskcat_title FROM $tasks_table as a,$taskscat_table as b  WHERE a.taskcat_id=b.taskcat_id and  bug_id = $bug_id ORDER BY taskcat_title";

	$result = db_query($query);
	while ($row = db_fetch_array($result)) {
		?>
		<tr  >
		<td><div align="left">
		<?PHP
		echo $row["task_id"];
		?>
		</div></td>

		<td><div align="center"><?php  echo html_entity_decode($row["task_title"]); ?>
		<br>
		<?PHP		echo html_entity_decode($row["taskcat_title"]);	?>
		</div></td>
		<td>
		<?php
		if ($row['task_handler']>0){
			echo user_get_name($row["task_handler"]);
		} else {
			echo group_get_name($row["task_group"]);
		}
		?>
		<br>
				<?php  echo date("Y.m.d", strtotime($row["task_due"])); ?>
		</td>
		<td><div align="left">
		<?PHP
		echo html_entity_decode($row["task_desc"]);
		?>
		</div></td>
		<td><div align="center">
		<?PHP
		echo html_entity_decode($row["task_response"]);
		?>
		</div></td>
		<td><div align="center"><?php
		if ( $row['task_completed'] <> "0000-00-00 00:00:00"){
			echo date("Y.m.d", strtotime($row["task_completed"]));
		}
		?> </div></td>
		<td><div>
		<?php
		if ( $row['task_completed'] === "0000-00-00 00:00:00"){
			if ( access_has_bug_level( plugin_config_get( 'tasks_update_threshold' ), $bug_id ) OR ($row["task_handler"] == $user)) {
				?>
				<a href="#" onclick="window.open('plugins/Tasks/pages/task_action_update.php?update_id=<?php echo $row["task_id"]; ?>&id=<?php echo $bug_id;?>&time=<?php echo $row['task_time'];?>', 'TaskUpdate', 'width=800, height=500'); return false;"><?php echo lang_get( 'task_update' ) ?></a><br>

				<?php
			}
			if ( access_has_bug_level( plugin_config_get( 'tasks_finish_threshold' ), $bug_id ) OR ($row["task_handler"] == $user)) {?>
				<a href="plugins/Tasks/pages/task_action_finish.php?finish_id=<?php echo $row["task_id"]; ?>&id=<?php echo $bug_id;?>"><?php echo lang_get( 'task_finish' ) ?></a><br>
				<?php
			} 
			if ( access_has_bug_level( plugin_config_get( 'tasks_edit_threshold' ), $bug_id ) OR ($row["task_user"] == $user) ) {
				?>
				<a href="#" onclick="window.open('plugins/Tasks/pages/task_action_edit.php?edit_id=<?php echo $row["task_id"]; ?>&response=<?php echo $row['task_response']; ?>&id=<?php echo $bug_id;?>', 'TaskEdit', 'width=800, height=600'); return false;"><?php echo lang_get( 'task_edit' ) ?></a><br>
				<?php
			}
			if ( access_has_bug_level( plugin_config_get( 'tasks_delete_threshold' ), $bug_id ) ) {?>
				<a href="plugins/Tasks/pages/task_action_delete.php?delete_id=<?php echo $row["task_id"]; ?>&id=<?php echo $bug_id;?>"><?php echo lang_get( 'task_delete' ) ?></a>
				<?php
			}
		}
		?>
		</div></td>
		<td></td>
		</tr>
		<?php
	}
}
?>
</table>
</div>
</div>
</div>
</div>
</form>
</div>
</div></td>
</tr>
