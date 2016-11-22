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

}
