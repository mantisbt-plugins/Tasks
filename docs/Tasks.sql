CREATE TABLE `mantis_plugin_Tasks_autodefined_table` (
  `autotask_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `autotask_handler` int(11) DEFAULT NULL,
  `autotaskcat_id` int(11) DEFAULT NULL,
  `autotask_desc` varchar(250) DEFAULT NULL,
  `autotask_due` int(11) DEFAULT NULL,
  `autotask_status` smallint(6) NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `mantis_plugin_Tasks_cat_table` (
  `taskcat_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `taskcat_title` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `mantis_plugin_Tasks_defined_table` (
  `task_id` int(10) UNSIGNED NOT NULL,
  `bug_id` int(11) DEFAULT NULL,
  `task_user` int(11) DEFAULT NULL,
  `task_handler` int(11) DEFAULT NULL,
  `taskcat_id` int(11) DEFAULT NULL,
  `task_title` varchar(250) DEFAULT NULL,
  `task_desc` varchar(250) DEFAULT NULL,
  `task_response` varchar(250) DEFAULT NULL,
  `task_created` datetime DEFAULT NULL,
  `task_changed` datetime DEFAULT NULL,
  `task_due` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `task_completed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `task_completed_by` int(11) DEFAULT NULL,
  `task_group` int(11) DEFAULT NULL,
  `task_time` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

ALTER TABLE `mantis_plugin_Tasks_autodefined_table`
  ADD PRIMARY KEY (`autotask_id`);
ALTER TABLE `mantis_plugin_Tasks_cat_table`
  ADD PRIMARY KEY (`taskcat_id`);
ALTER TABLE `mantis_plugin_Tasks_defined_table`
  ADD PRIMARY KEY (`task_id`);
ALTER TABLE `mantis_plugin_Tasks_autodefined_table`
  MODIFY `autotask_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `mantis_plugin_Tasks_cat_table`
  MODIFY `taskcat_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `mantis_plugin_Tasks_defined_table`
  MODIFY `task_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

