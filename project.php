<?php

/**
 * @file
 * Project page.
 */

if(!defined('e107_INIT'))
{
	require_once('../../class2.php');
}

if(!e107::isInstalled('e107projects'))
{
	e107::redirect(SITEURL);
}

// [PLUGINS]/e107projects/languages/[LANGUAGE]/[LANGUAGE]_front.php
e107::lan('e107projects', false, true);


/**
 * Class e107ProjectsProject.
 */
class e107ProjectsProject
{

	/**
	 * Plugin preferences.
	 *
	 * @var array
	 */
	private $plugPrefs = array();

	/**
	 * @var array
	 */
	private $repository;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->plugPrefs = e107::getPlugConfig('e107projects')->getPref();

		$db = e107::getDb();
		$tp = e107::getParser();

		$getUser = varset($_GET['user'], '');
		$getRepo = varset($_GET['repository'], '');

		$user = $tp->toDB($getUser);
		$repo = $tp->toDB($getRepo);

		$this->repository = $db->retrieve('e107projects_project', '*', 'project_user = "' . $user . '" AND project_name = "' . $repo . '" ');

		if(!isset($this->repository['project_id']))
		{
			e107::redirect('/');
		}

		if($this->repository['project_status'] != 1)
		{
			e107::redirect('/');
		}

		$this->loadAssets();
		$this->renderPage();
	}

	/**
	 * Load required JS/CSS files.
	 */
	public function loadAssets()
	{
		// "Place this tag in your head or just before your close body tag."
		// Header is already sent, so we can add this to the footer.
		// e107::js('footer', 'https://buttons.github.io/buttons.js');
	}

	/**
	 * Render project page.
	 */
	public function renderPage()
	{
		$ns = e107::getRender();
		$tpl = e107::getTemplate('e107projects');
		$sc = e107::getScBatch('e107projects', true);
		$tp = e107::getParser();

		$caption = $this->repository['project_name'];
		$content = '';

		$sc->setVars(array(
			'repository' => $this->repository,
		));

		$content .= $tp->parseTemplate($tpl['project'], true, $sc);

		$ns->tablerender($caption, $content);
	}

}


require_once(HEADERF);
new e107ProjectsProject();
require_once(FOOTERF);
exit;
