<?php

/**
 * @file
 * GitHub Webhook handler.
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
			echo 'It works!';
			exit;
		}

		$log = e107::getLog();
		$log->add('Webhook', $this->payload, E_LOG_INFORMATIVE, 'GITHUB');

		switch(strtolower($_SERVER['HTTP_X_GITHUB_EVENT']))
		{
			case 'ping':
				echo 'pong';
				break;

			// Any Git push to a Repository, including editing tags or branches.
			// Commits via API actions that update references are also counted.
			// This is the default event.
			case 'push':
				break;

			// Any time a Commit is commented on.
			case 'commit_comment':
				break;

			// Any time a Branch or Tag is created.
			case 'create':
				break;

			// Any time a Branch or Tag is deleted.
			case 'delete':
				break;

			// Any time a Repository has a new deployment created from the API.
			case 'deployment':
				break;

			// Any time a deployment for a Repository has a status update from
			// the API.
			case 'deployment_status':
				break;

			// Any time a Repository is forked.
			case 'fork':
				break;

			// Any time a Wiki page is updated.
			case 'gollum':
				break;

			// Any time a comment on an issue is created, edited, or deleted.
			// @see https://developer.github.com/v3/issues/comments/
			case 'issue_comment':
				break;

			// Any time an Issue is assigned, unassigned, labeled, unlabeled,
			// opened, edited, milestoned, demilestoned, closed, or reopened.
			case 'issues':
				break;

			// Any time a Label is created, edited, or deleted.
			case 'label':
				break;

			// Any time a User is added as a collaborator to a Repository.
			case 'member':
				break;

			// Any time a User is added or removed from a team. Organization
			// hooks only.
			case 'membership':
				break;

			// Any time a Milestone is created, closed, opened, edited, or
			// deleted.
			case 'milestone':
				break;

			// Any time a Pages site is built or results in a failed build.
			case 'page_build':
				break;

			// Any time a Repository changes from private to public.
			case 'public':
				break;

			// Any time a comment on a Pull Request's unified diff is created,
			// edited, or deleted (in the Files Changed tab).
			// @see https://developer.github.com/v3/pulls/comments/
			case 'pull_request_review_comment':
				break;

			// Any time a Pull Request Review is submitted.
			case 'pull_request_review':
				break;

			// Any time a Pull Request is assigned, unassigned, labeled,
			// unlabeled, opened, edited, closed, reopened, or synchronized
			// (updated due to a new push in the branch that the pull request is
			// tracking).
			case 'pull_request':
				break;

			// Any time a Repository is created, deleted, made public, or made
			// private.
			case 'repository':
				break;

			// Any time a Release is published in a Repository.
			case 'release':
				break;

			// Any time a Repository has a status update from the API.
			case 'status':
				break;

			// Any time a team is added or modified on a Repository.
			case 'team_add':
				break;

			// Any time a User stars a Repository.
			case 'watch':
				break;

			default:
				header('HTTP/1.0 404 Not Found');
				break;
		}

		exit;
	}

	/**
	 * Validate request.
	 *
	 * @return bool
	 */
	public function isValid()
	{
		if(!varset($_SERVER['HTTP_X_GITHUB_EVENT'], false))
		{
			// HTTP header 'X-GitHub-Event' is missing.
			return false;
		}

		if(!varset($this->plugPrefs['github_secret'], false))
		{
			// Missing secret key.
			return false;
		}

		if(!varset($_SERVER['HTTP_X_HUB_SIGNATURE'], false))
		{
			// HTTP header 'X-Hub-Signature' is missing.
			return false;
		}
		elseif(!extension_loaded('hash'))
		{
			// Missing 'hash' extension to check the secret code validity.
			return false;
		}

		list($algo, $hash) = explode('=', $_SERVER['HTTP_X_HUB_SIGNATURE'], 2) + array('', '');

		if(!in_array($algo, hash_algos(), true))
		{
			// Hash algorithm '$algo' is not supported.
			return false;
		}

		$rawPost = file_get_contents('php://input');

		if($hash !== hash_hmac($algo, $rawPost, $this->plugPrefs['github_secret']))
		{
			// Hook secret does not match.
			return false;
		}

		switch($_SERVER['HTTP_CONTENT_TYPE'])
		{
			case 'application/json':
				$json = file_get_contents('php://input');
				break;

			case 'application/x-www-form-urlencoded':
				$json = $_POST['payload'];
				break;

			default:
				return false;
				break;
		}

		// Payload structure depends on triggered event
		// https://developer.github.com/v3/activity/events/types/
		$this->payload = json_decode($json);

		if(!is_object($this->payload))
		{
			return false;
		}

		return true;
	}

}


new e107ProjectsCallback();
