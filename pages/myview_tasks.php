<?php	
########################################################
# Mantis Bugtracker Plugin Tasks
#
# By Cas Nuy  www.nuy.info 2023
# To be used with Mantis 2.x and above
#
########################################################
global $t_url_link_parameters;
global $t_bug_class;
global $t_update_bug_threshold;
global $t_filter;
require_once( config_get( 'plugin_path' ) . 'Tasks' . DIRECTORY_SEPARATOR . 'Tasks_api.php' );  
$user 			= auth_get_current_user_id();

$query = "CREATE TEMPORARY TABLE MyTasks SELECT * FROM {plugin_Tasks_defined} WHERE task_handler = $user  and task_completed = '0000-00-00 00:00:00' ORDER BY task_due ASC";
$query = "CREATE TEMPORARY TABLE IF NOT EXISTS MyTasks AS(SELECT * FROM {plugin_Tasks_defined} WHERE task_handler = $user  and task_completed = '0000-00-00 00:00:00' ORDER BY task_due ASC)";
$result = db_query($query);
$query = "select * from MyTasks";
$result = db_query($query);
$t_count=db_num_rows($result);

//if ( $t_count === 0 ) {
//	$query2 = "CREATE TEMPORARY TABLE MyTasks SELECT a.* FROM {plugin_Tasks_defined} a ,{plugin_Usergroups_usergroup} b  WHERE task_group= group_id and user_id = $user  and task_completed = '0000-00-00 00:00:00' ORDER BY task_due ASC";
	
//} else {
	$query2 = "insert into MyTasks SELECT a.* FROM {plugin_Tasks_defined} a,{plugin_Usergroups_usergroup} b  WHERE task_group= group_id and user_id = $user  and task_completed = '0000-00-00 00:00:00' ORDER BY task_due ASC";

//}
/*
$use_groups		= config_get( 'plugin_Tasks_tasks_assign_group' );
if ( ON == $use_groups){
	$query2 = "SELECT * FROM {plugin_Tasks_defined},{plugin_Usergroups_usergroup}  WHERE task_group= group_id and user_id = $user  and task_completed = '0000-00-00 00:00:00' ORDER BY task_due ASC";
	$result = db_query($query2);
	$t_count=db_num_rows($result);
} 
*/
$result = db_query($query2);
$query = "select * from MyTasks order by bug_id";
$result = db_query($query);
$t_count=db_num_rows($result);

if ($t_count>0) {
$t_box_title_label = 'tasks' ;
$t_box_title = 'My Tasks';
$t_collapse_block = is_collapsed( $t_box_title );
$t_block_css = $t_collapse_block ? 'collapsed' : '';
$t_block_icon = $t_collapse_block ? 'fa-chevron-down' : 'fa-chevron-up';

$t_bug_string = $t_count == 1 ? 'bug' : 'bugs';

# -- ====================== BUG LIST ========================= --
?>
<div>
<div id="<?php echo $t_box_title ?>" class="widget-box widget-color-blue2 <?php echo $t_block_css ?>">
	<div class="widget-header widget-header-small">
		<h4 class="widget-title lighter">
			<?php print_icon( 'fa-list-alt', 'ace-icon' ); ?>
			<?php
			#-- Box title
			echo $t_box_title;
# -- Viewing range info
$v_start =  1;
$v_end = $v_start + $t_count - 1;
echo '<span class="badge"> ' . " $v_start - $v_end / $t_count " . ' </span>';
?>

		</h4>
		<div class="widget-toolbar">
			<a data-action="collapse" href="#">
				<?php print_icon( $t_block_icon, '1 ace-icon bigger-125' ); ?>
			</a>
		</div>
	</div>

	<div class="widget-body">
		<div class="widget-main no-padding">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed table-striped table-hover">
<tbody>
<?php


while ($t_bug  = db_fetch_array($result)) {

	$t_summary = string_display_line_links( $t_bug['task_title'] );
	$t_summary .= "  ( ";
	$t_summary .= html_entity_decode($t_bug["task_desc"]);
	$t_summary .= " )";
	?>

<tr class="my-buglist-bug <?php echo $t_bug_class?>">
	<?php
	# -- Bug ID and details link + Pencil shortcut --?>
	<td class="nowrap width-13 my-buglist-id">
		<?php
			print_bug_link( $t_bug['bug_id'], false );
			echo '<br />';

			?>
	</td>

	<?php
	# -- Summary --?>
	<td>
		<?php
			$t_bug_url = string_get_bug_view_url( $t_bug['bug_id'] );
			echo '<span><a href="' . $t_bug_url . '">' . $t_summary . '</a></span><br />';
	?>
		<?php
	?>
	</td>
</tr>
<?php
	# -- end of Repeating bug row --
	
}

# -- ====================== end of BUG LIST ========================= --

?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<?php
# Free the memory allocated for the rows in this box since it is not longer needed.
unset( $t_rows );
}
echo "<br>";