<?php

/**
 * @file
 * Endpoint for Github Webhook URLs.
 */

if(!defined('e107_INIT'))
{
	require_once('../../class2.php');
}

if(!e107::isInstalled('e107projects'))
{
	exit;
}


/**
 * Class e107ProjectsCallback.
 */
class e107ProjectsCallback
{

	/**
	 * Plugin preferences.
	 *
	 * @var
	 */
	private $plugPrefs;

	/**
	 * @var
	 */
	private $payload;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->plugPrefs = e107::getPlugConfig('e107projects')->getPref();

		if(!$this->isValid())
		{
			exit;
		}

		$log = e107::getLog();
		$log->add('Webhook', $this->payload, E_LOG_INFORMATIVE, 'GITHUB');
	}

	/**
	 * Validate request.
	 *
	 * @return bool
	 */
	public function isValid()
	{

		if(!varset($_POST['payload'], false))
		{
			return false;
		}

		$this->payload = json_decode($_POST['payload'], true);

		if(!is_object($this->payload))
		{
			return false;
		}

		if(!varset($_SERVER['HTTP_X_HUB_SIGNATURE'], false))
		{
			return false;
		}

		if(!varset($this->plugPrefs['github_secret'], false))
		{
			return false;
		}

		$signature = 'sha1=' . hash_hmac('sha1', $_POST['payload'], $this->plugPrefs['github_secret'], false);

		if($signature !== $_SERVER['HTTP_X_HUB_SIGNATURE'])
		{
			return false;
		}

		return true;
	}

}


new e107ProjectsCallback();
