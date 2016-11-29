<?php

/**
 * @file
 *
 */

if(!defined('e107_INIT'))
{
	exit;
}

// [PLUGINS]/e107projects/languages/[LANGUAGE]/[LANGUAGE]_front.php
e107::lan('e107projects', false, true);

// Load required functions.
e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.common.php');


/**
 * Class e107projects_user.
 */
class e107projects_user
{

	/**
	 * Hook into user profile page.
	 *
	 * @param array $user
	 *  User details.
	 *
	 * @return array $rows
	 */
	function profile($user)
	{
		$rows = array();

		$rows[] = array(
			'label' => LAN_E107PROJECTS_FRONT_28,
			'text'  => e107projects_get_user_contributions($user['user_id']),
		);

		return $rows;
	}

	/**
	 * Hook into user settings page.
	 *
	 * @return array
	 */
	function fields()
	{
		$fields = array();

		return $fields;
	}

}