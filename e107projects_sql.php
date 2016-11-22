CREATE TABLE `e107projects_project` (
`project_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary ID.',
`project_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Project name.',
`project_author` int(11) NOT NULL DEFAULT '0' COMMENT 'User, who submitted the project.',
`project_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Status for the project. Visible or not.',
PRIMARY KEY (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
