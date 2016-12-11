<?php

/**
 * @file
 * Helper functions for event callbacks.
 */


/**
 * Try to geocode user's location.
 *
 * @param $data
 */
function e107projects_user_settings_changed_location($data)
{
	if(!varset($data['ue']['user_plugin_e107projects_location'], false))
	{
		return;
	}

	e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.geocode.php');
	$geo = new e107projectsGeocode();

	if($geo->isGeocoded($data['ue']['user_plugin_e107projects_location']))
	{
		return;
	}

	$details = $geo->geocodeAddress($data['ue']['user_plugin_e107projects_location']);

	if(!$details)
	{
		return;
	}

	$db = e107::getDb();
	$tp = e107::getParser();

	$insert = array(
		'data' => array(
			'location_name' => $tp->toDB($data['ue']['user_plugin_e107projects_location']),
			'location_lat'  => $details['lat'],
			'location_lon'  => $details['lng'],
		),
	);

	$db->insert('e107projects_location', $insert, false);
}

/**
 * Send notification to User after a project has been submitted.
 *
 * @param $data
 */
function e107projects_user_project_submitted_notification($data)
{
	$user_id = (int) $data['project_author'];

	if($user_id > 0)
	{
		e107_require_once(e_PLUGIN . 'nodejs/nodejs.main.php');

		// TODO - more details?
		$subject = LAN_PLUGIN_E107PROJECTS_SUBMIT_SUCCESS_SUBJECT;
		$message = LAN_PLUGIN_E107PROJECTS_SUBMIT_SUCCESS_MESSAGE;

		$package = (object) array(
			'channel'  => 'nodejs_user_' . $user_id,
			'callback' => 'nodejsNotify',
			'type'     => 'notification_project',
			'data'     => array(
				'subject' => $subject,
				'body'    => $message,
			),
		);

		nodejs_enqueue_message($package);
	}
}

/**
 * Send broadcast notification after a project has been approved.
 *
 * @param $data
 *  Array contains project details from database.
 */
function e107projects_user_project_approved_notification($data)
{
	e107_require_once(e_PLUGIN . 'nodejs/nodejs.main.php');

	$tpl = e107::getTemplate('e107projects');
	$sc = e107::getScBatch('e107projects', true);
	$tp = e107::getParser();
	$db = e107::getDb();

	$subject = LAN_PLUGIN_E107PROJECTS_PROJECT_APPROVED_SUBJECT;
	$message = LAN_PLUGIN_E107PROJECTS_PROJECT_APPROVED_MESSAGE;

	$name = $data['project_user'] . '/' . $data['project_name'];
	$url = e107::url('e107projects', 'project', array(
		'user'       => $data['project_user'],
		'repository' => $data['project_name'],
	), array('full' => true));
	$repoLink = '<a href="' . $url . '" target="_self">' . $name . '</a>';

	$message = $tp->lanVars($message, array(
		'x' => '<strong>' . $data['project_user'] . '</strong>',
		'y' => $repoLink,
	));

	$avatar = $db->retrieve('e107projects_contributor', 'contributor_avatar', 'contributor_id = ' . (int) $data['project_author']);

	$sc->setVars(array(
		'avatar_url'    => $avatar,
		'avatar_width'  => 50,
		'avatar_height' => 50,
		'message'       => $message,
		'link'          => '',
	));

	$message = $tp->parseTemplate($tpl['notification'], true, $sc);

	$package = (object) array(
		'broadcast' => true,
		'channel'   => 'nodejs_notify',
		'callback'  => 'nodejsNotify',
		'type'      => 'notification_project',
		'data'      => array(
			'subject' => $subject,
			'body'    => $message,
		),
	);

	nodejs_enqueue_message($package);
}

/**
 * Send broadcast notification for displaying an OpenLayers Popup
 * after a project has been approved.
 *
 * @param $data
 *  Array contains project details from database.
 */
function e107projects_user_project_approved_notification_openlayers($data)
{
	// Helper functions.
	e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.common.php');

	$tp = e107::getParser();

	// Need to get User location.
	$location = e107projects_get_user_location($data['project_author']);

	// "[x] submitted a new project: [y]"
	$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_PROJECT_APPROVED_MESSAGE, array(
		'x' => '<strong>' . $data['project_user'] . '</strong>',
		'y' => '<strong>' . $data['project_user'] . '/' . $data['project_name'] . '</strong>',
	));

	$popup = array(
		'lat' => (int) varset($location['lat']),
		'lon' => (int) varset($location['lon']),
		'msg' => '<p>' . varset($location['name']) . '</p><small>' . $message . '</small>',
	);

	// OpenLayers Popup.
	e107projects_new_openlayers_popup($popup);
}

/**
 * Send a notification after a project has been rejected.
 *
 * @param $data
 *  Array contains project details from database.
 */
function e107projects_user_project_rejected_notification($data)
{
	$user_id = (int) $data['project_author'];

	if($user_id > 0)
	{
		e107_require_once(e_PLUGIN . 'nodejs/nodejs.main.php');

		// TODO - more details?
		$subject = LAN_PLUGIN_E107PROJECTS_PROJECT_REJECTED_SUBJECT;
		$message = LAN_PLUGIN_E107PROJECTS_PROJECT_REJECTED_MESSAGE;

		$package = (object) array(
			'channel'  => 'nodejs_user_' . $user_id,
			'callback' => 'nodejsNotify',
			'type'     => 'notification_project',
			'data'     => array(
				'subject' => $subject,
				'body'    => $message,
			),
		);

		nodejs_enqueue_message($package);
	}
}

/**
 * Send broadcast notification after a Push IPN has been arrived.
 *
 * @param $data
 */
function e107projects_webhook_push_notification($data)
{
	// [PLUGINS]/e107projects/languages/[LANGUAGE]/[LANGUAGE]_front.php
	e107::lan('e107projects', false, true);

	$tpl = e107::getTemplate('e107projects');
	$sc = e107::getScBatch('e107projects', true);
	$tp = e107::getParser();

	e107_require_once(e_PLUGIN . 'nodejs/nodejs.main.php');

	$sender = varset($data['sender'], false);
	$commits = varset($data['commits'], false);
	$repository = varset($data['repository'], false);

	if(!$sender || !$commits || !$repository)
	{
		return;
	}

	$count = count($commits);

	if($count > 1)
	{
		$count .= ' ' . LAN_E107PROJECTS_FRONT_02;
	}
	else
	{
		$count .= ' ' . LAN_E107PROJECTS_FRONT_29;
	}

	$url = e107::url('e107projects', 'project', array(
		'user'       => $repository['owner']['name'],
		'repository' => $repository['name'],
	), array('full' => true));
	$repoLink = '<a href="' . $url . '" target="_self">' . $repository['full_name'] . '</a>';

	$subject = LAN_PLUGIN_E107PROJECTS_WEBHOOK_PUSH_SUBJECT;
	$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_PUSH_MESSAGE, array(
		'x' => '<strong>' . varset($sender['login'], '') . '</strong>',
		'y' => '<strong>' . $count . '</strong>',
		'z' => $repoLink,
	));

	$sc->setVars(array(
		'avatar_url'    => varset($sender['avatar_url'], ''),
		'avatar_width'  => 50,
		'avatar_height' => 50,
		'message'       => $message,
		'link'          => '',
	));

	$markup = $tp->parseTemplate($tpl['notification'], true, $sc);

	$package = (object) array(
		'broadcast' => true,
		'channel'   => 'nodejs_notify',
		'callback'  => 'nodejsNotify',
		'type'      => 'notification_push',
		'data'      => array(
			'subject' => $subject,
			'body'    => $markup,
		),
	);

	nodejs_enqueue_message($package);
}

/**
 * Send broadcast notification for displaying OpenLayers Map Popup
 * with information about pushing.
 *
 * @param $data
 */
function e107projects_webhook_push_notification_openlayers($data)
{
	// [PLUGINS]/e107projects/languages/[LANGUAGE]/[LANGUAGE]_front.php
	e107::lan('e107projects', false, true);
	// Helper functions.
	e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.common.php');

	$tp = e107::getParser();
	$db = e107::getDb();

	$sender = varset($data['sender'], false);
	$commits = varset($data['commits'], false);
	$repository = varset($data['repository'], false);

	$count = count($commits);
	if($count > 1)
	{
		$count .= ' ' . LAN_E107PROJECTS_FRONT_02;
	}
	else
	{
		$count .= ' ' . LAN_E107PROJECTS_FRONT_29;
	}

	// Get User ID for location.
	$user_id = $db->retrieve('e107projects_contributor', 'contributor_id', 'contributor_name = "' . $tp->toDB($sender['login']) . '"');
	$user_id = (int) $user_id;

	// Get User location.
	$location = e107projects_get_user_location($user_id);

	// "[x] pushed [y] to: [z]"
	$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_PUSH_MESSAGE, array(
		'x' => '<strong>' . varset($sender['login'], '') . '</strong>',
		'y' => '<strong>' . $count . '</strong>',
		'z' => '<strong>' . $repository['full_name'] . '</strong>',
	));

	$popup = array(
		'lat' => (int) varset($location['lat']),
		'lon' => (int) varset($location['lon']),
		'msg' => '<p>' . varset($location['name']) . '</p><small>' . $message . '</small>',
	);

	// OpenLayers Popup.
	e107projects_new_openlayers_popup($popup);
}

/**
 * Send broadcast notification after a commit comment is created.
 *
 * @param $data
 */
function e107projects_webhook_commit_comment_notification($data)
{
	$action = varset($data['action'], false);
	$comment = varset($data['comment'], false);
	$user = varset($data['comment']['user'], false);
	$repository = varset($data['repository'], false);

	if(!$action || !$comment || !$user || !$repository)
	{
		return;
	}

	if($action != 'created')
	{
		return;
	}

	$tpl = e107::getTemplate('e107projects');
	$sc = e107::getScBatch('e107projects', true);
	$tp = e107::getParser();

	e107_require_once(e_PLUGIN . 'nodejs/nodejs.main.php');

	$repoURL = e107::url('e107projects', 'project', array(
		'user'       => $user['login'],
		'repository' => $repository['name'],
	), array('full' => true));

	$y = LAN_PLUGIN_E107PROJECTS_WEBHOOK_COMMIT_COMMENT_MESSAGE_Y;
	$subject = LAN_PLUGIN_E107PROJECTS_WEBHOOK_COMMIT_COMMENT_SUBJECT;
	$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_COMMIT_COMMENT_MESSAGE, array(
		'x' => '<strong>' . $user['login'] . '</strong>',
		'y' => '<a href="' . $comment['html_url'] . '" target="_blank">' . $y . '</a>',
		'z' => '<a href="' . $repoURL . '" target="_self">' . $repository['full_name'] . '</a>',
	));

	$sc->setVars(array(
		'avatar_url'    => $user['avatar_url'],
		'avatar_width'  => 50,
		'avatar_height' => 50,
		'message'       => $message,
		'link'          => '',
	));

	$markup = $tp->parseTemplate($tpl['notification'], true, $sc);

	$package = (object) array(
		'broadcast' => true,
		'channel'   => 'nodejs_notify',
		'callback'  => 'nodejsNotify',
		'type'      => 'notification_commit_comment',
		'data'      => array(
			'subject' => $subject,
			'body'    => $markup,
		),
	);

	nodejs_enqueue_message($package);
}

/**
 * Send broadcast notification for displaying OpenLayers Map Popup
 * after a commit comment is created.
 *
 * @param $data
 */
function e107projects_webhook_commit_comment_notification_openlayers($data)
{
	$action = varset($data['action'], false);
	$user = varset($data['comment']['user'], false);
	$repository = varset($data['repository'], false);

	if(!$action || !$user || !$repository)
	{
		return;
	}

	if($action != 'created')
	{
		return;
	}

	// Helper functions.
	e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.common.php');

	$tp = e107::getParser();
	$db = e107::getDb();

	// Get User ID for location.
	$user_id = $db->retrieve('e107projects_contributor', 'contributor_id', 'contributor_name = "' . $tp->toDB($user['login']) . '"');
	$user_id = (int) $user_id;

	// Get User location.
	$location = e107projects_get_user_location($user_id);

	$y = LAN_PLUGIN_E107PROJECTS_WEBHOOK_COMMIT_COMMENT_MESSAGE_Y;
	// "[x] commented on a [y] in [z] repository."
	$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_PUSH_MESSAGE, array(
		'x' => '<strong>' . varset($user['login'], '') . '</strong>',
		'y' => '<strong>' . $y . '</strong>',
		'z' => '<strong>' . $repository['full_name'] . '</strong>',
	));

	$popup = array(
		'lat' => (int) varset($location['lat']),
		'lon' => (int) varset($location['lon']),
		'msg' => '<p>' . varset($location['name']) . '</p><small>' . $message . '</small>',
	);

	// OpenLayers Popup.
	e107projects_new_openlayers_popup($popup);
}

/**
 * Send broadcast notification after a Repository is forked.
 *
 * @param $data
 */
function e107projects_webhook_fork_notification($data)
{
	$forkee = varset($data['forkee'], false);
	$repository = varset($data['repository'], false);

	if(!$forkee || !$repository)
	{
		return;
	}

	$tpl = e107::getTemplate('e107projects');
	$sc = e107::getScBatch('e107projects', true);
	$tp = e107::getParser();

	e107_require_once(e_PLUGIN . 'nodejs/nodejs.main.php');

	$repoURL = e107::url('e107projects', 'project', array(
		'user'       => $repository['owner']['login'],
		'repository' => $repository['name'],
	), array('full' => true));

	$subject = LAN_PLUGIN_E107PROJECTS_WEBHOOK_FORK_SUBJECT;
	$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_FORK_MESSAGE, array(
		'x' => '<strong>' . $forkee['owner']['login'] . '</strong>',
		'y' => '<a href="' . $repoURL . '" target="_self">' . $repository['full_name'] . '</a>',
	));

	$sc->setVars(array(
		'avatar_url'    => $forkee['owner']['avatar_url'],
		'avatar_width'  => 50,
		'avatar_height' => 50,
		'message'       => $message,
		'link'          => '',
	));

	$markup = $tp->parseTemplate($tpl['notification'], true, $sc);

	$package = (object) array(
		'broadcast' => true,
		'channel'   => 'nodejs_notify',
		'callback'  => 'nodejsNotify',
		'type'      => 'notification_fork',
		'data'      => array(
			'subject' => $subject,
			'body'    => $markup,
		),
	);

	nodejs_enqueue_message($package);
}

/**
 * Send broadcast notification for displaying OpenLayers Map Popup
 * after a Repository is forked.
 *
 * @param $data
 */
function e107projects_webhook_fork_notification_openlayers($data)
{
	$forkee = varset($data['forkee'], false);
	$repository = varset($data['repository'], false);

	if(!$forkee || !$repository)
	{
		return;
	}

	// Helper functions.
	e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.common.php');

	$tp = e107::getParser();
	$db = e107::getDb();

	// Get User ID for location.
	$user_id = $db->retrieve('e107projects_contributor', 'contributor_id', 'contributor_name = "' . $tp->toDB($forkee['owner']['login']) . '"');
	$user_id = (int) $user_id;

	if($user_id == 0)
	{
		return;
	}

	// Get User location.
	$location = e107projects_get_user_location($user_id);

	// "[x] forked [y]"
	$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_FORK_MESSAGE, array(
		'x' => '<strong>' . varset($forkee['owner']['login'], '') . '</strong>',
		'y' => '<strong>' . $repository['full_name'] . '</strong>',
	));

	$popup = array(
		'lat' => (int) varset($location['lat']),
		'lon' => (int) varset($location['lon']),
		'msg' => '<p>' . varset($location['name']) . '</p><small>' . $message . '</small>',
	);

	// OpenLayers Popup.
	e107projects_new_openlayers_popup($popup);
}

/**
 * Send broadcast notification after an Issue is assigned, unassigned,
 * labeled, unlabeled, opened, edited, milestoned, demilestoned, closed,
 * or reopened.
 *
 * @param $data
 */
function e107projects_webhook_issues_notification($data)
{
	$action = varset($data['action'], false);
	$issue = varset($data['issue'], false);
	$sender = varset($data['sender'], false);
	$repository = varset($data['repository'], false);

	if(!$action || !$issue || !$sender || !$repository)
	{
		return;
	}

	$tpl = e107::getTemplate('e107projects');
	$sc = e107::getScBatch('e107projects', true);
	$tp = e107::getParser();

	$subject = '';
	$message = '';

	$repoURL = e107::url('e107projects', 'project', array(
		'user'       => $repository['owner']['login'],
		'repository' => $repository['name'],
	), array('full' => true));

	switch($action)
	{
		case 'opened':
			$subject = LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_SUBJECT_01;
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_MESSAGE_01, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<a href="' . $issue['html_url'] . '" target="_blank">#' . $issue['number'] . '</a>',
				'z' => '<a href="' . $repoURL . '" target="_self">' . $repository['full_name'] . '</a>',
			));
			break;

		case 'reopened':
			$subject = LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_SUBJECT_02;
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_MESSAGE_02, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<a href="' . $issue['html_url'] . '" target="_blank">#' . $issue['number'] . '</a>',
				'z' => '<a href="' . $repoURL . '" target="_self">' . $repository['full_name'] . '</a>',
			));
			break;

		case 'closed':
			$subject = LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_SUBJECT_03;
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_MESSAGE_03, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<a href="' . $issue['html_url'] . '" target="_blank">#' . $issue['number'] . '</a>',
				'z' => '<a href="' . $repoURL . '" target="_self">' . $repository['full_name'] . '</a>',
			));
			break;

		case 'edited':
			$subject = LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_SUBJECT_04;
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_MESSAGE_04, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<a href="' . $issue['html_url'] . '" target="_blank">#' . $issue['number'] . '</a>',
				'z' => '<a href="' . $repoURL . '" target="_self">' . $repository['full_name'] . '</a>',
			));
			break;

		case 'labeled':
			$label = varset($data['label'], false);

			$subject = LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_SUBJECT_05;
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_MESSAGE_05, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<strong>' . $label['name'] . '</strong>',
				'z' => '<a href="' . $issue['html_url'] . '" target="_blank">#' . $issue['number'] . '</a>',
				'w' => '<a href="' . $repoURL . '" target="_self">' . $repository['full_name'] . '</a>',
			));
			break;

		case 'unlabeled':
			$label = varset($data['label'], false);

			$subject = LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_SUBJECT_06;
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_MESSAGE_06, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<strong>' . $label['name'] . '</strong>',
				'z' => '<a href="' . $issue['html_url'] . '" target="_blank">#' . $issue['number'] . '</a>',
				'w' => '<a href="' . $repoURL . '" target="_self">' . $repository['full_name'] . '</a>',
			));
			break;

		case 'milestoned':
			$subject = '';
			$message = '';
			break;

		case 'demilestoned':
			$subject = '';
			$message = '';
			break;

		case 'assigned':
			$assignee = varset($data['assignee'], false);

			$subject = LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_SUBJECT_07;
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_MESSAGE_07, array(
				'x' => '<strong>' . $assignee['login'] . '</strong>',
				'y' => '<strong>' . $sender['login'] . '</strong>',
				'z' => '<a href="' . $issue['html_url'] . '" target="_blank">#' . $issue['number'] . '</a>',
				'w' => '<a href="' . $repoURL . '" target="_self">' . $repository['full_name'] . '</a>',
			));
			break;

		case 'unassigned':
			$assignee = varset($data['assignee'], false);

			$subject = LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_SUBJECT_08;
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_MESSAGE_08, array(
				'x' => '<strong>' . $assignee['login'] . '</strong>',
				'y' => '<strong>' . $sender['login'] . '</strong>',
				'z' => '<a href="' . $issue['html_url'] . '" target="_blank">#' . $issue['number'] . '</a>',
				'w' => '<a href="' . $repoURL . '" target="_self">' . $repository['full_name'] . '</a>',
			));
			break;
	}

	if(empty($subject) || empty($message))
	{
		return;
	}

	$sc->setVars(array(
		'avatar_url'    => $sender['avatar_url'],
		'avatar_width'  => 50,
		'avatar_height' => 50,
		'message'       => $message,
		'link'          => '',
	));

	$markup = $tp->parseTemplate($tpl['notification'], true, $sc);

	e107_require_once(e_PLUGIN . 'nodejs/nodejs.main.php');

	$package = (object) array(
		'broadcast' => true,
		'channel'   => 'nodejs_notify',
		'callback'  => 'nodejsNotify',
		'type'      => 'notification_issues',
		'data'      => array(
			'subject' => $subject,
			'body'    => $markup,
		),
	);

	nodejs_enqueue_message($package);
}

/**
 * Send broadcast notification for displaying OpenLayers Map Popup
 * after an Issue is assigned, unassigned, labeled, unlabeled, opened,
 * edited, milestoned, demilestoned, closed, or reopened.
 *
 * @param $data
 */
function e107projects_webhook_issues_notification_openlayers($data)
{
	$action = varset($data['action'], false);
	$issue = varset($data['issue'], false);
	$sender = varset($data['sender'], false);
	$repository = varset($data['repository'], false);

	if(!$action || !$issue || !$sender || !$repository)
	{
		return;
	}

	// Helper functions.
	e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.common.php');

	$tp = e107::getParser();
	$db = e107::getDb();

	// Get User ID for location.
	$user_id = $db->retrieve('e107projects_contributor', 'contributor_id', 'contributor_name = "' . $tp->toDB($sender['login']) . '"');
	$user_id = (int) $user_id;

	if($user_id == 0)
	{
		return;
	}

	$message = '';

	switch($action)
	{
		case 'opened':
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_MESSAGE_01, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<strong>#' . $issue['number'] . '</strong>',
				'z' => '<strong>' . $repository['full_name'] . '</strong>',
			));
			break;

		case 'reopened':
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_MESSAGE_02, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<strong>#' . $issue['number'] . '</strong>',
				'z' => '<strong>' . $repository['full_name'] . '</strong>',
			));
			break;

		case 'closed':
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_MESSAGE_03, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<strong>#' . $issue['number'] . '</strong>',
				'z' => '<strong>' . $repository['full_name'] . '</strong>',
			));
			break;

		case 'edited':
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_MESSAGE_04, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<strong>#' . $issue['number'] . '</strong>',
				'z' => '<strong>' . $repository['full_name'] . '</strong>',
			));
			break;

		case 'labeled':
			$label = varset($data['label'], false);

			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_MESSAGE_05, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<strong>' . $label['name'] . '</strong>',
				'z' => '<strong>#' . $issue['number'] . '</strong>',
				'w' => '<strong>' . $repository['full_name'] . '</strong>',
			));
			break;

		case 'unlabeled':
			$label = varset($data['label'], false);

			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_MESSAGE_06, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<strong>' . $label['name'] . '</strong>',
				'z' => '<strong>#' . $issue['number'] . '</strong>',
				'w' => '<strong>' . $repository['full_name'] . '</strong>',
			));
			break;

		case 'milestoned':
			$message = '';
			break;

		case 'demilestoned':
			$message = '';
			break;

		case 'assigned':
			$assignee = varset($data['assignee'], false);

			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_MESSAGE_07, array(
				'x' => '<strong>' . $assignee['login'] . '</strong>',
				'y' => '<strong>' . $sender['login'] . '</strong>',
				'z' => '<strong>#' . $issue['number'] . '</strong>',
				'w' => '<strong>' . $repository['full_name'] . '</strong>',
			));
			break;

		case 'unassigned':
			$assignee = varset($data['assignee'], false);

			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUES_MESSAGE_08, array(
				'x' => '<strong>' . $assignee['login'] . '</strong>',
				'y' => '<strong>' . $sender['login'] . '</strong>',
				'z' => '<strong>#' . $issue['number'] . '</strong>',
				'w' => '<strong>' . $repository['full_name'] . '</strong>',
			));
			break;
	}

	if(empty($message))
	{
		return;
	}

	// Get User location.
	$location = e107projects_get_user_location($user_id);

	$popup = array(
		'lat' => (int) varset($location['lat']),
		'lon' => (int) varset($location['lon']),
		'msg' => '<p>' . varset($location['name']) . '</p><small>' . $message . '</small>',
	);

	// OpenLayers Popup.
	e107projects_new_openlayers_popup($popup);
}

/**
 * Any time a comment on an issue is created, edited, or deleted.
 *
 * @param $data
 */
function e107projects_webhook_issue_comment_notification($data)
{
	$action = varset($data['action'], false);
	$issue = varset($data['issue'], false);
	$comment = varset($data['comment'], false);
	$sender = varset($data['sender'], false);
	$repository = varset($data['repository'], false);

	if(!$action || !$issue || !$comment || !$sender || !$repository)
	{
		return;
	}

	$tpl = e107::getTemplate('e107projects');
	$sc = e107::getScBatch('e107projects', true);
	$tp = e107::getParser();

	$subject = '';
	$message = '';

	$repoURL = e107::url('e107projects', 'project', array(
		'user'       => $repository['owner']['login'],
		'repository' => $repository['name'],
	), array('full' => true));

	switch($action)
	{
		case 'created':
			$subject = LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUE_COMMENT_SUBJECT_01;
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUE_COMMENT_MESSAGE_01, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<a href="' . $issue['html_url'] . '" target="_blank">#' . $issue['number'] . '</a>',
				'z' => '<a href="' . $repoURL . '" target="_self">' . $repository['full_name'] . '</a>',
			));
			break;

		case 'edited':
			$subject = LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUE_COMMENT_SUBJECT_02;
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUE_COMMENT_MESSAGE_02, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<a href="' . $issue['html_url'] . '" target="_blank">#' . $issue['number'] . '</a>',
				'z' => '<a href="' . $repoURL . '" target="_self">' . $repository['full_name'] . '</a>',
			));
			break;

		case 'deleted':
			$subject = LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUE_COMMENT_SUBJECT_03;
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUE_COMMENT_MESSAGE_03, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<a href="' . $issue['html_url'] . '" target="_blank">#' . $issue['number'] . '</a>',
				'z' => '<a href="' . $repoURL . '" target="_self">' . $repository['full_name'] . '</a>',
			));
			break;
	}

	if(empty($subject) || empty($message))
	{
		return;
	}

	$sc->setVars(array(
		'avatar_url'    => $sender['avatar_url'],
		'avatar_width'  => 50,
		'avatar_height' => 50,
		'message'       => $message,
		'link'          => '',
	));

	$markup = $tp->parseTemplate($tpl['notification'], true, $sc);

	e107_require_once(e_PLUGIN . 'nodejs/nodejs.main.php');

	$package = (object) array(
		'broadcast' => true,
		'channel'   => 'nodejs_notify',
		'callback'  => 'nodejsNotify',
		'type'      => 'notification_issue_comment',
		'data'      => array(
			'subject' => $subject,
			'body'    => $markup,
		),
	);

	nodejs_enqueue_message($package);
}

/**
 * Any time a comment on an issue is created, edited, or deleted.
 *
 * @param $data
 */
function e107projects_webhook_issue_comment_notification_openlayers($data)
{
	$action = varset($data['action'], false);
	$issue = varset($data['issue'], false);
	$comment = varset($data['comment'], false);
	$sender = varset($data['sender'], false);
	$repository = varset($data['repository'], false);

	if(!$action || !$issue || !$comment || !$sender || !$repository)
	{
		return;
	}

	// Helper functions.
	e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.common.php');

	$tp = e107::getParser();
	$db = e107::getDb();

	// Get User ID for location.
	$user_id = $db->retrieve('e107projects_contributor', 'contributor_id', 'contributor_name = "' . $tp->toDB($sender['login']) . '"');
	$user_id = (int) $user_id;

	if($user_id == 0)
	{
		return;
	}

	$message = '';

	switch($action)
	{
		case 'created':
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUE_COMMENT_MESSAGE_01, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<strong>#' . $issue['number'] . '</strong>',
				'z' => '<strong>' . $repository['full_name'] . '</strong>',
			));
			break;

		case 'edited':
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUE_COMMENT_MESSAGE_02, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<strong>#' . $issue['number'] . '</strong>',
				'z' => '<strong>' . $repository['full_name'] . '</strong>',
			));
			break;

		case 'deleted':
			$message = $tp->lanVars(LAN_PLUGIN_E107PROJECTS_WEBHOOK_ISSUE_COMMENT_MESSAGE_03, array(
				'x' => '<strong>' . $sender['login'] . '</strong>',
				'y' => '<strong>#' . $issue['number'] . '</strong>',
				'z' => '<strong>' . $repository['full_name'] . '</strong>',
			));
			break;
	}

	if(empty($message))
	{
		return;
	}

	// Get User location.
	$location = e107projects_get_user_location($user_id);

	$popup = array(
		'lat' => (int) varset($location['lat']),
		'lon' => (int) varset($location['lon']),
		'msg' => '<p>' . varset($location['name']) . '</p><small>' . $message . '</small>',
	);

	// OpenLayers Popup.
	e107projects_new_openlayers_popup($popup);
}

/**
 * Send broadcast notification for displaying OpenLayers Map Popup.
 *
 * @param $data
 */
function e107projects_new_openlayers_popup($data)
{
	$lat = varset($data['lat'], 0);
	$lon = varset($data['lon'], 0);
	$msg = varset($data['msg'], '');

	if(empty($lat) || empty($lon) || empty($msg))
	{
		return;
	}

	$package = (object) array(
		'broadcast' => true,
		'channel'   => 'nodejs_notify',
		'callback'  => 'e107projectsMapPopup',
		'lat'       => $lat,
		'lon'       => $lon,
		'msg'       => $msg,
	);

	nodejs_enqueue_message($package);
}
