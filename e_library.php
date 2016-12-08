<?php

/**
 * @file
 * Provides information about external libraries.
 */


/**
 * Class e107projects_library.
 */
class e107projects_library
{

	/**
	 * Return information about external libraries.
	 */
	function config()
	{
		// OpenLayers v3.19.1.
		$libraries['openlayers'] = array(
			'name'              => 'OpenLayers',
			'vendor_url'        => 'https://openlayers.org/',
			'download_url'      => 'https://github.com/openlayers/ol3/archive/master.zip',
			'version_arguments' => array(
				'file'    => 'ol.js',
				// Version: v3.19.1
				'pattern' => '/Version:\s+v(3\.\d+\.\d+)/',
				'lines'   => 5,
			),
			'files'             => array(
				'css' => array(
					'ol.css' => array(
						'zone' => 2,
					),
				),
				'js'  => array(
					'ol.js' => array(
						'zone' => 2,
					),
				),
			),
		);

		// GeoComplete v1.6.4.
		$libraries['geocomplete'] = array(
			'name'              => 'GeoComplete',
			'vendor_url'        => 'https://github.com/ubilabs/geocomplete',
			'download_url'      => 'https://github.com/ubilabs/geocomplete/releases',
			'version_arguments' => array(
				'file'    => 'jquery.geocomplete.js',
				// jQuery Geocoding and Places Autocomplete Plugin - V 1.6.4
				'pattern' => '/(1\.\d+\.\d+)/',
				'lines'   => 3,
			),
			'files'             => array(
				'js' => array(
					'jquery.geocomplete.js' => array(
						'zone' => 2,
					),
				),
			),
			'variants'          => array(
				// All properties defined for 'minified' override top-level properties.
				'minified' => array(
					'files' => array(
						'js' => array(
							'jquery.geocomplete.min.js' => array(
								'zone' => 2,
							),
						),
					),
				),
			),
		);

		// OctIcons.
		$libraries['octicons'] = array(
			'name'             => 'OctIcons',
			'vendor_url'       => 'https://octicons.github.com/',
			'download_url'     => 'https://github.com/primer/octicons/archive/v5.0.1.zip',
			'version_callback' => 'octicons_version_callback',
			'files'            => array(
				'css' => array(
					'octicons.css' => array(
						'zone' => 2,
					),
				),
			),
			'variants'         => array(
				// All properties defined for 'minified' override top-level properties.
				'minified' => array(
					'files' => array(
						'css' => array(
							'octicons.min.css' => array(
								'zone' => 2,
							),
						),
					),
				),
			),
		);

		// jquery.nanoscroller.
		$libraries['jquery.nanoscroller'] = array(
			'name'             => 'nanoScroller.js',
			'vendor_url'       => 'https://jamesflorentino.github.io/nanoScrollerJS/',
			'download_url'     => 'https://jamesflorentino.github.io/nanoScrollerJS/',
			'version_arguments' => array(
				'file'    => 'jquery.nanoscroller.js',
				// nanoScrollerJS - v0.8.7 - 2015
				'pattern' => '/(0\.\d+\.\d+)/',
				'lines'   => 3,
			),
			'files'            => array(
				'css' => array(
					'nanoscroller.css' => array(
						'zone' => 2,
					),
				),
				'js'  => array(
					'jquery.nanoscroller.js' => array(
						'zone' => 2,
					),
				),
			),
			'variants'         => array(
				// All properties defined for 'minified' override top-level properties.
				'minified' => array(
					'files' => array(
						'css' => array(
							'nanoscroller.css' => array(
								'zone' => 2,
							),
						),
						'js'  => array(
							'jquery.nanoscroller.min.js' => array(
								'zone' => 2,
							),
						),
					),
				),
			),
		);

		return $libraries;
	}

	function octicons_version_callback()
	{
		return '5.0.1';
	}

}
