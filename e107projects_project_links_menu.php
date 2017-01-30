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
 * Class e107projects_project_links_menu.
 */
class e107projects_project_links_menu
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

		$where = 'project_user = "' . $tp->toDB($this->user) . '" AND project_name = "' . $tp->toDB($this->repo) . '"';
		$project = $db->retrieve('e107projects_project', '*', $where);

		if(empty($project))
		{
			return;
		}

		$fullName = $this->user . '/' . $this->repo;

		$caption = LAN_E107PROJECTS_FRONT_61;
		$content = '';

		$content .= '<div class="list-group">';

		// Github repository.
		$repoIcon = '<i class="octicon octicon-database" aria-hidden="true"></i>';
		$repoURL = 'https://github.com/' . $fullName;

		$content .= '<a class="list-group-item" href="' . $repoURL . '" target="_blank">';
		$content .= $repoIcon . ' <strong>' . LAN_E107PROJECTS_FRONT_75 . '</strong> ';
		// $content .= '<span class="badge">' . $project['project_open_issues'] . '</span>';
		$content .= '</a>';

		// Open Issues.
		$issuesIcon = '<i class="octicon octicon-issue-opened" aria-hidden="true"></i>';
		$issuesURL = 'https://github.com/' . $fullName . '/issues';

		$content .= '<a class="list-group-item" href="' . $issuesURL . '" target="_blank">';
		$content .= $issuesIcon . ' <strong>' . LAN_E107PROJECTS_FRONT_57 . '</strong> ';
		$content .= '<span class="badge">' . $project['project_open_issues'] . '</span>';
		$content .= '</a>';

		// Stargazers.
		$starIcon = '<i class="octicon octicon-star" aria-hidden="true"></i>';
		$starURL = 'https://github.com/' . $fullName . '/stargazers';

		$content .= '<a class="list-group-item" href="' . $starURL . '" target="_blank">';
		$content .= $starIcon . ' <strong>' . LAN_E107PROJECTS_FRONT_56 . '</strong> ';
		$content .= '<span class="badge">' . $project['project_stars'] . '</span>';
		$content .= '</a>';

		// Watchers.
		$watchIcon = '<i class="octicon octicon-eye" aria-hidden="true"></i>';
		$watchURL = 'https://github.com/' . $fullName . '/watchers';

		$content .= '<a class="list-group-item" href="' . $watchURL . '" target="_blank">';
		$content .= $watchIcon . ' <strong>' . LAN_E107PROJECTS_FRONT_59 . '</strong> ';
		$content .= '<span class="badge">' . $project['project_watchers'] . '</span>';
		$content .= '</a>';

		// Forks.
		$forksIcon = '<i class="octicon octicon-repo-forked" aria-hidden="true"></i>';
		$forksURL = 'https://github.com/' . $fullName . '/network';

		$content .= '<a class="list-group-item" href="' . $forksURL . '" target="_blank">';
		$content .= $forksIcon . ' <strong>' . LAN_E107PROJECTS_FRONT_60 . '</strong> ';
		$content .= '<span class="badge">' . $project['project_forks'] . '</span>';
		$content .= '</a>';
		
		$content .= '</div>';

		$ns->tablerender($caption, $content);
	}

}


new e107projects_project_links_menu();

