<?php

/**
 * @file
 * Class for rendering contributors page.
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


/**
 * Class e107ProjectsContributors.
 */
class e107ProjectsContributors
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
	public function __construct($ajax = false)
	{
		$this->plugPrefs = e107::getPlugConfig('e107projects')->getPref();

		$this->renderPage();
	}

	/**
	 * Render pager.
	 *
	 * TODO:
	 * - use pager... infinite scroll?
	 * - search form
	 */
	public function renderPage()
	{
		$caption = LAN_E107PROJECTS_FRONT_74;
		$content = '';

		$ns = e107::getRender();
		$tp = e107::getParser();
		$db = e107::getDb();

		$db->select('e107projects_contributor', '*', 'contributor_gid > 0 ORDER BY contributor_name ASC');

		$content .= '<div class="row isotope-grid">';

		while($contributor = $db->fetch())
		{
			$user_id = $contributor['contributor_id'];
			$name = $contributor['contributor_name'];
			$url = '';

			if($user_id > 0)
			{
				$url = e107::getUrl()->create('user/profile/view', array(
					'name' => $name,
					'id' => $user_id,
				));
			}

			$avatar = '<img class="img-circle user-avatar" src="' . $contributor['contributor_avatar'] . '" width="75" height="75" alt=""/>';

			$content .= '<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 grid-item">';
			$content .= (!empty($url) ? '<a href="' . $url . '">' : '');
			$content .= $avatar;
			$content .= '<p class="name">' . $name . '</p>';
			$content .= (!empty($url) ? '</a>' : '');
			$content .= '</div>';
		}

		$content .= '</div>';

		$ns->tablerender($caption, $content);
	}

}


require_once(HEADERF);
new e107ProjectsContributors();
require_once(FOOTERF);
exit;
