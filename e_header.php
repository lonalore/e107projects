<?php

/**
 * @file
 * This file is loaded in the header of each page of your site.
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class e107projects_header.
 */
class e107projects_header
{

	private $needCSS = false;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		if(USER_AREA && e107::getMenu()->isLoaded('e107projects_openlayers'))
		{
			$this->loadOpenLayers();
		}

		if(USER_AREA && e107::getMenu()->isLoaded('e107projects_summary'))
		{
			$this->needCSS = true;
		}

		if($this->needCSS === true)
		{
			e107::css('e107projects', 'css/styles.css');
		}
	}

	public function loadOpenLayers()
	{
		if(($library = e107::library('load', 'openlayers')) && !empty($library['loaded']))
		{
			e107::js('e107projects', 'js/e107projects.openlayers.js');
			$this->needCSS = true;
		}
	}

}


new e107projects_header();
