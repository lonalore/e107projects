<?php

/**
 * @file
 * Implements e_notify.php.
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class e107projects_notify.
 */
class e107projects_notify extends notify
{

	/**
	 * Provides information array about notifications.
	 *
	 * @return array
	 */
	function config()
	{
		$config = array();

		$config[] = array(
			'name'     => LAN_PLUGIN_E107PROJECTS_NOTIFY_01,
			'function' => 'e107projects_user_project_submitted',
			'category' => LAN_PLUGIN_E107PROJECTS_NOTIFY_02,
		);

		return $config;
	}

	/**
	 * Callback function to send notification after triggering
	 * "e107projects_user_project_submitted" event.
	 *
	 * @param array $data
	 *  Associative array contains project details.
	 */
	function e107projects_user_project_submitted($data)
	{
		$tp = e107::getParser();

		$subject = LAN_PLUGIN_E107PROJECTS_NOTIFY_01;
		$message = '';

		$message .= $tp->lanVars(LAN_PLUGIN_E107PROJECTS_NOTIFY_03, array(
			'x' => varset($data['project_user'], 'TEST'),
			'y' => varset($data['project_name'], 'TEST'),
		));

		$this->send('e107projects_user_project_submitted', $subject, $message);
	}

}