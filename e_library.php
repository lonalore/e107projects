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
				'js' => array(
					'ol.js' => array(
						'zone' => 2,
					),
				),
			),
		);

		return $libraries;
	}

}
