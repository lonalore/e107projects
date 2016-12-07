CREATE TABLE `e107projects_project` (
`project_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Project ID from Github.',
`project_author` int(11) NOT NULL DEFAULT '0' COMMENT 'User, who submitted the project. e107 user ID.',
`project_user` varchar(50) NOT NULL DEFAULT '' COMMENT 'Project owner. Github username.',
`project_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'Project name.',
`project_description` text COMMENT 'Project description.',
`project_stars` int(11) NOT NULL DEFAULT '0' COMMENT 'Number of stars for project.',
`project_watchers` int(11) NOT NULL DEFAULT '0' COMMENT 'Number of watchers for project.',
`project_forks` int(11) NOT NULL DEFAULT '0' COMMENT 'Number of forks for project.',
`project_open_issues` int(11) NOT NULL DEFAULT '0' COMMENT 'Number of open issues for project.',
`project_commits` int(11) NOT NULL DEFAULT '0' COMMENT 'Number of commits.',
`project_default_branch` varchar(50) NOT NULL DEFAULT '' COMMENT 'Default branch for project.',
`project_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Status for the project. Visible or not.',
`project_submitted` int(11) NOT NULL DEFAULT '0' COMMENT 'Submitted time as timestamp.',
`project_updated` int(11) NOT NULL DEFAULT '0' COMMENT 'Updated time as timestamp.',
`project_readme` text COMMENT 'Readme file contents.',
`project_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Project type.',
PRIMARY KEY (`project_id`),
KEY `project_author` (`project_author`),
KEY `project_user` (`project_user`),
KEY `project_name` (`project_name`),
KEY `project_stars` (`project_stars`),
KEY `project_commits` (`project_commits`),
KEY `project_status` (`project_status`),
KEY `project_submitted` (`project_submitted`),
KEY `project_updated` (`project_updated`),
KEY `project_type` (`project_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `e107projects_release` (
`release_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Release ID from Github.',
`release_project_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Project ID from Github.',
`release_project_user` varchar(50) NOT NULL DEFAULT '' COMMENT 'Project owner. Github username.',
`release_project_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'Project name.',
`release_tag_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'Release tag name.',
`release_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'Release name.',
`release_draft` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Release is a draft.',
`release_author_id` int(11) NOT NULL DEFAULT '0' COMMENT 'User ID from Github.',
`release_author_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'Release author name.',
`release_prerelease` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Release is a prerelease.',
`release_created_at` int(11) NOT NULL DEFAULT '0' COMMENT 'Created at.',
`release_published_at` int(11) NOT NULL DEFAULT '0' COMMENT 'Published at.',
PRIMARY KEY (`release_id`),
KEY `release_project_id` (`release_project_id`),
KEY `release_project_user` (`release_project_user`),
KEY `release_project_name` (`release_project_name`),
KEY `release_tag_name` (`release_tag_name`),
KEY `release_name` (`release_name`),
KEY `release_draft` (`release_draft`),
KEY `release_author_id` (`release_author_id`),
KEY `release_author_name` (`release_author_name`),
KEY `release_prerelease` (`release_prerelease`),
KEY `release_created_at` (`release_created_at`),
KEY `release_published_at` (`release_published_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `e107projects_contribution` (
`project_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Project ID from Github.',
`project_user` varchar(50) NOT NULL DEFAULT '' COMMENT 'Project owner. Github username.',
`project_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'Project name.',
`contributor_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Github user ID of contributor.',
`contributor_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'Github username of contributor.',
`contributions` int(11) NOT NULL DEFAULT '0' COMMENT 'Number of contributions.',
KEY `project_id` (`project_id`),
KEY `project_user` (`project_user`),
KEY `project_name` (`project_name`),
KEY `contributor_id` (`contributor_id`),
KEY `contributor_name` (`contributor_name`),
KEY `contributions` (`contributions`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `e107projects_contributor` (
`contributor_id` int(11) NOT NULL DEFAULT '0' COMMENT 'e107 user ID.',
`contributor_gid` int(11) NOT NULL DEFAULT '0' COMMENT 'Github user ID.',
`contributor_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'Github username.',
`contributor_avatar` varchar(255) NOT NULL DEFAULT '' COMMENT 'Avatar URL.',
`contributor_type` varchar(50) NOT NULL DEFAULT '' COMMENT 'Type.',
KEY `contributor_id` (`contributor_id`),
KEY `contributor_gid` (`contributor_gid`),
KEY `contributor_name` (`contributor_name`),
KEY `contributor_type` (`contributor_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `e107projects_hook` (
`hook_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Hook ID.',
`hook_project_user` varchar(50) NOT NULL DEFAULT '' COMMENT 'Github username.',
`hook_project_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'Github repository name.',
`hook_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'Hook name.',
`hook_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Hook active.',
`hook_events` varchar(255) NOT NULL DEFAULT '' COMMENT 'Hook events.',
`hook_config_url` varchar(255) NOT NULL DEFAULT '' COMMENT 'Hook URL.',
`hook_config_type` varchar(50) NOT NULL DEFAULT '' COMMENT 'Hook Content Type.',
`hook_created_at` int(11) NOT NULL DEFAULT '0' COMMENT 'Created at.',
`hook_updated_at` int(11) NOT NULL DEFAULT '0' COMMENT 'Updated at.',
`hook_access_token` varchar(255) NOT NULL DEFAULT '' COMMENT 'Access token for managing hook.',
PRIMARY KEY (`hook_id`),
KEY `hook_project_user` (`hook_project_user`),
KEY `hook_project_name` (`hook_project_name`),
KEY `hook_created_at` (`hook_created_at`),
KEY `hook_updated_at` (`hook_updated_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `e107projects_location` (
`location_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Location.',
`location_lat` decimal(10,8) NOT NULL COMMENT 'Latitude.',
`location_lon` decimal(10,8) NOT NULL COMMENT 'Longitude.',
PRIMARY KEY (`location_name`),
KEY `location_lat` (`location_lat`),
KEY `location_lon` (`location_lon`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `e107projects_e107org_release` (
`or_project_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Project ID from Github.',
`or_project_user` varchar(50) NOT NULL DEFAULT '' COMMENT 'Project owner. Github username.',
`or_project_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'Project name.',
`or_version` varchar(50) NOT NULL DEFAULT '' COMMENT 'Release version.',
`or_compatibility` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Compatibility. 1 or 2',
`or_url` varchar(255) NOT NULL DEFAULT '' COMMENT 'URL for downloading.',
`or_date` int(11) NOT NULL DEFAULT '0' COMMENT 'Published at.',
KEY `or_project_id` (`or_project_id`),
KEY `or_project_user` (`or_project_user`),
KEY `or_project_name` (`or_project_name`),
KEY `or_version` (`or_version`),
KEY `or_compatibility` (`or_compatibility`),
KEY `or_url` (`or_url`),
KEY `or_date` (`or_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
