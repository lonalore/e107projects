<?php

/**
 * @file
 * Addon file to display help block on Admin UI.
 */

if(!defined('e107_INIT'))
{
	exit;
}

// [PLUGINS]/e107projects/languages/[LANGUAGE]/[LANGUAGE]_admin.php
e107::lan('e107projects', true, true);


/**
 * Class e107projects_help.
 */
class e107projects_help
{

	/**
	 * @var mixed
	 */
	private $action;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->action = varset($_GET['action'], '');
		$this->renderHelpBlock();
	}

	/**
	 * Render contents.
	 */
	public function renderHelpBlock()
	{
		$caption = LAN_E107PROJECTS_ADMIN_MENU_25;
		$content = '';

		// Load Github Client class.
		e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.github.php');

		$client = new e107projectsGithub();
		$limits = $client->getRateLimits();

		$content .=  '<p><strong>' . LAN_E107PROJECTS_ADMIN_MENU_30 . '</strong></p>';
		$content .=  '<div>' . LAN_E107PROJECTS_ADMIN_MENU_26 . ' ' . $limits['rate']['limit'] . '</div>';
		$content .=  '<div>' . LAN_E107PROJECTS_ADMIN_MENU_27 . ' ' . $limits['rate']['remaining'] . '</div>';
		$content .=  '<div>' . LAN_E107PROJECTS_ADMIN_MENU_28 . ' ' . date('H:i:s', $limits['rate']['reset']) . '</div>';
		
		e107::getRender()->tablerender($caption, $content);
	}

}


new e107projects_help();
