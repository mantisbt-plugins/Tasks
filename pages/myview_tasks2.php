<?php	
########################################################
# Mantis Bugtracker Plugin Tasks
#
# By Cas Nuy  www.nuy.info 2020
# To be used with Mantis 2.x and above
#
########################################################
$user 			= auth_get_current_user_id();
$query = "SELECT * FROM {plugin_Tasks_defined} WHERE task_handler = $user  and task_completed = '0000-00-00 00:00:00' ORDER BY task_due ASC";
$result = db_query($query);
$t_count=db_num_rows($result);
require_once( config_get( 'plugin_path' ) . 'Tasks' . DIRECTORY_SEPARATOR . 'Tasks_api.php' );  
$use_groups		= config_get( 'plugin_Tasks_tasks_assign_group' );
if ( ON == $use_groups){
	$query2 = "SELECT * FROM {plugin_Tasks_defined},{plugin_Usergroups_usergroup}  WHERE task_group= group_id and user_id = $user  and task_completed = '0000-00-00 00:00:00' ORDER BY task_due ASC";
	$result2 = db_query($query2);
	$t_count2=db_num_rows($result2);

} else {
	$t_count2=0;
}
if (($t_count>0) or ($t_count2>0) ){
?>

<div class="col-md-12 col-xs-12">
<div class="space-10"></div>
<div class="form-container" > 
<div class="widget-box widget-color-blue2">
<div class="widget-header widget-header-small">
	<h4 class="widget-title lighter">
		<i class="ace-icon fa fa-text-width"></i>
		<?php echo lang_get( 'mytasks' )?>
	</h4>
</div>
<div class="widget-body">
<div class="widget-main no-padding">
<div class="table-responsive"> 
<table class="table table-bordered table-condensed table-striped"> 
<tr>
<td class="center" colspan="6">
</div>
</td>
</tr>
<tr class="row-category">
<b>
<td><div align="center"><?php echo lang_get( 'task_id' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'task_title' ); ?>/<?php echo lang_get( 'task_due' ); ?></div></td>
<td><div align="left"><?php echo lang_get( 'task_handler' ); ?></div></td>
<td><div align="left"><?php echo lang_get( 'task_desc' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'task_response' ); ?></div></td> 
<td><div align="center"><?php echo lang_get( 'task_actions' ); ?></div></td>
</b>
</tr>
<?php
# Pull all Tasks-Record entries for the current user

while ($row = db_fetch_array($result)) {
	?>
	<tr >
	<td><div align="center">
	<?PHP
 	echo $row["task_id"];
	?>
	</div></td>
	<td><div align="center"><?php  echo html_entity_decode($row["task_title"]); ?>
	<br>
	<?php  echo date("d.m.Y", strtotime($row["task_due"])); ?></div></td>
	<td><?php echo user_get_name($row["task_handler"]); ?></td>
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
	<td><div align="center">
	<a href="view.php?id=<?php echo $row['bug_id'];?>"><?php echo lang_get( 'issue_task' ) ?></a>
	</div></td>
	</tr>
	<?php
}	 
if (($t_count2>0) and check_user_in_group($user)){
while ($row = db_fetch_array($result2)) {
	?>
	<tr >
	<td><div align="center">
	<?PHP
 	echo $row["task_id"];
	?>
	</div></td>
	<td><div align="center"><?php  echo html_entity_decode($row["task_title"]); ?>
	<br>
	<?php  echo date("d.m.Y", strtotime($row["task_due"])); ?></div></td>
	<td><?php echo group_get_name($row["task_group"]); ?></td>
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
	<td><div align="center">
	<a href="view.php?id=<?php echo $row['bug_id'];?>"><?php echo lang_get( 'issue_task' ) ?></a>
	</div></td>
	</tr>
	<?php
}	 
}

?>
</table></div></div></div></div></div>
</td>
</tr>
<?php
}
