<?php	
########################################################
# Mantis Bugtracker Plugin Tasks
#
# By Cas Nuy  www.nuy.info 2020
# To be used with Mantis 2.x and above
#
########################################################
# what is the table for tasks
layout_page_header(  );
layout_page_begin( );
print_manage_menu();
$link=plugin_page('config');
$link2=plugin_page('task_definition');
?>
<tr>
<td class="center" colspan="6">
<br>
<?php 
$colspan=6;
?>
<table class="width100" cellspacing="1">
<tr>
<td class="form-title" colspan="8">
<a href="<?php echo $link ?>"><?php echo lang_get( 'task_configuration' ) ?></a>
<a href="<?php echo $link2 ?>"><?php echo lang_get( 'task_definition' ) ?></a>
</td>
</tr>
<tr>
<td colspan="<?php echo $colspan ?>" class="row-category"><div align="left"><a name="taskrecord"></a>
<?php 
echo lang_get( 'alltasks' ); 
?>
</div>
</td>
</tr>
<tr class="row-category">
<td><div align="center"><?php echo lang_get( 'task_title' ); ?>/<?php echo lang_get( 'task_due' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'task_handler' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'task_desc' ); ?></div></td>
<td><div align="center"><?php echo lang_get( 'task_response' ); ?></div></td> 
<td><div align="center"><?php echo lang_get( 'task_actions' ); ?></div></td>
</tr>
<?php
# Pull all Tasks-Record entries for the current user
$query = "SELECT * FROM {plugin_Tasks_defined} WHERE task_completed = '0000-00-00 00:00:00' ORDER BY task_due ASC";
$result = db_query($query);
while ($row = db_fetch_array($result)) {
	?>
	<tr>
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
	<a href="view.php?id=<?php echo $row['bug_id'];?>"><?php echo lang_get( 'issue_task' ) ?></a><br>
	</div></td>
	</tr>
	<?php
}	 
?>
</table>
</td>
</tr>
<?php
layout_page_end( );