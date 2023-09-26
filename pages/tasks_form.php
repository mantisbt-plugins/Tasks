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
$t_date_format = config_get( 'normal_date_format' );
# do we allow to allocate to group?
$use_groups		= config_get( 'plugin_Tasks_tasks_assign_group' );
require_once( config_get( 'plugin_path' ) . 'Tasks' . DIRECTORY_SEPARATOR . 'Tasks_api.php' );
 $js = '/plugin_file.php?file=Tasks/tasks.js';
 echo <<<RESOURCES
  <script type="text/javascript" src="{$js}"></script>
 RESOURCES;
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
	<form name="taskadding" method="post" action="plugin.php?page=Tasks/task_action_add.php">
	<input type="hidden" name="bug_id" value="<?php echo $bug_id;  ?>">
	<input type="hidden" name="id" value="<?php echo $bug_id;  ?>">
	<input type="hidden" name="user" value="<?php echo $user;  ?>">
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
	$query="select * from {plugin_Tasks_cat} where project_id=$project order by taskcat_title" ;
	$result = db_query($query);
	$res2=db_num_rows($result);
	if ($res2 == 0){
		$project=0;
		$query="select * from {plugin_Tasks_cat} where project_id=$project order by taskcat_title";
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
		$sql9 = "select distinct a.group_id,group_name from {plugin_Usergroups_groups} a,{plugin_Usergroups_usergroup} b where a.group_id=b.group_id order by group_name";
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
	$t_date_to_display = date($t_date_format); 
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
	$query = "SELECT a.*,b.taskcat_title FROM {plugin_Tasks_defined} as a,{plugin_Tasks_cat} as b  WHERE a.taskcat_id=b.taskcat_id and  bug_id = $bug_id ORDER BY taskcat_title";

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
				<?php  echo date("$t_date_format", strtotime($row["task_due"])); ?>
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
				<a href="#" class="task_update_action" data-taskid="<?= $row["task_id"]?>" data-id="<?= $bug_id;?>" data-time="<?= $row['task_time'];?>"><?php echo lang_get( 'task_update' ) ?></a><br>

				<?php
			}
			if ( access_has_bug_level( plugin_config_get( 'tasks_finish_threshold' ), $bug_id ) OR ($row["task_handler"] == $user)) {?>
				<a href="plugin.php?page=Tasks/task_action_finish.php&finish_id=<?php echo $row["task_id"]; ?>&id=<?php echo $bug_id;?>"><?php echo lang_get( 'task_finish' ) ?></a><br>
				<?php
			} 
			if ( access_has_bug_level( plugin_config_get( 'tasks_edit_threshold' ), $bug_id ) OR ($row["task_user"] == $user) ) {
				?>
				<a href="#" class="task_edit_action" data-taskid="<?= $row["task_id"]?>" data-response="<?= $row['task_response'];?>" data-id="<?= $bug_id;?>"><?php echo lang_get( 'task_edit' ) ?></a><br>
				<?php
			}
			if ( access_has_bug_level( plugin_config_get( 'tasks_delete_threshold' ), $bug_id ) ) {?>
				<a href="plugin.php?page=Tasks/task_action_delete.php&delete_id=<?php echo $row["task_id"]; ?>&id=<?php echo $bug_id;?>"><?php echo lang_get( 'task_delete' ) ?></a>
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
