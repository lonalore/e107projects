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

	/**
	 * @return array
	 */
	function config()
	{
		$cron = array();

		$cron[] = array(
			// Displayed in admin area.
			'name'        => 'Update projects',
			// Name of the function which is defined below.
			'function'    => 'e107projects_update_projects',
			// Choose between: mail, user, content, notify, or backup.
			'category'    => 'content',
			// Displayed in admin area.
			'description' => 'Update project details, releases, contributions, contributors... etc.',
			// Every hour.
			'tab'         => '* * * * *',
			// Activate it.
			'active'      => 1,
		);

		return $cron;
	}

	/**
	 * Update project details, releases, contributions, contributors... etc.
	 *
	 * @param int $limit
	 */
	public function e107projects_update_projects($limit = 1)
	{
		// Common functions.
		e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.common.php');

		$db = e107::getDb('cron_projects');

		// Get the oldest project.
		$db->select('e107projects_project', 'project_id', 'project_id >= 0 ORDER BY project_updated ASC LIMIT 0,' . $limit);

		while($project = $db->fetch())
		{
			e107projects_update_project($project['project_id']);
		}
	}

}
