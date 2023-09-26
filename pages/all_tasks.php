<?php	
########################################################
# Mantis Bugtracker Plugin Tasks
#
# By Cas Nuy  www.nuy.info 2020
# To be used with Mantis 2.x and above
#
########################################
layout_page_header(  );
layout_page_begin(  );
print_manage_menu();
$excelpage = plugin_page('all_tasks_xls.php');
require_once( config_get( 'plugin_path' ) . 'Tasks' . DIRECTORY_SEPARATOR . 'Tasks_api.php' );  
$colspan=6;
?>
<div class="col-md-12 col-xs-12">
<div class="space-10"></div>
<div class="form-container" > 
<br/>
<div class="widget-box widget-color-blue2">
<div class="widget-header widget-header-small">
	<h4 class="widget-title lighter">
		<i class="ace-icon fa fa-text-width"></i>
		<?php echo lang_get( 'alltasks' )?>
	</h4>
</div>
<div class="widget-body">
<div class="widget-main no-padding">
<div class="table-responsive"> 
<table class="table table-bordered table-condensed table-striped"> 
<tr>
<td class="center" colspan="6">
<br>
<td colspan="<?php echo $colspan ?>" class="row-category"><div align="left"><a name="taskrecord"></a>
<a href=<?PHP echo $excelpage ?>><?php echo lang_get('tasks_xls') ?></a> 

</div>
</td>
</tr>
<tr class="row-category">
<td><div align="center"><?php echo lang_get( 'task_id' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'task_bug' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'task_title' ); ?>/<?php echo lang_get( 'task_category' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'task_handler' ); ?>/<?php echo lang_get( 'task_due' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'task_desc' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'task_response' ); ?></div></td> 
<td><div align="center"><?php echo lang_get( 'task_actions' ); ?></div></td>
</tr>
<?php
# Pull all Tasks-Record entries for the current user
$query = "SELECT a.*,b.taskcat_title FROM {plugin_Tasks_defined} as a,{plugin_Tasks_cat} as b  WHERE a.taskcat_id=b.taskcat_id and task_completed = '0000-00-00 00:00:00' and task_handler>0 ORDER BY task_handler,task_due ASC";
$result = db_query($query);
while ($row = db_fetch_array($result)) {
	?>
	<tr >
	<td><div align="center">
	<?PHP
	echo ($row["task_id"]);
	?>
	</div></td>
	<td><div align="center">
	<?PHP
	echo ($row["bug_id"]);
	?>
	</div></td>
	<td><div align="center"><?php  echo html_entity_decode($row["task_title"]); ?>
	<br>
	<?php  echo $row["taskcat_title"]; ?></div></td>
	<td><div align="center"><?php echo user_get_name($row["task_handler"]); ?>
		<br>
	<?php  echo date("d.m.Y", strtotime($row["task_due"])); ?></div>
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
	<td><div align="center">
	<a href="view.php?id=<?php echo $row['bug_id'];?>"><?php echo lang_get( 'issue_task' ) ?></a><br>
	</div></td>
	</tr>
	<?php
}	 
$use_groups		= config_get( 'plugin_Tasks_tasks_assign_group' );
if ( ON == $use_groups){
$query = "SELECT a.*,b.taskcat_title FROM {plugin_Tasks_defined} as a,{plugin_Tasks_cat} as b  WHERE a.taskcat_id=b.taskcat_id and task_completed = '0000-00-00 00:00:00' and task_group>0 ORDER BY task_group,task_due ASC";
$result = db_query($query);
while ($row = db_fetch_array($result)) {
	?>
	<tr >
	<td><div align="center">
	<?PHP
	echo ($row["task_id"]);
	?>
	</div></td>
	<td><div align="center">
	<?PHP
	echo ($row["bug_id"]);
	?>
	</div></td>
	<td><div align="center"><?php  echo html_entity_decode($row["task_title"]); ?>
	<br>
	<?php  echo $row["taskcat_title"]; ?></div></td>
	<td><div align="center"><?php echo group_get_name($row["task_group"]); ?>
		<br>
	<?php  echo date("d.m.Y", strtotime($row["task_due"])); ?></div>
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
	<td><div align="center">
	<a href="view.php?id=<?php echo $row['bug_id'];?>"><?php echo lang_get( 'issue_task' ) ?></a><br>
	</div></td>
	</tr>
	<?php
}	
} 
?>
</table></div></div></div></div>
</td>
</tr>
<?php
layout_page_end();
