<?php

/**
 * @file
 * Project submission page.
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

// Common functions.
e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.common.php');
// Load required class.
e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.github.php');


/**
 * Class e107projectsSubmit.
 */
class e107projectsSubmit
{

	/**
	 * Plugin preferences.
	 *
	 * @var
	 */
	private $plugPrefs;

	/**
	 * @var e107projectsGithub
	 */
	private $client;

	/**
	 * Github username of the current e107 user.
	 *
	 * @var
	 */
	private $username;

	/**
	 * Github repositories of the current e107 user.
	 *
	 * @var
	 */
	private $repositories;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		if(e107::getUser()->isGuest())
		{
			e107::redirect(SITEURL);
		}

		$this->plugPrefs = e107::getPlugConfig('e107projects')->getPref();
		$this->client = new e107projectsGithub();
		$this->username = $this->client->getGithubUsername(USERID);
		$this->repositories = $this->client->getUserRepositories($this->username);

		if(e_AJAX_REQUEST)
		{
			$valid = $this->validateRepository();

			if($valid)
			{
				$this->submitRepository();
			}
		}

		$this->renderPage();
	}

	/**
	 * Callback function to validate Ajax posted form.
	 */
	public function validateRepository()
	{
		return true;
	}

	/**
	 * Callback function to process Ajax posted form.
	 */
	public function submitRepository()
	{
		$ajax = e107::getAjax();

		$selector = '#submit-repository-' . $_POST['repository'];
		$contents = '<p class="text-success">' . LAN_E107PROJECTS_FRONT_11 . '</p>';

		$commands = array();
		$commands[] = $ajax->commandInsert($selector, 'html', $contents);

		$ajax->response($commands);
		exit;
	}

	/**
	 * Render page contents.
	 */
	public function renderPage()
	{
		$ns = e107::getRender();
		$tpl = e107::getTemplate('e107projects');
		$sc = e107::getScBatch('e107projects', true);
		$tp = e107::getParser();

		$caption = LAN_PLUGIN_E107PROJECTS_SUBMIT;
		$content = '';

		if(empty($this->repositories))
		{
			$content .= $tp->parseTemplate($tpl['submit']['empty'], true, $sc);
			$ns->tablerender($caption, $content);
			return;
		}

		$submittedRepositories = e107projects_get_user_submitted_projects(USERID);
		$submittedRepositories = array_keys($submittedRepositories);

		$content .= $tp->parseTemplate($tpl['submit']['pre'], true, $sc);
		foreach($this->repositories as $repository)
		{
			$sc->setVars(array(
				'repository' => $repository,
				'submitted'  => in_array($repository, $submittedRepositories),
			));
			$content .= $tp->parseTemplate($tpl['submit']['row'], true, $sc);
		}
		$content .= $tp->parseTemplate($tpl['submit']['post'], true, $sc);

		$ns->tablerender($caption, $content);
	}

}


if(!e_AJAX_REQUEST)
{
	require_once(HEADERF);
}

new e107projectsSubmit();

if(!e_AJAX_REQUEST)
{
	require_once(FOOTERF);
	exit;
}
