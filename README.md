########################################################
# 	Mantis Bugtracker Add-On
# 	Tasks Version 3.20
#	2010-2023 plugin by Cas Nuy www.NUY.info
########################################################

This plugin allows to assign tasks to other authorised handlers within the current project.
Every tasks has :
Handler
Creation date	(automatic)
Change date		(automatic)
Due  date		(to be set once)
Finished date	(to be set by authorised person)
Title 			( 50 characters )
Description		( 250 characters )
Response		( 250 characters )
Once a task has been reported complete, it can only be deleted by authorised staff. Maintenance is no longer possible.
Upon adding a task, the person you assign it to wil receive an email in case plugin is configured to do.
Equally from every change, History records can be created in case plugin is configured to do.

If configured accordingly the system will verify if all tasks for this issue have been finished.

One can also setup auto tasks per project/category.
This means that whenever an issue is reported with the defined project/category,
the system automatically will assign various tasks to predefined users.
The due date will be the date of reporting plus a defined number of working days.

Default the overview with tasks is presented where the event_hook 'EVENT_VIEW_BUG_EXTRA' is positioned.
If you like to have it on top of the page, simply change the following line in tasks.php:
		plugin_event_hook( 'EVENT_VIEW_BUG_EXTRA', 'tasks_form1' );
into
		plugin_event_hook( 'EVENT_VIEW_BUG_DETAILS', 'tasks_form1' );

In case you would like to have it just below the details, replace the following line in tasks.php:
		plugin_event_hook( 'EVENT_VIEW_BUG_EXTRA', 'tasks_form1' );
with
		event_declare('EVENT_VIEW_BUG_DETAILS2');
		plugin_event_hook( 'EVENT_VIEW_BUG_DETAILS2', 'tasks_form1' );

In that case, you need to position the following line somewhere in bug_view_inc.php.
Search the line holding "# User list monitoring the bug" around line 686 and add the following line just before:
		event_signal( 'EVENT_VIEW_BUG_DETAILS2', array( $tpl_bug_id ) );

********************************************************************************************
* Installation                                                                             *
********************************************************************************************
Add the following line in config_inc.php:
$g_tasks_show_menu = OFF;
In OFF position, Open tasks are shown under My View
Do ensure to define a signal in my_view_page.php.
Then add the following line right after "print_recently_visited();"
event_signal( 'EVENT_MYVIEW' );
In ON position, a menu item will appear named "My tasks".

In addition we need to add a signal in core/bug_api.php in case we want to check tasks upon resolving
Find function bug_resolve and add the following line:
	event_signal( 'EVENT_RESOLVE_BUG', $p_bug_id );
just before :
	# Add bugnote if supplied

In addition we need to add a signal in core/bug_api.php in case we want to check tasks upon closing
Find function bug_close and add the following line:
	event_signal( 'EVENT_CLOSE_BUG', $p_bug_id );
just before :
	# Add bugnote if supplied

The rest is like any other plugin.
After copying to your webserver :
- Start mantis ad administrator
- Select manage
- Select manage Plugins
- Select Install behind Tasks 2.20
- Once installed, click on the plugin-name for further configuration.


In order to have the tasks printed on the Print lay-out, please add the following linea to print_bug_page.php:
	// Tasks-plugin
		echo '<tr><td class="print-spacer" colspan="6"><hr size="1" /></td></tr>';
		event_signal( 'EVENT_PRINT_TASKS', $f_bug_id );
	// Tasks-plugin
Add these lines just before "# Issue History" around line 496

********************************************************************************************
Configuration options                                                                      *
********************************************************************************************
// Who is allowed to manage this plugin
tasks_admin_treshold		=	ADMINISTRATOR

// Who is allowed to add tasks
tasks_add_treshold			= 	DEVELOPER

// Who can we give tasks
tasks_allocating_treshold	= 	DEVELOPER

// Who is allowed to view tasks
tasks_view_treshold			= 	VIEWER

// Who is allowed to update tasks
tasks_update_treshold		= 	DEVELOPER

// Who is allowed to edit tasks
tasks_edit_treshold		= 	ADMINISTRATOR
The creator of the tasks can do this also

// Who is allowed to delete tasks
tasks_delete_treshold		= 	ADMINISTRATOR
The creator of the tasks can do this also

// Who is allowed to mark tasks complete
tasks_finish_treshold		= 	DEVELOPER

// When should we add auto tasks
tasks_auto_start_status		= 	NEW

// Do we send an email when adding task?
tasks_mail					=	OFF

// Do we send an email when  task is complete?
tasks_mail_finish			=	OFF

// Do we keep history of activities
tasks_history				=	OFF

// Do we check if all tasks are complete upon resoling
tasks_check					=	OFF

// Where do we show Open tasks
tasks_show_menu				=	OFF   


********************************************************************************************
License                                                                                    *
********************************************************************************************
This plugin is distributed under the same conditions as Mantis itself.

********************************************************************************************
Mantis Issue                                                                               *
********************************************************************************************
http://www.mantisbt.org/bugs/view.php?id=12789

********************************************************************************************
Greetings                                                                                  *
********************************************************************************************
Cas Nuy
cas@nuy.info
http://www.nuy.info
