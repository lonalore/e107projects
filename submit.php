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
	 * @var
	 */
	private $accessToken;

	/**
	 * @var
	 */
	private $organizations;

	/**
	 * Github repositories of the current e107 user.
	 *
	 * @var
	 */
	private $repositories = array();

	/**
	 * @var
	 */
	private $repository;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		if(e107::getUser()->isGuest())
		{
			e107::redirect(SITEURL);
		}

		// Try to get Access Token for current user.
		$accessToken = e107projects_get_access_token();

		// If no access token, we redirect user to Github authentication page.
		if(!$accessToken)
		{
			$hybridAuth = e107::getHybridAuth();
			$hybridAuth->getAdapter('Github')->login();
		}

		// Update Access Token on hooks are already existed in database.
		e107projects_update_access_token($accessToken);

		// Store Access Token.
		$this->accessToken = $accessToken;
		// Get plugin preferences.
		$this->plugPrefs = e107::getPlugConfig('e107projects')->getPref();
		// Get a Github Client.
		$this->client = new e107projectsGithub($this->accessToken);
		// Get the Github username for current user.
		$this->username = $this->client->getGithubUsername(USERID);
		// Get user organizations.
		$this->organizations = $this->client->getUserOrganizations($this->username);
		// Get repositories of the current user.
		$this->repositories = array_merge($this->repositories, $this->client->getUserRepositories($this->username));

		// Get repositories from organizations.
		foreach($this->organizations as $organization)
		{
			$orgRepos = $this->client->getUserRepositories($organization['login']);

			// Remove organization repository where no admin permission.
			foreach($orgRepos as $key => $orgRepo)
			{
				if(!varset($orgRepo['permissions']['admin'], false))
				{
					unset($orgRepos[$key]);
				}
			}

			// Get repositories of the organization.
			$this->repositories = array_merge($this->repositories, $orgRepos);
		}

		// Remove forked repositories from the list.
		foreach($this->repositories as $key => $repo)
		{
			if($repo['fork'] == true || $repo['private'] == true)
			{
				unset($this->repositories[$key]);
			}
		}

		// Ajax handlers will exit with JSON response, so no need to exit
		// before renderPage().
		if(e_AJAX_REQUEST)
		{
			$this->validateRepository();
			$this->submitRepository();
		}

		$this->renderPage();
	}

	/**
	 * Callback function to validate Ajax posted form.
	 */
	public function validateRepository()
	{
		$ajax = e107::getAjax();
		$commands = array();

		$inArray = false;
		foreach($this->repositories as $repository)
		{
			if($_POST['repository'] == $repository['id'])
			{
				$inArray = true;
				$this->repository = $repository;
			}
		}

		if(!$inArray)
		{
			$selector = '#submit-repository-' . $_POST['repository'];
			$contents = '<p class="text-danger">' . LAN_ERROR . '</p>';
			$commands[] = $ajax->commandInsert($selector, 'html', $contents);
		}

		if(!empty($commands))
		{
			$ajax->response($commands);
			exit;
		}
	}

	/**
	 * Callback function to process Ajax posted form.
	 */
	public function submitRepository()
	{
		$ajax = e107::getAjax();

		$commands = array();

		$selector = '#submit-repository-' . $_POST['repository'];
		$contents = '<p class="text-danger">' . LAN_ERROR . '</p>';

		$saved = e107projects_insert_project($_POST['repository'], USERID, $this->accessToken);
		if($saved)
		{
			$contents = '<p class="text-success">' . LAN_E107PROJECTS_FRONT_11 . '</p>';
		}

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
		$submittedKeys = array_keys($submittedRepositories);

		$content .= $tp->parseTemplate($tpl['submit']['pre'], true, $sc);
		foreach($this->repositories as $repository)
		{
			$sc->setVars(array(
				'repository' => $repository,
				'submitted'  => in_array($repository['name'], $submittedKeys),
				'status'     => (int) varset($submittedRepositories[$repository['name']]['project_status'], 0),
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
