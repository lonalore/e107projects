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

		// Github callback with query support.
		$config['github/callback?'] = array(
			// Matched against url, and if true, redirected to 'redirect' below.
			'regex'    => '^github/callback/?\?(.*)$',
			// File-path of what to load when the regex returns true.
			'redirect' => '{e_PLUGIN}e107projects/callback.php?$1',
		);

		// Github callback.
		$config['github/callback'] = array(
			// Matched against url, and if true, redirected to 'redirect' below.
			'regex'    => '^github/callback/?(.*)$',
			// Used by e107::url(); to create a url from the db table.
			'sef'      => 'github/callback',
			// File-path of what to load when the regex returns true.
			'redirect' => '{e_PLUGIN}e107projects/callback.php',
		);

		// Projects - search page.
		$config['projects'] = array(
			// Matched against url, and if true, redirected to 'redirect' below.
			'regex'    => '^projects/?(.*)$',
			// Used by e107::url(); to create a url from the db table.
			'sef'      => 'projects',
			// File-path of what to load when the regex returns true.
			'redirect' => '{e_PLUGIN}e107projects/projects.php',
		);

		return $config;
	}

}