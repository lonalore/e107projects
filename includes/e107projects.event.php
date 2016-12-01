<?php

/**
 * @file
 * Helper functions for event callbacks.
 */


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
			'callback'  => 'nodejsNotify',
			'type'      => 'notification_project',
			'data'      => array(
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
		'user'       => $repository['owner']['login'],
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
