<?php	
########################################################
# Mantis Bugtracker Plugin Tasks
#
# By Cas Nuy  www.nuy.info 2020
# To be used with Mantis 2.x and above
#
########################################################
require_once( 'core.php' );
auth_ensure_user_authenticated();
$t_export_title = "Export_all_open_tasks";
$t_export_title = preg_replace( '[\/:*?"<>|]', '', $t_export_title );
# Make sure that IE can download the attachments under https.
header( 'Pragma: public' );
header( 'Content-Type: application/vnd.ms-excel' );
header( 'Content-Disposition: attachment; filename="' . $t_export_title . '.xls"' );
require_once( config_get( 'plugin_path' ) . 'Tasks' . DIRECTORY_SEPARATOR . 'Tasks_api.php' );  
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns="http://www.w3.org/TR/REC-html40">
<style id="Classeur1_16681_Styles">
</style>
<div id="Classeur1_16681" align=center x:publishsource="Excel">
<table x:str border=0 cellpadding=0 cellspacing=0 width=100% style='border-collapse:collapse'>
<tr>
  <td>ID</td>
  <td>Issue</td>
  <td>Title</td>
  <td>Category</td>
  <td>Handler</td>
  <td>Duedate</td>
  <td>Description</td>
  <td>Response</td>
 </tr>

<?php
# Pull all Tasks-Record entries for the current user
$query = "SELECT a.*,b.taskcat_title FROM {plugin_Tasks_defined} as a,{plugin_Tasks_cat}e as b  WHERE a.taskcat_id=b.taskcat_id and task_completed = '0000-00-00 00:00:00' ORDER BY task_handler,task_due ASC";
$result = db_query($query);
while ($row = db_fetch_array($result)) {
	?>
	<tr>
	<td><?PHP echo ($row["task_id"]);?></td>
	<td><?PHP echo ($row["bug_id"]);?></td>
	<td><?PHP echo html_entity_decode($row["task_title"]);?></td>
	<td><?PHP echo html_entity_decode($row["taskcat_title"]);?></td>
	<td><?PHP echo user_get_name($row["task_handler"]);?></td>
	<td><?PHP echo date("d.m.Y", strtotime($row["task_due"])) ;?></td>
	<td><?PHP echo html_entity_decode($row["task_desc"])?></td>
	<td><?PHP echo html_entity_decode($row["task_response"])?></td>
	</tr>
	<?php
}	 

if ( ON == $use_groups){
$query = "SELECT a.*,b.taskcat_title FROM {plugin_Tasks_defined} as a,{plugin_Tasks_cat} as b  WHERE a.taskcat_id=b.taskcat_id and task_completed = '0000-00-00 00:00:00' and task_group>0 ORDER BY task_group,task_due ASC";
$result = db_query($query);
while ($row = db_fetch_array($result)) {
	?>
	<tr>
	<td><?PHP echo ($row["task_id"]); ?></td>
	<td><?PHP echo ($row["bug_id"]); ?></td>
	<td><?php echo html_entity_decode($row["task_title"]); ?></td>
	<td><?php echo $row["taskcat_title"]; ?></td>
	<td><?php echo group_get_name($row["task_group"]); ?></td>
	<td><?php echo date("d.m.Y", strtotime($row["task_due"])); ?></td>
	<td><?PHP echo html_entity_decode($row["task_desc"]);?></td>
	<td><?PHP echo html_entity_decode($row["task_response"]);?></td>
	</tr>
	<?php
}	
} 
?>
</table>
