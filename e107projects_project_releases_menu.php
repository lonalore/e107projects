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

		$where = 'project_user = "' . $tp->toDB($this->user) . '" AND project_name = "' . $tp->toDB($this->repo) . '"';
		$project = $db->retrieve('e107projects_project', '*', $where);

		$where = 'release_project_user = "' . $tp->toDB($this->user) . '" ';
		$where .= 'AND release_project_name = "' . $tp->toDB($this->repo) . '" ';
		$where .= 'ORDER BY release_published_at DESC ';

		$releases = $db->select('e107projects_release', '*', $where);

		$content .= '<div class="nano">';
		$content .= '<div class="nano-content">';
		$content .= '<div class="list-group">';

		// Master.
		$url = 'https://github.com/' . $fullName . '/archive/' . $project['project_default_branch'] . '.zip';
		$content .= '<a class="list-group-item" href="' . $url . '" target="_blank">';
		$content .= '<strong>' . $project['project_default_branch'] . '</strong> <span class="small">(zip)</span>';
		$content .= '<span class="label label-danger">' . LAN_E107PROJECTS_FRONT_64 . '</span>';
		$content .= '</a>';

		if(!empty($releases))
		{
			while($release = $db->fetch())
			{
				$url = 'https://github.com/' . $fullName . '/releases/tag/' . $release['release_tag_name'];
				$content .= '<a class="list-group-item" href="' . $url . '" target="_blank">';
				$content .= '<strong>' . $release['release_tag_name'] . '</strong> ';

				if($release['release_prerelease'] == 1)
				{
					$content .= '<span class="label label-warning">' . LAN_E107PROJECTS_FRONT_63 . '</span>';
				}
				else
				{
					// $content .= '<span class="label label-success">' . LAN_E107PROJECTS_FRONT_65 . '</span>';
				}

				$content .= '</a>';
			}
		}

		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';

		$ns->tablerender($caption, $content);
	}

}


new e107projects_project_releases_menu();

