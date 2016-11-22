<?php

/**
 * @file
 * Cron handler.
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class e107projects_cron.
 */
class e107projects_cron
{

	function config()
	{
		$cron = array();

		/*
		$cron[] = array(
			// Displayed in admin area.
			'name'        => '',
			// Name of the function which is defined below.
			'function'    => 'e107projects_',
			// Choose between: mail, user, content, notify, or backup.
			'category'    => 'content',
			// Displayed in admin area.
			'description' => '',
			'tab'         => '0 * * * *',
			'active'      => 1,
		);
		*/

		return $cron;
	}
	
	public function e107projects_()
	{
	}

}
