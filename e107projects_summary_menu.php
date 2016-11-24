<?php

/**
 * @file
 * Menu class for rendering Summary.
 */

if(!defined('e107_INIT'))
{
	exit;
}

if(!e107::isInstalled('e107projects'))
{
	e107::redirect(e_BASE . 'index.php');
}

// [PLUGINS]/e107projects/languages/[LANGUAGE]/[LANGUAGE]_front.php
e107::lan('e107projects', false, true);


/**
 * Class e107projects_summary_menu.
 */
class e107projects_summary_menu
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
	function __construct()
	{
		$this->plugPrefs = e107::getPlugConfig('e107projects')->getPref();
		$this->renderMenu();
	}

	/**
	 * Render menu contents.
	 */
	public function renderMenu()
	{
		$ns = e107::getRender();
		$tpl = e107::getTemplate('e107projects');
		$sc = e107::getScBatch('e107projects', true);
		$tp = e107::getParser();

		$sc->setVars(array(
			'col_1' => 1234567,
			'col_2' => 1234567,
			'col_3' => 1234567,
			'col_4' => 1234567,
		));
		
		$caption = '';
		$content = $tp->parseTemplate($tpl['summary_menu'], true, $sc);

		$ns->tablerender($caption, $content);
	}

}


new e107projects_summary_menu();
