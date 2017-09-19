<?php

/**
 * @file
 * This file is loaded in the header of each page of your site.
 */

if(!defined('e107_INIT'))
{
	exit;
}

// [PLUGINS]/e107projects/languages/[LANGUAGE]/[LANGUAGE]_front.php
e107::lan('e107projects', false, true);


/**
 * Class e107projects_header.
 */
class e107projects_header
{

	/**
	 * Plugin preferences.
	 *
	 * @var array
	 */
	private $plugPrefs = array();

	/**
	 * @var bool
	 */
	private $needCSS = false;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->plugPrefs = e107::getPlugConfig('e107projects')->getPref();

		// Allow to use OctIcons globally.
		e107::library('load', 'octicons', 'minified');
		// Common behaviors.
		e107::js('e107projects', 'js/e107projects.common.js');

		// If user profile is incomplete, redirecting to usersettings.php.
		if(USER_AREA && defset('e_PAGE') != 'usersettings.php' && $this->incompleteUserAccount())
		{
			e107::getMessage()->add(LAN_E107PROJECTS_FRONT_05, E_MESSAGE_ERROR, true);
			e107::redirect('/usersettings.php');
		}

		// Load files for OpenLayers Map menu.
		if(USER_AREA && e107::getMenu()->isLoaded('e107projects_openlayers'))
		{
			$this->loadOpenLayers();
		}

		// Load files for Summary menu.
		if(USER_AREA && e107::getMenu()->isLoaded('e107projects_summary'))
		{
			$this->needCSS = true;
		}

		$menuGitRl = e107::getMenu()->isLoaded('e107projects_project_releases');
		$menuOrgRl = e107::getMenu()->isLoaded('e107projects_project_e107org');
		$menuContr = e107::getMenu()->isLoaded('e107projects_contributions');

		// Load files project related menus.
		if(USER_AREA && ($menuGitRl || $menuOrgRl || $menuContr))
		{
			$this->loadNanoScroller();
			$this->needCSS = true;
		}

		// Load GeoComplete files for user settings form.
		if(defset('e_PAGE') == 'usersettings.php')
		{
			$this->loadGeoComplete();
		}

		// Load Ajax helper JS for project submission and search page.
		if(defset('e_URL_LEGACY') == 'e107_plugins/e107projects/submit.php' || defset('e_URL_LEGACY') == 'e107_plugins/e107projects/projects.php'
		)
		{
			e107::js('e107projects', 'js/e107projects.submit.js');
			$this->needCSS = true;
		}

		// Load Isotope files for contributors page.
		if(defset('e_URL_LEGACY') == 'e107_plugins/e107projects/contributors.php')
		{
			$this->loadIsotope();
			$this->needCSS = true;
		}

		// Need CSS globally, because it contains styles for NodeJS notifications.
		$this->needCSS = true;

		if($this->needCSS === true)
		{
			e107::css('e107projects', 'css/styles.css');
		}
	}

	/**
	 * Check if account is incomplete.
	 *
	 * @return bool
	 */
	public function incompleteUserAccount()
	{
		$db = e107::getDb();
		$user = e107::getUser();

		if($user->isGuest())
		{
			return false;
		}

		$uid = (int) $user->getId();
		$location = $db->retrieve('user_extended', 'user_plugin_e107projects_location', 'user_extended_id = ' . $uid);

		return empty($location);
	}

	/**
	 * Load Isotope library.
	 */
	public function loadIsotope()
	{
		if(($library = e107::library('load', 'isotope', 'minified')) && !empty($library['loaded']))
		{
			e107::js('e107projects', 'js/e107projects.isotope.js');
		}
	}

	/**
	 * Load NanoScroller library.
	 */
	public function loadNanoScroller()
	{
		if(($library = e107::library('load', 'jquery.nanoscroller', 'minified')) && !empty($library['loaded']))
		{
			e107::js('e107projects', 'js/e107projects.nanoscroller.js');
		}
	}

	/**
	 * Load OpenLayers library.
	 */
	public function loadOpenLayers()
	{
		e107::library('load', 'openlayers');
		e107::library('load', 'ol3-panzoom');

		$this->needCSS = true;

		// FIXME - Move this to an async Ajax request?

		$db = e107::getDb();
		$db->gen("SELECT l.location_lat, l.location_lon, l.location_name, u.user_name, u.user_login, c.contributor_name FROM #user_extended AS ue 
			LEFT JOIN #e107projects_location AS l ON l.location_name = ue.user_plugin_e107projects_location
			LEFT JOIN #user AS u ON ue.user_extended_id = u.user_id
			LEFT JOIN #e107projects_contributor AS c ON ue.user_extended_id = c.contributor_id
			WHERE u.user_name != '' ");

		$markers = array();
		while($row = $db->fetch())
		{
			$key = $row['location_lat'] . '_' . $row['location_lon'];

			if(!isset($markers[$key]))
			{
				$markers[$key] = array(
					'location'     => array(
						'name' => $row['location_name'],
						'lat'  => $row['location_lat'],
						'lon'  => $row['location_lon'],
					),
					'contributors' => array(),
				);
			}

			$name = $row['user_name'];

			if(!empty($row['contributor_name']))
			{
				$name .= ' (' . $row['contributor_name'] . ')';
			}

			$markers[$key]['contributors'][] = array(
				'name' => $name,
			);
		}

		e107::js('settings', array(
			'e107projects' => array(
				'marker'  => SITEURL . e_PLUGIN . 'e107projects/images/marker.png',
				'markers' => $markers,
                'geojson' => SITEURL . e_PLUGIN . 'e107projects/js/countries.geojson',
			),
			'panZoom'      => array(
				'resources' => SITEURL . e_WEB . 'lib/ol3-panzoom/resources/',
			),
		));

		e107::js('e107projects', 'js/e107projects.openlayers.js');
	}

	/**
	 * Load GeoComplete library.
	 */
	public function loadGeoComplete()
	{
		$apiKey = varset($this->plugPrefs['google_places_api_key']);

		if(empty($apiKey))
		{
			return;
		}

		$query = array(
			'key'       => $apiKey,
			'language'  => defset('e_LAN', 'en'),
			'libraries' => 'places',
		);

		$url = 'https://maps.googleapis.com/maps/api/js?' . http_build_query($query);

		e107::js('url', $url, array('zone' => 2));

		if(($library = e107::library('load', 'geocomplete', 'minified')) && !empty($library['loaded']))
		{
			e107::js('e107projects', 'js/e107projects.geocomplete.js');
		}
	}

}


new e107projects_header();
