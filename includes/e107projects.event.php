<?php

/**
 * @file
 * Helper functions for event callbacks.
 */


/**
 * Render and send NodeJS notification.
 *
 * @param $data
 */
function e107projects_webhook_push_notification($data)
{
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
	
	$url = e107::url('e107projects', 'project', array(
		'user'       => $repository['owner']['login'],
		'repository' => $repository['name'],
	), array('full' => true));
	$repoLink = '<a href="' . $url . '" target="_self">' . $repository['full_name'] . '</a>';

	$subject = LAN_PLUGIN_WEBHOOK_PUSH_SUBJECT;
	$message = $tp->lanVars(LAN_PLUGIN_WEBHOOK_PUSH_MESSAGE, array(
		'x' => '<strong>' . varset($sender['login'], '') . '</strong>',
		'y' => '<strong>' . count($commits) . '</strong>',
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
		'callback'  => 'e107projectsNotify',
		'type'      => 'webhookPush',
		'subject'   => $subject,
		'markup'    => $markup,
	);

	nodejs_enqueue_message($package);
}
