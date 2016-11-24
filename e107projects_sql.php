CREATE TABLE `e107projects_project` (
`project_id` int(11) unsigned NOT NULL COMMENT 'Project ID from Github.',
`project_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Project name.',
`project_full_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Project full name.',
`project_owner` varchar(255) NOT NULL DEFAULT '' COMMENT 'Project owner.',
`project_description` text COMMENT 'Project description.',
`project_stars` int(11) NOT NULL DEFAULT '0' COMMENT 'Number of stars for project.',
`project_commits` int(11) NOT NULL DEFAULT '0' COMMENT 'Number of commits.',
`project_issues` int(11) NOT NULL DEFAULT '0' COMMENT 'Number of issues.',
`project_author` int(11) NOT NULL DEFAULT '0' COMMENT 'User, who submitted the project.',
`project_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Status for the project. Visible or not.',
`project_submitted` int(11) NOT NULL DEFAULT '0' COMMENT 'Submitted time as timestamp.',
`project_updated` int(11) NOT NULL DEFAULT '0' COMMENT 'Updated time as timestamp.',
PRIMARY KEY (`project_id`),
KEY `project_name` (`project_name`),
KEY `project_full_name` (`project_full_name`),
KEY `project_owner` (`project_owner`),
KEY `project_stars` (`project_stars`),
KEY `project_commits` (`project_commits`),
KEY `project_issues` (`project_issues`),
KEY `project_author` (`project_author`),
KEY `project_status` (`project_status`),
KEY `project_submitted` (`project_submitted`),
KEY `project_updated` (`project_updated`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `e107projects_location` (
`location_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Location.',
`location_lat` decimal(10,8) NOT NULL COMMENT 'Latitude.',
`location_lon` decimal(10,8) NOT NULL COMMENT 'Longitude.',
PRIMARY KEY (`location_name`),
KEY `location_lat` (`location_lat`),
KEY `location_lon` (`location_lon`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
