<?php

/**
 * @file
 * Describes Extended User Fields to display them on the global notification settings
 * form provided by "nodejs_notify" plugin.
 */


/**
 * Class e107projects_nodejs_notify.
 */
class e107projects_nodejs_notify
{

	/**
	 * NodeJS Notify configuration.
	 *
	 * @return array
	 *    The list of configuration items.
	 */
	public function config()
	{
		$items = array();

		// "Any time a new Project is submitted".
		$items[] = array(
			// Use global language file.
			'label'       => LAN_PLUGIN_E107PROJECTS_NODEJS_NOTIFY_11,
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_project
			'field_alert' => 'notification_project',
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_project_sound
			'field_sound' => 'notification_project_sound',
		);

		// "Any Git push to a Repository".
		$items[] = array(
			// Use global language file.
			'label'       => LAN_PLUGIN_E107PROJECTS_NODEJS_NOTIFY_02,
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_push
			'field_alert' => 'notification_push',
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_push_sound
			'field_sound' => 'notification_push_sound',
		);

		// "Any time a Commit is commented on".
		$items[] = array(
			// Use global language file.
			'label'       => LAN_PLUGIN_E107PROJECTS_NODEJS_NOTIFY_03,
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_commit_comment
			'field_alert' => 'notification_commit_comment',
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_commit_comment_sound
			'field_sound' => 'notification_commit_comment_sound',
		);

		// "Any time a Repository is forked".
		$items[] = array(
			// Use global language file.
			'label'       => LAN_PLUGIN_E107PROJECTS_NODEJS_NOTIFY_04,
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_fork
			'field_alert' => 'notification_fork',
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_fork_sound
			'field_sound' => 'notification_fork_sound',
		);

		// "Any time an Issue is assigned, unassigned, labeled, unlabeled, opened, edited,
		// milestoned, demilestoned, closed, or reopened".
		$items[] = array(
			// Use global language file.
			'label'       => LAN_PLUGIN_E107PROJECTS_NODEJS_NOTIFY_06,
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_issues
			'field_alert' => 'notification_issues',
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_issues_sound
			'field_sound' => 'notification_issues_sound',
		);

		// "Any time a comment on an issue is created, edited, or deleted".
		$items[] = array(
			// Use global language file.
			'label'       => LAN_PLUGIN_E107PROJECTS_NODEJS_NOTIFY_05,
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_issue_comment
			'field_alert' => 'notification_issue_comment',
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_issue_comment_sound
			'field_sound' => 'notification_issue_comment_sound',
		);

		// "Any time a Milestone is created, closed, opened, edited, or deleted".
		$items[] = array(
			// Use global language file.
			'label'       => LAN_PLUGIN_E107PROJECTS_NODEJS_NOTIFY_07,
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_milestone
			'field_alert' => 'notification_milestone',
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_milestone_sound
			'field_sound' => 'notification_milestone_sound',
		);

		// "Any time a Pull Request is assigned, unassigned, labeled, unlabeled, opened,
		// edited, closed, reopened, or synchronized".
		$items[] = array(
			// Use global language file.
			'label'       => LAN_PLUGIN_E107PROJECTS_NODEJS_NOTIFY_08,
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_pull_request
			'field_alert' => 'notification_pull_request',
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_pull_request_sound
			'field_sound' => 'notification_pull_request_sound',
		);

		// "Any time a Release is published in a Repository".
		$items[] = array(
			// Use global language file.
			'label'       => LAN_PLUGIN_E107PROJECTS_NODEJS_NOTIFY_09,
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_release
			'field_alert' => 'notification_release',
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_release_sound
			'field_sound' => 'notification_release_sound',
		);

		// "Any time a User stars a Repository".
		$items[] = array(
			// Use global language file.
			'label'       => LAN_PLUGIN_E107PROJECTS_NODEJS_NOTIFY_10,
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_watch
			'field_alert' => 'notification_watch',
			// Extended User Field name from plugin.xml to store configuration by user.
			// plugin_e107projects_notification_watch_sound
			'field_sound' => 'notification_watch_sound',
		);

		return array(
			'group_title'       => LAN_PLUGIN_E107PROJECTS_NODEJS_NOTIFY_01,
			'group_description' => LAN_PLUGIN_E107PROJECTS_NODEJS_NOTIFY_DESC,
			'group_items'       => $items,
		);
	}

}
