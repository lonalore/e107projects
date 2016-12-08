<?php

/**
 * @file
 *
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class e107projects_event.
 */
class e107projects_event
{

	/**
	 * Configure functions/methods to run when specific e107 events are triggered.
	 *
	 * @return array
	 */
	function config()
	{
		$event = array();

		// After XUP login.
		$event[] = array(
			'name'     => 'user_xup_updated',
			'function' => 'e107projects_user_xup_updated_callback',
		);

		// After a user updated his profile.
		$event[] = array(
			'name'     => 'postuserset', // TODO this may change in core.
			'function' => 'e107projects_user_settings_changed_callback',
		);

		// After a project has been submitted.
		$event[] = array(
			'name'     => 'e107projects_user_project_submitted',
			'function' => 'e107projects_user_project_submitted_callback',
		);

		// After a project has been approved.
		$event[] = array(
			'name'     => 'e107projects_user_project_approved',
			'function' => 'e107projects_user_project_approved_callback',
		);

		// After a project has been rejected.
		$event[] = array(
			'name'     => 'e107projects_user_project_rejected',
			'function' => 'e107projects_user_project_rejected_callback',
		);

		// After a Github Push Webhook IPN has arrived.
		$event[] = array(
			'name'     => 'e107projects_webhook_push',
			'function' => 'e107projects_webhook_push_callback',
		);

		// Any time a Commit is commented on..
		$event[] = array(
			'name'     => 'e107projects_webhook_commit_comment',
			'function' => 'e107projects_webhook_commit_comment_callback',
		);

		// Any time a Repository is forked.
		$event[] = array(
			'name'     => 'e107projects_webhook_fork',
			'function' => 'e107projects_webhook_fork_callback'
		);

		// Any time an Issue is assigned, unassigned, labeled, unlabeled,
		// opened, edited, milestoned, demilestoned, closed, or reopened.
		$event[] = array(
			'name'     => 'e107projects_webhook_issues',
			'function' => 'e107projects_webhook_issues_callback',
		);

		// Any time a comment on an issue is created, edited, or deleted.
		$event[] = array(
			'name'     => 'e107projects_webhook_issue_comment',
			'function' => 'e107projects_webhook_issue_comment_callback',
		);

		return $event;
	}

	/**
	 * External User Profile updated.
	 *
	 * @param $data
	 */
	function e107projects_user_xup_updated_callback($data)
	{
		// Common functions.
		e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.common.php');
		// Try to update Access Token on hooks (are saved in database)
		// belong to the logged in user.
		e107projects_update_access_token(null, $data['user_id']);
	}

	/**
	 * After updating user settings.
	 *
	 * @param $data
	 */
	function e107projects_user_settings_changed_callback($data)
	{
		// Helper functions for event callbacks.
		e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.event.php');
		// Try to geocode user's location.
		e107projects_user_settings_changed_location($data);
	}

	/**
	 * After project submission.
	 *
	 * @param $data
	 */
	function e107projects_user_project_submitted_callback($data)
	{
		// Helper functions for event callbacks.
		e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.event.php');
		// Send notification.
		e107projects_user_project_submitted_notification($data);
	}

	/**
	 * After a project has been approved.
	 *
	 * @param $data
	 */
	function e107projects_user_project_approved_callback($data)
	{
		// Helper functions for event callbacks.
		e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.event.php');
		// Send broadcast notification.
		e107projects_user_project_approved_notification($data);
		// Send broadcast notification for displaying OpenLayers Popup.
		e107projects_user_project_approved_notification_openlayers($data);
	}

	/**
	 * After a project has been rejected.
	 *
	 * @param $data
	 */
	function e107projects_user_project_rejected_callback($data)
	{
		// Helper functions for event callbacks.
		e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.event.php');
		// Send broadcast notification.
		e107projects_user_project_rejected_notification($data);
	}

	/**
	 * Any Git push to a Repository, including editing tags or branches.
	 * Commits via API actions that update references are also counted.
	 *
	 * @see https://developer.github.com/v3/activity/events/types/#pushevent
	 *
	 * @param array $data
	 *  Payload data.
	 */
	function e107projects_webhook_push_callback($data)
	{
		// Helper functions for event callbacks.
		e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.event.php');
		// Send broadcast notification.
		e107projects_webhook_push_notification($data);
		// Send broadcast notification for displaying OpenLayers Popup.
		e107projects_webhook_push_notification_openlayers($data);
	}

	/**
	 * Any time a Commit is commented on.
	 *
	 * @see https://developer.github.com/v3/activity/events/types/#commitcommentevent
	 *
	 * @param array $data
	 *  Payload data.
	 */
	function e107projects_webhook_commit_comment_callback($data)
	{
		// Helper functions for event callbacks.
		e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.event.php');
		// Send broadcast notification.
		e107projects_webhook_commit_comment_notification($data);
		// Send broadcast notification for displaying OpenLayers Popup.
		e107projects_webhook_commit_comment_notification_openlayers($data);
	}

	/**
	 * Any time a Repository is forked.
	 *
	 * @see https://developer.github.com/v3/activity/events/types/#forkevent
	 *
	 * @param $data
	 *  Payload data.
	 */
	function e107projects_webhook_fork_callback($data)
	{
		// Helper functions for event callbacks.
		e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.event.php');
		// Send broadcast notification.
		e107projects_webhook_fork_notification($data);
		// Send broadcast notification for displaying OpenLayers Popup.
		e107projects_webhook_fork_notification_openlayers($data);
	}

	/**
	 * Any time an Issue is assigned, unassigned, labeled, unlabeled,
	 * opened, edited, milestoned, demilestoned, closed, or reopened.
	 *
	 * @see https://developer.github.com/v3/activity/events/types/#issuesevent
	 *
	 * @param $data
	 *  Payload data.
	 */
	function e107projects_webhook_issues_callback($data)
	{
		// Helper functions for event callbacks.
		e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.event.php');
		// Send broadcast notification.
		e107projects_webhook_issues_notification($data);
		// Send broadcast notification for displaying OpenLayers Popup.
		e107projects_webhook_issues_notification_openlayers($data);
	}

	/**
	 * Any time a comment on an issue is created, edited, or deleted.
	 *
	 * @see https://developer.github.com/v3/activity/events/types/#issuecommentevent
	 *
	 * @param $data
	 *  Payload data.
	 */
	function e107projects_webhook_issue_comment_callback($data)
	{
		// Helper functions for event callbacks.
		e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.event.php');
		// Send broadcast notification.
		e107projects_webhook_issue_comment_notification($data);
		// Send broadcast notification for displaying OpenLayers Popup.
		e107projects_webhook_issue_comment_notification_openlayers($data);
	}

}
