<?php

/**
 * @file
 * Render menu with contributions for project.
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

// Common functions.
e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.common.php');


/**
 * Class e107projects_project_releases_menu.
 */
class e107projects_project_releases_menu
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
		$ns = e107::getRender();
		$db = e107::getDb();
		$tp = e107::getParser();

		$caption = LAN_E107PROJECTS_FRONT_58;
		$content = '';

		$fullName = $this->user . '/' . $this->repo;
		$where = '';

		$where .= 'release_project_user = "' . $tp->toDB($this->user) . '" ';
		$where .= 'AND release_project_name = "' . $tp->toDB($this->repo) . '" ';
		$where .= 'ORDER BY release_published_at DESC ';

		$releases = $db->select('e107projects_release', '*', $where);

		if(empty($releases))
		{
			$content .= '<div class="panel panel-default github-buttons-container">';
			$content .= '<div class="panel-body text-center">';
			$content .= LAN_E107PROJECTS_FRONT_62;
			$content .= '</div>';
			$content .= '</div>';
			$ns->tablerender($caption, $content);
			return;
		}

		$content .= '<ul class="list-group">';
		while($release = $db->fetch())
		{
			$url = 'https://github.com/' . $fullName . '/releases/tag/' . $release['release_tag_name'];
			$content .= '<a class="list-group-item" href="' . $url . '" target="_blank">';
			$content .= '<strong>' . $release['release_tag_name'] . '</strong> ';
			if($release['release_prerelease'] == 1)
			{
				$content .= '<span class="badge">' . LAN_E107PROJECTS_FRONT_63 . '</span>';
			}
			$content .= '</a>';
		}
		$content .= '</ul>';

		$ns->tablerender($caption, $content);
	}

}


new e107projects_project_releases_menu();

