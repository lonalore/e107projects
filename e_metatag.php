<?php

/**
 * @file
 * Metatag addon file.
 */


/**
 * Class e107projects_metatag.
 *
 * Usage: PLUGIN_metatag
 */
class e107projects_metatag
{

	/**
	 * Provides information about metatag handlers.
	 *
	 * @return array $config
	 *  An associative array whose keys are the event trigger names used by
	 *  Admin UIs.
	 *
	 *  $config[KEY]
	 *      Where KEY is the event trigger name used by Admin UI in that case
	 *      you want to add "Metatag" tab to your Create/Edit form.
	 * @See $eventName in class e_admin_ui.
	 *  $config[KEY]['name']
	 *      Human-readable name for this entity.
	 *  $config[KEY]['detect']
	 *      Callback function to implement logic for detecting entity path.
	 *      - If your callback function is a class::method, you have to
	 *        provide an array whose first element is the class name and the
	 *        second is the method.
	 *      - If your callback is a simple function, you have to provide a
	 *        string instead of an array.
	 *      - If your callback function returns with false, it means that
	 *        current path is not an entity path.
	 *      - If your callback function returns with true, it means that
	 *        current path is an entity path, and entity does not have custom
	 *        instances, so default meta tags will be loaded for the entity.
	 *      - If your callback function returns with a primary id (e.g. a News
	 *        ID), it means that current path is an entity path, and need to
	 *        load meta tags for a specific entity item.
	 *  $config[KEY]['load']
	 *      Callback function to load entity from database in case of
	 *      'detect' returns with ID, and $config[KEY]['token'] is provided.
	 *  $config[KEY]['file']
	 *      Path for the file, which contains $config[KEY]['detect'] function.
	 *  $config[KEY]['token']
	 *      An associative array with tokens can be used for this entity. The
	 *      key is the token name, and the value is an array with:
	 *      'help' - Contains a short description about the token.
	 *      'handler' - Callback function returns with the token's value. The
	 *          handler function's first parameter will be the return value of
	 *          $config[KEY]['load'].
	 *      'file' - Path to the file, which contains the handler function.
	 *  $config[KEY]['default']
	 *      Provides default meta tags for the entity. An associative array
	 *      whose keys are the meta tag's name, and the value is the value of
	 *      the meta tag. These default meta tags will override the top level,
	 *      global meta tags.
	 *  $config[KEY]['tab']
	 *      Set to false if admin_ui has no tabs.
	 */
	public function config()
	{
		$config = array();

		// Project search.
		$config['e107projects_projects'] = array(
			'name'   => LAN_PLUGIN_E107PROJECTS_META_01,
			'detect' => 'e107projects_meta_project_search_detect',
			'file'   => '{e_PLUGIN}e107projects/includes/e107projects.metatag.php',
		);

		// Project.
		$config['e107projects_project'] = array(
			'name'    => LAN_PLUGIN_E107PROJECTS_META_02,
			'detect'  => 'e107projects_meta_project_detect',
			'load'    => 'e107projects_meta_project_load',
			'file'    => '{e_PLUGIN}e107projects/includes/e107projects.metatag.php',
			'token'   => array(
				'project:title'       => array(
					'help'    => LAN_PLUGIN_E107PROJECTS_META_03,
					'handler' => 'e107projects_meta_token_project_title',
					'file'    => '{e_PLUGIN}e107projects/includes/e107projects.metatag.php',
				),
				'project:description' => array(
					'help'    => LAN_PLUGIN_E107PROJECTS_META_04,
					'handler' => 'e107projects_meta_token_project_description',
					'file'    => '{e_PLUGIN}e107projects/includes/e107projects.metatag.php',
				),
			),
			'default' => array(
				'title'          => '{project:title} - {site:name}',
				'og:title'       => '{project:title} - {site:name}',
				'description'    => '{project:description}',
				'og:description' => '{project:description}',
			),
		);

		return $config;
	}

}
