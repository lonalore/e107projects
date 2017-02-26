<?php

/**
 * @file
 * Menu class for rendering Summary.
 */

if(!defined('e107_INIT'))
{
	exit;
}

if(!e107::isInstalled('e107projects'))
{
	e107::redirect(e_BASE . 'index.php');
}

// [PLUGINS]/e107projects/languages/[LANGUAGE]/[LANGUAGE]_front.php
e107::lan('e107projects', false, true);


/**
 * Class e107projects_summary_menu.
 */
class e107projects_summary_menu
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
	function __construct()
	{
		$this->plugPrefs = e107::getPlugConfig('e107projects')->getPref();
		$this->renderMenu();
	}

	/**
	 * Render menu contents.
	 */
	public function renderMenu()
	{
		$ns = e107::getRender();
		$tpl = e107::getTemplate('e107projects');
		$sc = e107::getScBatch('e107projects', true);
		$tp = e107::getParser();
		$db = e107::getDb();

		$contributors = $db->count('e107projects_contributor', '(contributor_gid)', '', false);
		$projects = $db->count('e107projects_project', '(project_id)', 'project_status = 1', false);
		$commits = $db->retrieve('e107projects_project', 'SUM(project_commits) AS commits', 'project_status = 1', false, null, false);

		$sc->setVars(array(
			'col_1' => $contributors,
			'col_2' => $projects,
			'col_3' => $commits,
		));
		
		$caption = '';
		$content = $tp->parseTemplate($tpl['summary_menu'], true, $sc);

		$ns->tablerender($caption, $content);
	}

}


new e107projects_summary_menu();
