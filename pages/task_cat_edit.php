<?PHP
########################################################
# Mantis Bugtracker Plugin Tasks
#
# By Cas Nuy  www.nuy.info 2023
# To be used with Mantis 2.x and above
#
########################################################
$reqVar = '_' . $_SERVER['REQUEST_METHOD'];
$form_vars	= $$reqVar;
$edit_id		= $form_vars['edit_id'] ;
layout_page_header();
layout_page_begin();
print_manage_menu();

// get current values
$query = "SELECT * FROM {plugin_Tasks_cat} WHERE taskcat_id = $edit_id ";
$result = db_query($query);
$row = db_fetch_array( $result );
$project=$row['project_id'];
$title=$row['taskcat_title'];

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
						<tr></tr>
						<tr><td>
						<form name="taskcatediting" action="<?php echo plugin_page( 'task_cat_edit2' ) ?>" method="post">
						<input type="hidden" name="edit_id" value="<?php echo $edit_id;  ?>">
						</td>
					</div>
					<?php echo lang_get( 'taskcat_edit_comments' ) ?>
					<td>
					<?php echo lang_get( 'project' ) ?> :
					</td><td>
					<?php echo lang_get( 'task_category' ) ?> :
					</td>
					<td></td><td></td>
					</tr>
					<tr><td>
					<?php
					echo '<select name="project">';
					print_project_option_list( $project );
					echo '</select>';
					?>
					</td>
					<td>
					<?php
					print ("<INPUT TYPE=text NAME=taskcat_title SIZE=50 MAXLENGTH=50 VALUE='".$title."'>"); 
					?>
					</td>
					<td><input name="Update" type="submit" value="Update"></td>
					<td><a href="../../../plugin.php?page=Tasks/task_categories"><b><?php echo 'Return' ?></b></a>
					</td>
					</tr>
					</form>
					</table>
			</div>
		</div>
	</div>
</div>
<?php

layout_page_end( );
