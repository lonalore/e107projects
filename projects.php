<?php

/**
 * @file
 * Class for rendering search page.
 */

if(!defined('e107_INIT'))
{
	require_once('../../class2.php');
}

if(!e107::isInstalled('e107projects'))
{
	e107::redirect(e_BASE . 'index.php');
}

// [PLUGINS]/e107projects/languages/[LANGUAGE]/[LANGUAGE]_front.php
e107::lan('e107projects', false, true);

// Load required class.
e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.search.php');

/*
$search = new e107ProjectsSearchManager();
$search->setCondition('name', '', 'STARTS_WITH');
$search->run();
*/



/**
 * Class e107ProjectsProjects.
 */
class e107ProjectsProjects extends e107ProjectsSearchManager
{

	/**
	 * Plugin preferences.
	 *
	 * @var array
	 */
	private $plugPrefs = array();

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->plugPrefs = e107::getPlugConfig('e107projects')->getPref();
	}

}


new e107ProjectsProjects();
