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

		return $libraries;
	}

}
