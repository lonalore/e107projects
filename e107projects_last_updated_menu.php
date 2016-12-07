<?php

/**
 * @file
 * Render menu with last updated time for project.
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
 * Class e107projects_last_updated_menu.
 */
class e107projects_last_updated_menu
{

	/**
	 * Plugin preferences.
	 *
	 * @var array
	 */
	private $plugPrefs = array();

	private $user;

	private $repo;

	/**
	 * Constructor.
	 */
	function __construct()
	{
		$this->plugPrefs = e107::getPlugConfig('e107projects')->getPref();

		$this->user = varset($_GET['user'], false);
		$this->repo = varset($_GET['repository'], false);

		if($this->user && $this->repo)
		{
			$this->renderMenu();
		}
	}

	/**
	 * Render menu contents.
	 */
	public function renderMenu()
	{
		$db = e107::getDb();
		$ns = e107::getRender();
		$tp = e107::getParser();
		
		$caption = LAN_E107PROJECTS_FRONT_55;
		$content = '';

		$updated = $db->retrieve('e107projects_project', 'project_updated', 'project_user = "' . $tp->toDB($this->user) . '" AND project_name = "' . $tp->toDB($this->repo) . '"');

		$content .= '<div class="panel panel-default">';
		$content .= '<div class="panel-body text-center">';
		
		$content .= $tp->lanVars(LAN_E107PROJECTS_FRONT_54, array(
			'x' => $tp->toDate($updated, 'long'),
		));
		
		$content .= '</div>';
		$content .= '</div>';

		$ns->tablerender($caption, $content);
	}

}


new e107projects_last_updated_menu();

