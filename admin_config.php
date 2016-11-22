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
		'checkboxes'     => array(
			'title'   => '',
			'type'    => null,
			'width'   => '5%',
			'forced'  => true,
			'thclass' => 'center',
			'class'   => 'center',
		),
		'project_id'     => array(
			'title'    => LAN_E107PROJECTS_ADMIN_MENU_05,
			'type'     => 'number',
			'width'    => '5%',
			'forced'   => false,
			'readonly' => true,
			'thclass'  => 'center',
			'class'    => 'center',
			'tab'      => 0,
		),
		'project_name'   => array(
			'title' => LAN_E107PROJECTS_ADMIN_MENU_06,
			'type'  => 'text',
			'data'  => 'str',
			'tab'   => 0,
		),
		'project_author' => array(
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
		'project_status' => array(
			'title'      => LAN_E107PROJECTS_ADMIN_MENU_08,
			'type'       => 'boolean',
			'writeParms' => 'label=yesno',
			'data'       => 'int',
			'inline'     => true,
			'filter'     => false,
			'validate'   => true,
			'thclass'    => 'center',
			'class'      => 'center',
			'tab'        => 0,
		),
		'options'        => array(
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
		'project_id',
		'project_name',
		'project_author',
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
		// If this doesn't return with TRUE, "admin_reservation_reservation_deleted" event won't be fired.
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
	protected $prefs = array();

	/**
	 * User defined init.
	 */
	public function init()
	{

	}

}


new e107projects_admin();

require_once(e_ADMIN . "auth.php");
e107::getAdminUI()->runPage();
require_once(e_ADMIN . "footer.php");
exit;
