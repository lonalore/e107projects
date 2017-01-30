<?php

/**
 * @file
 * Render menu with e107.org releases.
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
 * Class e107projects_project_e107org_menu.
 */
class e107projects_project_e107org_menu
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

		$caption = LAN_E107PROJECTS_FRONT_66;
		$content = '';

		$where = 'or_project_user = "' . $tp->toDB($this->user) . '" AND or_project_name = "' . $tp->toDB($this->repo) . '"';
		$releases = $db->select('e107projects_e107org_release', '*', $where . ' ORDER BY or_date DESC');

		if(!empty($releases))
		{
			$content .= '<div class="nano">';
			$content .= '<div class="nano-content">';
			$content .= '<div class="list-group">';

			while($release = $db->fetch())
			{
				$content .= '<a class="list-group-item" href="' . $release['or_url'] . '" target="_blank">';
				$content .= '<strong>v' . $release['or_version'] . '</strong> ';

				if($release['or_compatibility'] == 2)
				{
					$content .= '<span class="label label-success">2.x</span>';
				}
				else
				{
					$content .= '<span class="label label-warning">1.x</span>';
				}

				$content .= '</a>';
			}

			$content .= '</div>';
			$content .= '</div>';
			$content .= '</div>';

			$ns->tablerender($caption, $content);
		}
		else
		{
			$where = 'project_user = "' . $tp->toDB($this->user) . '" AND project_name = "' . $tp->toDB($this->repo) . '"';
			$user_id = $db->retrieve('e107projects_project', 'project_author', $where);

			if($user_id > 0 && defset('USERID', 0) == $user_id)
			{
				$content .= '<div class="panel panel-default">';
				$content .= '<div class="panel-body text-center">';
				$content .= LAN_E107PROJECTS_FRONT_67;
				$content .= '<br/><br/>';
				$content .= '<a href="http://e107.org/developers" target="_blank" rel="nofollow" class="btn btn-primary btn-sm">' . LAN_E107PROJECTS_FRONT_68 . '</a>';
				$content .= '</div>';
				$content .= '</div>';

				$ns->tablerender($caption, $content);
			}
		}
	}

}


new e107projects_project_e107org_menu();

