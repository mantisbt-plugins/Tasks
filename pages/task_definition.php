<?php
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );
layout_page_header( lang_get( 'plugin_format_title' ) );
layout_page_begin( );
print_manage_menu();
$link=plugin_page('config');
$link2=plugin_page('task_categories');
$project =  helper_get_current_project(); 
$pquery1="select name from {project} where id=$project";
$presult1 = db_query($pquery1);
if ($presult1){
	$projectname1 = db_result( $presult1 );
}
?>
<div class="col-md-12 col-xs-12">
<div class="space-10"></div>
<div class="form-container" > 
<form action="<?php echo plugin_page( 'config_edit' ) ?>" method="post">
<div class="widget-box widget-color-blue2">
<div class="widget-header widget-header-small">
	<h4 class="widget-title lighter">
		<i class="ace-icon fa fa-text-width"></i>
		<?php echo lang_get( 'tasks' ) . ': ' . lang_get( 'plugin_format_config' )?>
	</h4>
</div>
<div class="widget-body">
<div class="widget-main no-padding">
<div class="table-responsive"> 
<table class="table table-bordered table-condensed table-striped"> 
<tr>
<td class="form-title" colspan="3">
<a href="<?php echo $link ?>"><?php echo lang_get( 'task_configuration' ) ?></a>
<a href="<?php echo $link2 ?>"><?php echo lang_get( 'task_categories' ) ?></a>
</td>
</tr>
<tr>
<td class="category" colspan="8">
</td>
</tr>
<tr>
<td class="form-title" colspan="8">
<?php echo lang_get( 'plugin_tasks_title' ) . ': ' . lang_get( 'task_definition' ) ?>
</td>
</tr>

<td colspan="8" class="row-category"><div align="left"><a name="taskrecord"></a>
</div>
</td>
</tr>
<tr class="row-category">
<td><div align="center"><?php echo lang_get( 'project' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'category' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'autotask_title' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'tasks_auto_start_status' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'autotask_handler' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'autotask_desc' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'autotask_due' ); ?></div></td> 
<td><div align="center"><?php echo lang_get( 'task_actions' ); ?></div></td>
</tr>
<form name="taskadding2" action="<?php echo plugin_page( 'autotask_action_add' ) ?>" method="post">
<input type="hidden" name="project" value="<?php echo $project;  ?>">
<tr>
<tr <?php echo helper_alternate_class() ?>>
<td>
<?php
echo $projectname1;
?>
</select> 
</td>
<td>
<?PHP
if ($project>0) {
	?>
	<select <?php echo helper_get_tab_index() ?> name="category">
	<?php
	print_category_option_list();
	?>
	</select> 
	<?php
}
?>
</td>

<td nowrap><div align="left">
<input name="autotask_title" type="text" size=26 maxlength=50 >
<br><br>
</td>
<td>
<select name="tasks_auto_start_status">
<?php print_enum_string_option_list( 'status', '10' ) ?>;
</select> 
</td>

<td>
<?php
$handler=0;
echo '<select name="autotask_handler">';\
print_assign_to_option_list( $handler, 0 ,plugin_config_get( 'tasks_allocate_threshold' ));
echo '</select>';
?>
</td>
<td><div align="left">
<textarea name="autotask_desc" rows="5" cols="30"></textarea>
</div>

<td>
<input name="autotask_due" type="text" size=2 maxlength=2 >
</td>
<td><input name="<?php echo lang_get( 'task_submit' ) ?>" type="submit" value="<?php echo lang_get( 'task_submit' ) ?>">
</td>
<td></td>
</tr>
</form>
</div>
</div>
</div>

<?PHP
# Pull all Auto Tasks-Record entries 
$query = "SELECT * FROM {plugin_Tasks_autodefined}  ORDER BY project_id, category_id";
$result = db_query($query);
while ($row = db_fetch_array($result)) {
	$project_id = $row['project_id'];
	$category_id = $row['category_id'];
	$pquery="select name from {project} where id=$project_id";
	$presult = db_query($pquery);
	if ($presult){
		$projectname = db_result( $presult );
	} else {
		$projectname= "--" ;
	}
	$cquery="select name from {category} where id=$category_id";
	$cresult = db_query($cquery);
	if ($cresult){
		$categoryname = db_result( $cresult );
	} else {
		$categoryname= "--" ;
	}
	?>
	<tr <?php echo helper_alternate_class() ?>>
	<td><div align="center">
	<?php echo $projectname; ?>
	</div></td>
	<td><div align="center">
	<?PHP echo $categoryname ; ?>
	</div></td>
	<td><div align="center"><?php  echo html_entity_decode($row["autotask_title"]); ?>
	<br>
	</div></td>
	<td>
	<?PHP
	echo MantisEnum::getLabel( lang_get( 'status_enum_string' ), $row["autotask_status"] ); 	
	?>
	</td>
	<td><?php echo user_get_name($row["autotask_handler"]); ?></td>
	<td><div align="left">
	<?PHP
	echo html_entity_decode($row["autotask_desc"]);		
	?>
	</div></td>
	<td><div align="center">
	<?PHP
	echo $row["autotask_due"];
	?>
	</div></td>
	<td><div>
	<a href="plugins/Tasks/pages/autotask_action_delete.php?delete_id=<?php echo $row["autotask_id"]; ?>"><?php echo lang_get( 'task_delete' ) ?></a>
	</div></td>
	</tr>
	<?php
}	 
?>
</table>
</div>
</div>
</div>
</div>
</form>
</div>
</div>
<?php
layout_page_end( );
