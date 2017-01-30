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
 * Class e107projects_contributions_menu.
 */
class e107projects_contributions_menu
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

		$contributions = e107projects_get_contributions($this->user, $this->repo);

		$caption = LAN_E107PROJECTS_FRONT_53;
		$content = '';

		$content .= '<div class="nano">';
		$content .= '<div class="nano-content">';
		$content .= '<div class="list-group">';

		foreach($contributions as $contribution)
		{
			$user_id = $contribution['contributor_id'];
			$name = $contribution['contributor_name'];
			$count = $contribution['contributions'];
			$url = '#';
			
			if($user_id > 0)
			{
				$url = e107::getUrl()->create('user/profile/view', array(
					'name' => $name,
					'id' => $user_id,
				));
			}

			$avatar = '<img class="img-circle user-avatar" src="' . $contribution['contributor_avatar'] . '" width="18" height="18" alt=""/>';
			
			$content .= '<a class="list-group-item' . ($url == '#' ? ' no-link' : '') . '" href="' . $url . '">';
			$content .= $avatar . ' <strong>' . $name . '</strong> ';
			$content .= '<span class="badge">' . $count . '</span>';
			$content .= '</a>';
		}

		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';

		$ns->tablerender($caption, $content);
	}

}


new e107projects_contributions_menu();

