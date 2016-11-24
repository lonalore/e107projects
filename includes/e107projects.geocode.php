<?php

/**
 * @file
 * Helper class for geo-coding.
 */

// Load required functions.
e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.common.php');


/**
 * Class e107projectsGeocode.
 */
class e107projectsGeocode
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
	public function __construct()
	{
		$this->plugPrefs = e107::getPlugConfig('e107projects')->getPref();
	}

	/**
	 * Address is already geo-coded, and geo details are existed in database.
	 *
	 * @param string $address
	 *  Address to geocode.
	 *
	 * @return boolean
	 *  true - if address is already geo-coded.
	 *  false - no geo data in database for the address.
	 */
	public function isGeocoded($address)
	{
		$db = e107::getDb();
		$tp = e107::getParser();

		$count = $db->count('e107projects_location', 'location_name', 'location_name = "' . $tp->toDB($address) . '"', false);

		return (bool) $count;
	}

	/**
	 * Geocode address.
	 *
	 * @param string $address
	 *  Address to geocode.
	 *
	 * @return mixed
	 *  false - if unable to geocode address.
	 *  array - contains latitude, longitude, etc.
	 */
	public function geocodeAddress($address)
	{
		$query = array(
			'address'  => $address,
			'language' => defset('e_LAN', 'en'),
			'sensor'   => 'true',
		);
		$url = 'http://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($query);

		$response = e107projects_http_request($url);

		$matches = array();

		if(empty($response->error))
		{
			$data = json_decode($response->data);

			if($data->status == 'OK')
			{
				foreach($data->results as $result)
				{
					if(!empty($result->formatted_address))
					{
						$lat = $result->geometry->location->lat;
						$lng = $result->geometry->location->lng;

						$matches[] = array(
							'lat'  => $lat,
							'lng'  => $lng,
						);
					}
				}
			}
		}

		return varset($matches[0], false);
	}

}
