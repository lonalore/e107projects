<?php

/**
 * @file
 * Shortcodes for "e107projects" plugin.
 */

if(!defined('e107_INIT'))
{
	exit;
}

// [PLUGINS]/e107projects/languages/[LANGUAGE]/[LANGUAGE]_front.php
e107::lan('e107projects', false, true);


/**
 * Class e107projects_shortcodes.
 */
class e107projects_shortcodes extends e_shortcode
{

	/**
	 * Constructor.
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Contents for first column in summary menu.
	 */
	public function sc_summary_menu_col_1()
	{
		$count = (int) $this->var['col_1'];
		$formatted = number_format($count);
		return '<strong>' . $formatted . '</strong><br/>' . LAN_E107PROJECTS_FRONT_01;
	}

	/**
	 * Contents for second column in summary menu.
	 */
	public function sc_summary_menu_col_2()
	{
		$count = (int) $this->var['col_2'];
		$formatted = number_format($count);
		return '<strong>' . $formatted . '</strong><br/>' . LAN_E107PROJECTS_FRONT_03;
	}

	/**
	 * Contents for third column in summary menu.
	 */
	public function sc_summary_menu_col_3()
	{
		$count = (int) $this->var['col_3'];
		$formatted = number_format($count);
		return '<strong>' . $formatted . '</strong><br/>' . LAN_E107PROJECTS_FRONT_02;
	}

	/**
	 * Contents for third column in summary menu.
	 */
	public function sc_summary_menu_col_4()
	{
		$count = (int) $this->var['col_4'];
		$formatted = number_format($count);
		return '<strong>' . $formatted . '</strong><br/>' . LAN_E107PROJECTS_FRONT_04;
	}

}
