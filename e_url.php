<?php

/**
 * @file
 * Simple mod-rewrite module.
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class e107projects_url.
 */
class e107projects_url
{

	/**
	 * @return array
	 */
	function config()
	{
		$config = array();

		// Github callback.
		$config['github/callback'] = array(
			// Matched against url, and if true, redirected to 'redirect' below.
			'regex'    => '^github/callback$',
			// Used by e107::url(); to create a url from the db table.
			'sef'      => 'github/callback',
			// File-path of what to load when the regex returns true.
			'redirect' => '{e_PLUGIN}e107projects/callback.php',
		);

		// Project page.
		$config['project'] = array(
			'alias'    => 'project',
			// Matched against url, and if true, redirected to 'redirect' below.
			'regex'    => '^{alias}/([^\/]*)/([^\/]*)/?$',
			// Used by e107::url(); to create a url from the db table.
			'sef'      => '{alias}/{user}/{repository}',
			// File-path of what to load when the regex returns true.
			'redirect' => '{e_PLUGIN}e107projects/project.php?user=$1&repository=$2'
		);

		// Projects - search page.
		$config['projects'] = array(
			'alias'    => 'projects',
			// Matched against url, and if true, redirected to 'redirect' below.
			'regex'    => '^{alias}$',
			// Used by e107::url(); to create a url from the db table.
			'sef'      => '{alias}',
			// File-path of what to load when the regex returns true.
			'redirect' => '{e_PLUGIN}e107projects/projects.php',
		);

		// Project submission.
		$config['projects/submit'] = array(
			'alias'    => 'projects/submit',
			// Matched against url, and if true, redirected to 'redirect' below.
			'regex'    => '^{alias}$',
			// Used by e107::url(); to create a url from the db table.
			'sef'      => '{alias}',
			// File-path of what to load when the regex returns true.
			'redirect' => '{e_PLUGIN}e107projects/submit.php',
		);

		// Contributors page.
		$config['contributors'] = array(
			'alias'    => 'contributors',
			// Matched against url, and if true, redirected to 'redirect' below.
			'regex'    => '^{alias}$',
			// Used by e107::url(); to create a url from the db table.
			'sef'      => '{alias}',
			// File-path of what to load when the regex returns true.
			'redirect' => '{e_PLUGIN}e107projects/contributors.php'
		);

		return $config;
	}

}