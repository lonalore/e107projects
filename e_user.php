<?php

/**
 * @file
 *
 */

if(!defined('e107_INIT'))
{
	exit;
}


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

		/*
		$rows[] = array(
			'label' => 'Submitted projects',
			'text'  => '[x]',
			'url'   => e_PLUGIN_ABS . 'e107projects/projects.php',
		);
		*/

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