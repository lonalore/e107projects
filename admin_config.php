<?php

/**
 * @file
 * Class installations to handle configuration forms on Admin UI.
 */

require_once('../../class2.php');

if(!e107::isInstalled('e107projects') || !getperms("P"))
{
	e107::redirect(e_BASE . 'index.php');
}

// [PLUGINS]/e107projects/languages/[LANGUAGE]/[LANGUAGE]_admin.php
e107::lan('e107projects', true, true);


/**
 * Class e107projects_admin.
 */
class e107projects_admin extends e_admin_dispatcher
{

	/**
	 * Required (set by child class).
	 *
	 * Controller map array in format.
	 * @code
	 *  'MODE' => array(
	 *      'controller' =>'CONTROLLER_CLASS_NAME',
	 *      'path' => 'CONTROLLER SCRIPT PATH',
	 *      'ui' => 'UI_CLASS', // extend of 'comments_admin_form_ui'
	 *      'uipath' => 'path/to/ui/',
	 *  );
	 * @endcode
	 *
	 * @var array
	 */
	protected $modes = array(
		'ajax'     => array(
			'controller' => 'e107projects_admin_ajax_ui',
		),
		'projects' => array(
			'controller' => 'e107projects_admin_projects_ui',
			'ui'         => 'e107projects_admin_projects_form_ui',
		),
		'main'     => array(
			'controller' => 'e107projects_admin_ui',
			'path'       => null,
		),
	);

	/**
	 * Optional (set by child class).
	 *
	 * Required for admin menu render. Format:
	 * @code
	 *  'mode/action' => array(
	 *      'caption' => 'Link title',
	 *      'perm' => '0',
	 *      'url' => '{e_PLUGIN}plugname/admin_config.php',
	 *      ...
	 *  );
	 * @endcode
	 *
	 * Note that 'perm' and 'userclass' restrictions are inherited from the $modes, $access and $perm, so you don't
	 * have to set that vars if you don't need any additional 'visual' control.
	 *
	 * All valid key-value pair (see e107::getNav()->admin function) are accepted.
	 *
	 * @var array
	 */
	protected $adminMenu = array(
		'projects/list' => array(
			'caption' => LAN_E107PROJECTS_ADMIN_MENU_02,
			'perm'    => 'P',
		),
		'opt1'          => array(
			'divider' => true,
		),
		'main/limits'    => array(
			'caption' => LAN_E107PROJECTS_ADMIN_MENU_33,
			'perm'    => 'P',
		),
		'opt2'          => array(
			'divider' => true,
		),
		'main/prefs'    => array(
			'caption' => LAN_E107PROJECTS_ADMIN_MENU_01,
			'perm'    => 'P',
		),
	);

	/**
	 * Optional (set by child class).
	 *
	 * @var string
	 */
	protected $menuTitle = LAN_PLUGIN_E107PROJECTS_NAME;

}


/**
 * Class e107projects_admin_ajax_ui.
 */
class e107projects_admin_ajax_ui extends e_admin_ui
{

	/**
	 * Initial function.
	 */
	public function init()
	{
		// Construct action string.
		$action = varset($_GET['mode']) . '/' . varset($_GET['action']);

		switch($action)
		{

		}
	}

}


/**
 * Class e107projects_admin_projects_ui.
 */
class e107projects_admin_projects_ui extends e_admin_ui
{

	/**
	 * Could be LAN constant (multi-language support).
	 *
	 * @var string plugin name
	 */
	protected $pluginTitle = LAN_PLUGIN_E107PROJECTS_NAME;

	/**
	 * @var string plugin name
	 */
	protected $pluginName = 'e107projects';

	/**
	 * Base event trigger name to be used. Leave blank for no trigger.
	 *
	 * @var string event name
	 */
	protected $eventName = 'e107projects_project';

	protected $table = "e107projects_project";

	protected $pid = "project_id";

	/**
	 * Default (db) limit value.
	 *
	 * @var integer
	 */
	protected $perPage = 0;

	/**
	 * @var boolean
	 */
	protected $batchDelete = true;

	/**
	 * @var string SQL order, false to disable order, null is default order
	 */
	protected $listOrder = "project_name ASC";

	protected $tabs = array(
		LAN_E107PROJECTS_ADMIN_MENU_03,
	);

	/**
	 * @var array UI field data
	 */
	protected $fields = array(
		'checkboxes'          => array(
			'title'   => '',
			'type'    => null,
			'width'   => '5%',
			'forced'  => true,
			'thclass' => 'center',
			'class'   => 'center',
		),
		'project_id'          => array(
			'title'    => LAN_E107PROJECTS_ADMIN_MENU_05,
			'type'     => 'number',
			'width'    => '5%',
			'forced'   => false,
			'readonly' => true,
			'thclass'  => 'center',
			'class'    => 'center',
			'tab'      => 0,
		),
		'project_author'      => array(
			'title'    => LAN_E107PROJECTS_ADMIN_MENU_07,
			'type'     => 'user',
			'inline'   => true,
			'filter'   => false,
			'validate' => true,
			'data'     => 'str',
			'width'    => 'auto',
			'thclass'  => 'center',
			'class'    => 'center',
			'tab'      => 0,
		),
		'project_user'        => array(
			'title' => LAN_E107PROJECTS_ADMIN_MENU_13,
			'type'  => 'text',
			'data'  => 'str',
			'tab'   => 0,
		),
		'project_name'        => array(
			'title' => LAN_E107PROJECTS_ADMIN_MENU_06,
			'type'  => 'text',
			'data'  => 'str',
			'tab'   => 0,
		),
		'project_description' => array(
			'title' => LAN_E107PROJECTS_ADMIN_MENU_14,
			'type'  => 'textarea',
			'data'  => 'str',
			'tab'   => 0,
		),
		'project_stars'       => array(
			'title' => LAN_E107PROJECTS_ADMIN_MENU_15,
			'type'  => 'text',
			'data'  => 'int',
			'tab'   => 0,
		),
		'project_commits'     => array(
			'title' => LAN_E107PROJECTS_ADMIN_MENU_16,
			'type'  => 'text',
			'data'  => 'int',
			'tab'   => 0,
		),
		'project_open_issues'     => array(
			'title' => LAN_E107PROJECTS_ADMIN_MENU_34,
			'type'  => 'text',
			'data'  => 'int',
			'tab'   => 0,
		),
		'project_watchers'     => array(
			'title' => LAN_E107PROJECTS_ADMIN_MENU_35,
			'type'  => 'text',
			'data'  => 'int',
			'tab'   => 0,
		),
		'project_forks'     => array(
			'title' => LAN_E107PROJECTS_ADMIN_MENU_36,
			'type'  => 'text',
			'data'  => 'int',
			'tab'   => 0,
		),
		'project_status'      => array(
			'title'      => LAN_E107PROJECTS_ADMIN_MENU_08,
			'type'       => 'dropdown',
			'data'       => 'int',
			'writeParms' => array(
				0 => LAN_E107PROJECTS_ADMIN_MENU_22,
				1 => LAN_E107PROJECTS_ADMIN_MENU_23,
				2 => LAN_E107PROJECTS_ADMIN_MENU_24,
			),
			'inline'     => true,
			'filter'     => true,
			'validate'   => true,
			'thclass'    => 'center',
			'class'      => 'center',
			'tab'        => 0,
		),
		'project_submitted'   => array(
			'title'      => LAN_E107PROJECTS_ADMIN_MENU_18,
			'type'       => 'datestamp',
			'data'       => 'int',
			'width'      => 'auto',
			'writeParms' => 'auto=1&type=datetime',
			'tab'        => 0,
		),
		'project_updated'     => array(
			'title'      => LAN_E107PROJECTS_ADMIN_MENU_19,
			'type'       => 'datestamp',
			'data'       => 'int',
			'width'      => 'auto',
			'writeParms' => 'auto=1&type=datetime',
			'tab'        => 0,
		),
		'project_readme'      => array(
			'title' => LAN_E107PROJECTS_ADMIN_MENU_21,
			'type'  => 'textarea',
			'data'  => 'str',
			'tab'   => 0,
		),
		'options'             => array(
			'type'    => null,
			'width'   => '10%',
			'forced'  => true,
			'thclass' => 'center last',
			'class'   => 'center',
			'sort'    => false,
		),
	);

	/**
	 * @var array default fields activated on List view
	 */
	protected $fieldpref = array(
		'checkboxes',
		// 'project_id',
		'project_user',
		'project_name',
		'project_author',
		'project_submitted',
		'project_updated',
		'project_status',
		'options',
	);

	/**
	 * User defined init.
	 */
	public function init()
	{

	}

	/**
	 * User defined pre-create logic, return false to prevent DB query execution.
	 *
	 * @param $new_data
	 *  Posted data.
	 * @param $old_data
	 *
	 * @return boolean
	 */
	public function beforeCreate($new_data, $old_data)
	{
		return $new_data;
	}

	/**
	 * User defined after-create logic.
	 *
	 * @param $new_data
	 *  Posted data.
	 * @param $old_data
	 * @param $id
	 */
	public function afterCreate($new_data, $old_data, $id)
	{
	}

	/**
	 * User defined pre-update logic, return false to prevent DB query execution.
	 *
	 * @param $new_data
	 *  Posted data.
	 * @param $old_data
	 * @return mixed
	 */
	public function beforeUpdate($new_data, $old_data)
	{
		return $new_data;
	}

	/**
	 * User defined after-update logic.
	 *
	 * @param $new_data
	 *  Posted data.
	 * @param $old_data
	 */
	public function afterUpdate($new_data, $old_data, $id)
	{
		if($old_data['project_status'] == 0 && $new_data['project_status'] == 1)
		{
			e107::getEvent()->trigger('e107projects_user_project_approved', $new_data);
		}
	}

	/**
	 * User defined pre-delete logic.
	 */
	public function beforeDelete($data, $id)
	{
		return true;
	}

	/**
	 * User defined after-delete logic.
	 */
	public function afterDelete($deleted_data, $id, $deleted_check)
	{
		// Common functions.
		e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.common.php');
		// Load required class.
		e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.github.php');

		$db = e107::getDb();
		$tp = e107::getParser();

		$user = $deleted_data['project_user'];
		$name = $deleted_data['project_name'];

		$db->delete('e107projects_release', 'release_project_id = ' . (int) $id);
		$db->delete('e107projects_contribution', 'project_id = ' . (int) $id);

		$db->select('e107projects_hook', '*', 'hook_project_user = "' . $tp->toDB($user) . '" AND hook_project_name = "' . $tp->toDB($name) . '" ');

		while($hook = $db->fetch())
		{
			// Get a Github Client.
			$client = new e107projectsGithub($hook['hook_access_token']);
			// Try to delete hook.
			$client->deleteHook($hook['hook_project_user'], $hook['hook_project_name'], $hook['hook_id']);
		}

		$db->delete('e107projects_hook', 'hook_project_user = "' . $tp->toDB($user) . '" AND hook_project_name = "' . $tp->toDB($name) . '" ');

		// If this doesn't return with TRUE, "admin_e107projects_project" event won't be fired.
		return true;
	}

}


/**
 * Class e107projects_admin_projects_form_ui.
 */
class e107projects_admin_projects_form_ui extends e_admin_form_ui
{

}


/**
 * Class e107projects_admin_ui.
 */
class e107projects_admin_ui extends e_admin_ui
{

	/**
	 * Could be LAN constant (multi-language support).
	 *
	 * @var string plugin name
	 */
	protected $pluginTitle = LAN_PLUGIN_E107PROJECTS_NAME;

	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	protected $pluginName = "e107projects";

	/**
	 * Example: array('0' => 'Tab label', '1' => 'Another label');
	 * Referenced from $prefs property per field - 'tab => xxx' where xxx is the tab key (identifier).
	 *
	 * @var array edit/create form tabs
	 */
	protected $preftabs = array(
		LAN_E107PROJECTS_ADMIN_MENU_01, // Settings.
	);

	/**
	 * Plugin Preference description array.
	 *
	 * @var array
	 */
	protected $prefs = array(
		'google_places_api_key' => array(
			'title' => LAN_E107PROJECTS_ADMIN_MENU_09,
			'help'  => LAN_E107PROJECTS_ADMIN_MENU_10,
			'type'  => 'text',
			'data'  => 'str',
			'tab'   => 0,
		),
		'github_secret'         => array(
			'title' => LAN_E107PROJECTS_ADMIN_MENU_20,
			'type'  => 'text',
			'data'  => 'str',
			'tab'   => 0,
		),
	);

	/**
	 * User defined init.
	 */
	public function init()
	{

	}

	public function limitsPage()
	{
		// Common functions.
		e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.common.php');
		// Load required class.
		e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.github.php');

		$ns = e107::getRender();
		$tp = e107::getParser();
		$db = e107::getDb('get_access_tokens');

		$db->gen('SELECT DISTINCT(h.hook_access_token), c.contributor_name FROM #e107projects_hook AS h
		LEFT JOIN #e107projects_project AS p ON h.hook_project_user = p.project_user AND h.hook_project_name = p.project_name
		LEFT JOIN #e107projects_contributor AS c ON p.project_author = c.contributor_id', false);

		$caption = '';
		$content = '';

		$content .= '<div class="responsive-table">';
		$content .= '<table class="table table-striped">';
		
		$content .= '<thead>';
		$content .= '<tr>';
		$content .= '<th>' . LAN_E107PROJECTS_FRONT_32 . '</th>';
		$content .= '<th>' . LAN_E107PROJECTS_FRONT_33 . '</th>';
		$content .= '<th>' . LAN_E107PROJECTS_FRONT_34 . '</th>';
		$content .= '<th>' . LAN_E107PROJECTS_FRONT_35 . '</th>';
		$content .= '<th>' . LAN_E107PROJECTS_FRONT_36 . '</th>';
		$content .= '</tr>';
		$content .= '</thead>';
		
		$content .= '<tbody>';
		while($row = $db->fetch())
		{
			$valid = e107projects_access_token_is_valid($row['hook_access_token']);

			$limit = 0;
			$rmnng = 0;
			$reset = '-';

			if($valid)
			{
				$client = new e107projectsGithub($row['hook_access_token']);
				$limits = $client->getRateLimits();

				$limit = $limits['rate']['limit'];
				$rmnng = $limits['rate']['remaining'];
				$reset = $tp->toDate($limits['rate']['reset'], '%H:%M:%S');
			}

			$content .= '<tr>';
			$content .= '<td>' . $row['contributor_name'] . '</td>';
			$content .= '<td>' . $row['hook_access_token'] . '</td>';
			$content .= '<td>' . $limit . '</td>';
			$content .= '<td>' . $rmnng . '</td>';
			$content .= '<td>' . $reset . '</td>';
			$content .= '</tr>';
		}
		$content .= '</tbody>';
		
		$content .= '</table>';
		$content .= '</div>';

		$ns->tablerender($caption, $content);
	}

}


new e107projects_admin();

require_once(e_ADMIN . "auth.php");
e107::getAdminUI()->runPage();
require_once(e_ADMIN . "footer.php");
exit;
