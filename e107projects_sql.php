CREATE TABLE `e107projects_project` (
`project_id` int(11) unsigned NOT NULL COMMENT 'Project ID from Github.',
`project_author` int(11) NOT NULL DEFAULT '0' COMMENT 'User, who submitted the project. e107 user ID.',
`project_user` varchar(255) NOT NULL DEFAULT '' COMMENT 'Project owner. Github username.',
`project_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Project name.',
`project_description` text COMMENT 'Project description.',
`project_stars` int(11) NOT NULL DEFAULT '0' COMMENT 'Number of stars for project.',
`project_commits` int(11) NOT NULL DEFAULT '0' COMMENT 'Number of commits.',
`project_issues` int(11) NOT NULL DEFAULT '0' COMMENT 'Number of issues.',
`project_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Status for the project. Visible or not.',
`project_submitted` int(11) NOT NULL DEFAULT '0' COMMENT 'Submitted time as timestamp.',
`project_updated` int(11) NOT NULL DEFAULT '0' COMMENT 'Updated time as timestamp.',
`project_secret` varchar(32) NOT NULL DEFAULT '' COMMENT 'Generated secret key for Github Webhooks.',
PRIMARY KEY (`project_id`),
KEY `project_author` (`project_author`),
KEY `project_user` (`project_user`),
KEY `project_name` (`project_name`),
KEY `project_stars` (`project_stars`),
KEY `project_commits` (`project_commits`),
KEY `project_issues` (`project_issues`),
KEY `project_status` (`project_status`),
KEY `project_submitted` (`project_submitted`),
KEY `project_updated` (`project_updated`),
KEY `project_secret` (`project_secret`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `e107projects_user` (
`user_id` int(11) NOT NULL DEFAULT '0' COMMENT 'e107 user ID.',
`user_gid` int(11) unsigned NOT NULL COMMENT 'Github user ID.',
`user_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'Github username.',
PRIMARY KEY (`user_id`),
KEY `user_gid` (`user_gid`),
KEY `user_name` (`user_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `e107projects_location` (
`location_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Location.',
`location_lat` decimal(10,8) NOT NULL COMMENT 'Latitude.',
`location_lon` decimal(10,8) NOT NULL COMMENT 'Longitude.',
PRIMARY KEY (`location_name`),
KEY `location_lat` (`location_lat`),
KEY `location_lon` (`location_lon`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
