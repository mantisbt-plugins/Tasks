<?php
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );
layout_page_header( lang_get( 'tasks' ) );
layout_page_begin( 'config_page.php' );

print_manage_menu();
$link=plugin_page('task_definition');
$link2=plugin_page('task_categories');
?>
<div class="col-md-12 col-xs-12">
<div class="space-10"></div>
<div class="form-container" > 
<form action="<?php echo plugin_page( 'config_edit' ) ?>" method="post">
<div class="widget-box widget-color-blue2">
<div class="widget-header widget-header-small">
	<h4 class="widget-title lighter">
		<i class="ace-icon fa fa-text-width"></i>
		<?php echo lang_get( 'tasks' ) . ': ' . lang_get( 'plugin_tasks_config' )?>
	</h4>
</div>
<div class="widget-body">
<div class="widget-main no-padding">
<div class="table-responsive"> 
<table class="table table-bordered table-condensed table-striped"> 
<tr>
<td class="form-title" colspan="3">
<a href="<?php echo $link ?>"><?php echo lang_get( 'task_definition' ) ?></a>
<a href="<?php echo $link2 ?>"><?php echo lang_get( 'task_categories' ) ?></a>
</td>
</tr>
<tr  >
<td class="category" colspan="3">

</td>
</tr>
<tr>
<td class="form-title" colspan="3">
<?php echo lang_get( 'plugin_tasks_title' ) . ': ' . lang_get( 'plugin_tasks_config' ) ?>
</td>
</tr>

<tr  >
<td class="category">
<?php echo lang_get( 'tasks_admin_threshold' ) ?>
</td>
<td class="center">
<select name="tasks_admin_threshold">
<?php print_enum_string_option_list( 'access_levels', plugin_config_get( 'tasks_admin_threshold'  ) ) ?>;
</select> 
</td>
</tr>

<tr  >
<td class="category">
<?php echo lang_get( 'tasks_add_threshold' ) ?>
</td>
<td class="center">
<select name="tasks_add_threshold">
<?php print_enum_string_option_list( 'access_levels', plugin_config_get( 'tasks_add_threshold'  ) ) ?>;
</select> 
</td>
</tr>

<tr  >
<td class="category">
<?php echo lang_get( 'tasks_allocate_threshold' ) ?>
</td>
<td class="center">
<select name="tasks_allocate_threshold">
<?php print_enum_string_option_list( 'access_levels', plugin_config_get( 'tasks_allocate_threshold'  ) ) ?>;
</select> 
</td>
</tr>

<tr  >
<td class="category">
<?php echo lang_get( 'tasks_view_threshold' ) ?>
</td>
<td class="center">
<select name="tasks_view_threshold">
<?php print_enum_string_option_list( 'access_levels', plugin_config_get( 'tasks_view_threshold'  ) ) ?>;
</select> 
</td>
</tr>

<tr  >
<td class="category">
<?php echo lang_get( 'tasks_update_threshold' ) ?>
</td>
<td class="center">
<select name="tasks_update_threshold">
<?php print_enum_string_option_list( 'access_levels', plugin_config_get( 'tasks_update_threshold'  ) ) ?>;
</select> 
</td>
</tr>

<tr  >
<td class="category">
<?php echo lang_get( 'tasks_edit_threshold' ) ?>
</td>
<td class="center">
<select name="tasks_edit_threshold">
<?php print_enum_string_option_list( 'access_levels', plugin_config_get( 'tasks_edit_threshold'  ) ) ?>;
</select> 
</td>
</tr>

<tr  >
<td class="category">
<?php echo lang_get( 'tasks_delete_threshold' ) ?>
</td>
<td class="center">
<select name="tasks_delete_threshold">
<?php print_enum_string_option_list( 'access_levels', plugin_config_get( 'tasks_delete_threshold'  ) ) ?>;
</select> 
</td>
</tr>

<tr  >
<td class="category">
<?php echo lang_get( 'tasks_finish_threshold' ) ?>
</td>
<td class="center">
<select name="tasks_finish_threshold">
<?php print_enum_string_option_list( 'access_levels', plugin_config_get( 'tasks_finish_threshold'  ) ) ?>;
</select> 
</td>
</tr>

<tr  >
<td class="category">
<?php echo lang_get( 'tasks_auto_start_status' ) ?>
</td>
<td class="center">
<select name="tasks_auto_start_status">
<?php print_enum_string_option_list( 'status', plugin_config_get( 'tasks_auto_start_status'  ) ) ?>;
</select> 
</td>
</tr>

<tr >
<td class="category" width="60%">
<?php echo lang_get( 'tasks_update_bug' )?>
</td>
<td class="center" width="20%">
<label><input type="radio" name='tasks_update_bug' value="1" <?php echo( ON == plugin_config_get( 'tasks_update_bug' ) ) ? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'tasks_enabled' )?></label>
<label><input type="radio" name='tasks_update_bug' value="0" <?php echo( OFF == plugin_config_get( 'tasks_update_bug' ) )? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'tasks_disabled' )?></label>
</td>
</tr> 

<?php
if (plugin_is_loaded('Usergroups')){
?>
<tr  >
<td class="category" width="60%">
<?php echo lang_get( 'tasks_assign_group' )?>
</td>
<td class="center" width="20%">
<label><input type="radio" name='tasks_assign_group' value="1" <?php echo( ON == plugin_config_get( 'tasks_assign_group' ) ) ? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'tasks_enabled' )?></label>
<label><input type="radio" name='tasks_assign_group' value="0" <?php echo( OFF == plugin_config_get( 'tasks_assign_group' ) )? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'tasks_disabled' )?></label>
</td>
</tr> 
<?php
}
?>


<tr  >
<td class="category">
<?php echo lang_get( 'tasks_hour_rate' ) ?>
</td>
<td class="center">
<input type="text" size="10" maxlength="10" name="tasks_hour_rate" value="<?php echo plugin_config_get( 'tasks_hour_rate'  )?>"/>
</td>
</tr> 

<tr  >
<td class="category" width="60%">
<?php echo lang_get( 'tasks_mail' )?>
</td>
<td class="center" width="20%">
<label><input type="radio" name='tasks_mail' value="1" <?php echo( ON == plugin_config_get( 'tasks_mail' ) ) ? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'tasks_enabled' )?></label>
<label><input type="radio" name='tasks_mail' value="0" <?php echo( OFF == plugin_config_get( 'tasks_mail' ) )? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'tasks_disabled' )?></label>
</td>
</tr> 

<tr  >
<td class="category" width="60%">
<?php echo lang_get( 'tasks_mail_finish' )?>
</td>
<td class="center" width="20%">
<label><input type="radio" name='tasks_mail_finish' value="1" <?php echo( ON == plugin_config_get( 'tasks_mail_finish' ) ) ? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'tasks_enabled' )?></label>
<label><input type="radio" name='tasks_mail_finish' value="0" <?php echo( OFF == plugin_config_get( 'tasks_mail_finish' ) )? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'tasks_disabled' )?></label>
</td>
</tr> 

<tr  >
<td class="category" width="60%">
<?php echo lang_get( 'tasks_history' )?>
</td>
<td class="center" width="20%">
<label><input type="radio" name='tasks_history' value="1" <?php echo( ON == plugin_config_get( 'tasks_history' ) ) ? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'tasks_enabled' )?></label>
<label><input type="radio" name='tasks_history' value="0" <?php echo( OFF == plugin_config_get( 'tasks_history' ) )? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'tasks_disabled' )?></label>
</td>
</tr> 

<tr  >
<td class="category" width="60%">
<?php echo lang_get( 'tasks_check' )?>
</td>
<td class="center" width="20%">
<label><input type="radio" name='tasks_check' value="1" <?php echo( ON == plugin_config_get( 'tasks_check' ) ) ? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'tasks_enabled' )?></label>
<label><input type="radio" name='tasks_check' value="0" <?php echo( OFF == plugin_config_get( 'tasks_check' ) )? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'tasks_disabled' )?></label>
</td>
</tr> 

<tr  >
<td class="category" width="60%">
<?php echo lang_get( 'tasks_show_menu' )?>
</td>
<td class="center" width="20%">
<label><input type="radio" name='tasks_show_menu' value="1" <?php echo( ON == plugin_config_get( 'tasks_show_menu' ) ) ? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'tasks_enabled' )?></label>
<label><input type="radio" name='tasks_show_menu' value="0" <?php echo( OFF == plugin_config_get( 'tasks_show_menu' ) )? 'checked="checked" ' : ''?>/>
<?php echo lang_get( 'tasks_disabled' )?></label>
</td>
</tr> 

<tr  >
<td class="category" width="60%">
<?php echo lang_get( 'tasks_clean' )?>
</td>
<td class="center" width="20%">
<label><input type="radio" name='tasks_clean' value="1" />
<?php echo lang_get( 'tasks_enabled' )?></label>
<label><input type="radio" name='tasks_clean' value="0"  checked="checked" />
<?php echo lang_get( 'tasks_disabled' )?></label>
</td>
</tr> 


<tr>
<td class="center" colspan="3">
<input type="submit" class="button" value="<?php echo lang_get( 'change_configuration' ) ?>" />
</td>
</tr>

</table>
</div>
</div>
</div>
</div>
</form>
</div>
</div>
<?php
layout_page_end();