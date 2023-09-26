<?php	
########################################################
# Mantis Bugtracker Plugin Tasks
#
# By Cas Nuy  www.nuy.info 2023
# To be used with Mantis 2.x and above
#
########################################################
layout_page_header( );
layout_page_begin( );
print_manage_menu();
$link=plugin_page('config');
$link2=plugin_page('task_definition');
$colspan=3;
?>
<div class="col-md-12 col-xs-12">
<div class="space-10"></div>
<div class="form-container" > 

<div class="widget-box widget-color-blue2">
<div class="widget-header widget-header-small">
	<h4 class="widget-title lighter">
		<i class="ace-icon fa fa-text-width"></i>
		<?php echo 'Tasks : ' . lang_get( 'task_categories' )?>
	</h4>
</div>
<div class="widget-body">
<div class="widget-main no-padding">
<div class="table-responsive"> 
<table class="table table-bordered table-condensed table-striped">
<tr>
<td class="form-title" colspan="8">
<a href="<?php echo $link ?>"><?php echo lang_get( 'task_configuration' ) ?></a>
<a href="<?php echo $link2 ?>"><?php echo lang_get( 'task_definition' ) ?></a>
</td>
</tr>
<tr>
<td colspan="<?php echo $colspan ?>" class="row-category"><div align="left"><a name="taskrecord"></a>
<?php 
echo lang_get( 'allcategories' ); 
?>
<form name="taskcat2" action="<?php echo plugin_page( 'task_cat_add' ) ?>" method="post">
</div>
</td>
</tr>
<tr class="row-category">
<td><div align="center"><?php echo lang_get( 'project' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'taskcat_title' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'task_actions' ); ?></div></td>
</tr>
<tr>
<td><div align="center">
<select <?php echo helper_get_tab_index() ?> name="project">
<?php
print_project_option_list();
?>
</select> 
</td>
<td><div align="center">
<input name="taskcat_title" type="text" maxlength=50 >
</td>
<td><div align="center">
<input name="<?php echo lang_get( 'taskcat_submit' ) ?>" type="submit" value="<?php echo lang_get( 'taskcat_submit' ) ?>">
</td>
</td>
</tr>
</form>
<?php
# Pull all Tasks-Record entries for the current user
$query = "SELECT * FROM {plugin_Tasks_cat} order by project_id,taskcat_title";
$result = db_query($query);
while ($row = db_fetch_array($result)) {
	$project_id = $row['project_id'];
	$pquery="select name from {project} where id=$project_id";
	$presult = db_query($pquery);
	if ($presult){
		$projectname = db_result( $presult );
	} else {
		$projectname= "--" ;
	}

	?>
	<tr>
	<td><div align="center">
	<?php echo $projectname; ?>
	
	</td>
	<td><div align="center"><?php  echo html_entity_decode($row["taskcat_title"]); ?>
	</td><td><div align="center">
	<a href="plugins/Tasks/pages/task_cat_delete.php?delete_id=<?php echo $row["taskcat_id"]; ?>"><?php echo lang_get( 'task_delete' ) ?></a>
	<===> 
	<a href="plugins/Tasks/pages/task_cat_edit.php?edit_id=<?php echo $row["taskcat_id"]; ?>"><?php echo lang_get( 'task_edit' ) ?></a><br>
	</td>
	</tr>
	<?php
}	 
?>
</td>
</tr>
</table>
</div>
</div></div></div>
<?php
layout_page_end( );
