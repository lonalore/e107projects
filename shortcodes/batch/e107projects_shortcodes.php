<?php

/**
 * @file
 * Shortcodes for "e107projects" plugin.
 */

if(!defined('e107_INIT'))
{
	exit;
}

// [PLUGINS]/e107projects/languages/[LANGUAGE]/[LANGUAGE]_front.php
e107::lan('e107projects', false, true);


/**
 * Class e107projects_shortcodes.
 */
class e107projects_shortcodes extends e_shortcode
{

	/**
	 * Constructor.
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Contents for first column in summary menu.
	 */
	public function sc_summary_menu_col_1()
	{
		$count = (int) $this->var['col_1'];
		$formatted = number_format($count);
		return '<strong>' . $formatted . '</strong><br/>' . LAN_E107PROJECTS_FRONT_01;
	}

	/**
	 * Contents for second column in summary menu.
	 */
	public function sc_summary_menu_col_2()
	{
		$count = (int) $this->var['col_2'];
		$formatted = number_format($count);
		return '<strong>' . $formatted . '</strong><br/>' . LAN_E107PROJECTS_FRONT_03;
	}

	/**
	 * Contents for third column in summary menu.
	 */
	public function sc_summary_menu_col_3()
	{
		$count = (int) $this->var['col_3'];
		$formatted = number_format($count);
		return '<strong>' . $formatted . '</strong><br/>' . LAN_E107PROJECTS_FRONT_76;
	}

	/**
	 * Notification - avatar.
	 */
	public function sc_notification_avatar()
	{
		$url = varset($this->var['avatar_url'], '');

		if(!empty($url))
		{
			$width = varset($this->var['avatar_width'], 50);
			$height = varset($this->var['avatar_height'], 50);

			return '<img src="' . $url . '" width="' . $width . '" height="' . $height . '" alt=""/>';
		}
	}

	/**
	 * Notification - message.
	 */
	public function sc_notification_message()
	{
		$message = varset($this->var['message'], '');

		if(!empty($message))
		{
			return '<p>' . $message . '</p>';
		}
	}

	/**
	 * Notification - link.
	 */
	public function sc_notification_link()
	{
		$link = varset($this->var['link'], '');

		if(!empty($link))
		{
			return $link;
		}
	}

	/**
	 * Submit page - empty text.
	 */
	public function sc_submit_project_empty_text()
	{
		return LAN_E107PROJECTS_FRONT_09;
	}

	/**
	 * Submit page - help text.
	 */
	public function sc_submit_project_help_text()
	{
		return LAN_E107PROJECTS_FRONT_12 . '<br />' . LAN_E107PROJECTS_FRONT_31;
	}

	/**
	 * Submit page - project name label.
	 */
	public function sc_submit_project_name_label()
	{
		return LAN_E107PROJECTS_FRONT_06;
	}

	/**
	 * Submit page - project name.
	 */
	public function sc_submit_project_name()
	{
		$repository = $this->var['repository'];
		$submitted = (bool) $this->var['submitted'];
		$status = (int) $this->var['status'];

		if($submitted && $status == 1)
		{
			$url = e107::url('e107projects', 'project', array(
				'user'       => $repository['owner']['login'],
				'repository' => $repository['name'],
			), array('full' => true));

			return '<a href="' . $url . '" target="_self">' . $repository['full_name'] . '</a>';
		}

		return varset($repository['full_name'], '');
	}

	/**
	 * Submit page - project description.
	 */
	public function sc_submit_project_description()
	{
		$repository = $this->var['repository'];
		return varset($repository['description'], '');
	}

	/**
	 * Submit page - project action label.
	 */
	public function sc_submit_project_action_label()
	{
		return LAN_E107PROJECTS_FRONT_07;
	}

	/**
	 * Submit page - project action label.
	 */
	public function sc_submit_project_action()
	{
		$repository = $this->var['repository'];
		$submitted = (bool) $this->var['submitted'];
		$status = (int) $this->var['status'];

		$html = '';

		if(!$submitted)
		{
			$form = e107::getForm();
			$tp = e107::getParser();

			$btnType = 'button';
			$btnName = 'submit';
			$btnVals = LAN_E107PROJECTS_FRONT_08;
			$btnAttr = $form->get_attributes(array(
				'class'          => 'btn btn-primary e-ajax has-spinner ajax-action-button',
				'data-event'     => 'click',
				'data-ajax-type' => 'POST',
			), $btnName, $btnVals);

			$html .= $form->open('submit-repository-' . $repository['id']);
			$html .= $form->hidden('repository', $repository['id']);
			$html .= '<button type="' . $btnType . '" name="' . $btnName . '"' . $btnAttr . '>';
			$html .= '<span class="spinner">' . $tp->toGlyph('fa-refresh', array('spin' => 1)) . '</span>';
			$html .= $btnVals;
			$html .= '</button>';

			$html .= $form->close();
			return $html;
		}

		// Pending.
		if($status == 0)
		{
			$html = '<p class="text-success">' . LAN_E107PROJECTS_FRONT_11 . '</p>';
			return $html;
		}

		// Rejected.
		if($status == 2)
		{
			$html = '<p class="text-danger">' . LAN_E107PROJECTS_FRONT_14 . '</p>';
			return $html;
		}

		// Approved.
		$html = '<p class="text-success">' . LAN_E107PROJECTS_FRONT_13 . '</p>';
		return $html;
	}

	/**
	 * Project list - name label.
	 */
	public function sc_project_list_project_name_label()
	{
		return LAN_E107PROJECTS_FRONT_16;
	}

	/**
	 * Project list - owner label.
	 */
	public function sc_project_list_project_owner_label()
	{
		return LAN_E107PROJECTS_FRONT_17;
	}

	/**
	 * Project list - stars label.
	 */
	public function sc_project_list_project_stars_label()
	{
		return LAN_E107PROJECTS_FRONT_18;
	}

	/**
	 * Project list - search.
	 */
	public function sc_project_list_search()
	{
		$form = e107::getForm();
		$tp = e107::getParser();

		$html = $form->open('project_search_form', 'POST');
		$html .= $form->token();

		$html .= '<div class="input-group">';

		$html .= $form->text('project_name', '', 80, array(
			'placeholder'     => LAN_E107PROJECTS_FRONT_19 . '...',
			'class'           => 'form-control e-ajax has-ajax-button',
			'data-event'      => 'keyup',
			'data-event-wait' => '500',
			'data-ajax-type'  => 'POST',
		));

		$html .= '<div class="input-group-btn">';

		$html .= '<button type="button" class="btn btn-danger e-ajax ajax-action-button has-spinner">';
		$html .= '<span class="spinner">' . $tp->toGlyph('fa-refresh', array('spin' => 1)) . '</span>';
		$html .= LAN_E107PROJECTS_FRONT_19;
		$html .= '</button>';

		$html .= '<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
		$html .= '<span class="caret"></span><span class="sr-only"></span>';
		$html .= '</button>';

		$html .= '<ul class="dropdown-menu">';

		// Search for
		$html .= '<li><strong>' . LAN_E107PROJECTS_FRONT_69 . '</strong></li>';

		$html .= '<li class="radio-content">';
		$html .= '<label class="radio">';
		$html .= $form->radio('search_for', 0, true) . ' ' . LAN_E107PROJECTS_FRONT_70;
		$html .= '</label>';
		$html .= '</li>';

		$html .= '<li class="radio-content">';
		$html .= '<label class="radio">';
		$html .= $form->radio('search_for', 1, false) . ' ' . LAN_E107PROJECTS_FRONT_71;
		$html .= '</label>';
		$html .= '</li>';

		$html .= '<li class="radio-content">';
		$html .= '<label class="radio">';
		$html .= $form->radio('search_for', 2, false) . ' ' . LAN_E107PROJECTS_FRONT_72;
		$html .= '</label>';
		$html .= '</li>';

		$html .= '<li class="radio-content">';
		$html .= '<label class="radio">';
		$html .= $form->radio('search_for', 9, false) . ' ' . LAN_E107PROJECTS_FRONT_73;
		$html .= '</label>';
		$html .= '</li>';

		// Search by
		$html .= '<li><strong>' . LAN_E107PROJECTS_FRONT_25 . '</strong></li>';

		$html .= '<li class="radio-content">';
		$html .= '<label class="radio">';
		$html .= $form->radio('search_by', 1, true) . ' ' . LAN_E107PROJECTS_FRONT_26;
		$html .= '</label>';
		$html .= '</li>';

		$html .= '<li class="radio-content">';
		$html .= '<label class="radio">';
		$html .= $form->radio('search_by', 2, false) . ' ' . LAN_E107PROJECTS_FRONT_27;
		$html .= '</label>';
		$html .= '</li>';

		// Order by
		$html .= '<li><strong>' . LAN_E107PROJECTS_FRONT_20 . '</strong></li>';

		$html .= '<li class="radio-content">';
		$html .= '<label class="radio">';
		$html .= $form->radio('order_by', 1, false) . ' ' . LAN_E107PROJECTS_FRONT_21;
		$html .= '</label>';
		$html .= '</li>';

		$html .= '<li class="radio-content">';
		$html .= '<label class="radio">';
		$html .= $form->radio('order_by', 2, true) . ' ' . LAN_E107PROJECTS_FRONT_22;
		$html .= '</label>';
		$html .= '</li>';

		// Number of results
		$html .= '<li><strong>' . LAN_E107PROJECTS_FRONT_24 . '</strong></li>';

		$html .= '<li>';
		$html .= $form->select('limit', array(
			10  => 10,
			25  => 25,
			50  => 50,
			100 => 100,
		), 10);
		$html .= '</li>';

		$html .= '</ul>';

		$html .= '</div>';
		$html .= '</div>';

		$html .= $form->close();

		return $html;
	}

	/**
	 * Project list - project url.
	 */
	public function sc_project_list_project_url()
	{
		$repository = varset($this->var['repository'], array());
		return e107::url('e107projects', 'project', array(
			'user'       => varset($repository['project_user'], ''),
			'repository' => varset($repository['project_name'], ''),
		));
	}

	/**
	 * Project list - project name.
	 */
	public function sc_project_list_project_name()
	{
		$repository = varset($this->var['repository'], array());
		return varset($repository['project_name'], '');
	}

	/**
	 * Project list - project description.
	 */
	public function sc_project_list_project_description()
	{
		$repository = varset($this->var['repository'], array());
		return varset($repository['project_description'], '');
	}

	/**
	 * Project list - project owner.
	 */
	public function sc_project_list_project_owner()
	{
		$repository = varset($this->var['repository'], array());
		return varset($repository['project_user'], '');
	}

	/**
	 * Project list - project stars.
	 */
	public function sc_project_list_project_stars()
	{
		$repository = varset($this->var['repository'], array());
		return varset($repository['project_stars'], 0);
	}

	/**
	 * Project - readme.
	 */
	public function sc_project_readme()
	{
		$repository = varset($this->var['repository'], array());
		$readme = varset($repository['project_readme'], '');

		if(!empty($readme))
		{
			// FIXME... e_parse does not format text properly.
			// return e107::getParser()->toHTML($readme, false, 'BODY', '');

			e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.parsedown.php');
			$Parsedown = new e107projectsParsedown();
			return $Parsedown->text($readme);
		}

		return '';
	}

	/**
	 * Project - button issue.
	 */
	public function sc_project_button_issue()
	{
		$repository = varset($this->var['repository'], array());
		$fullName = varset($repository['project_user'], '') . '/' . varset($repository['project_name'], '');

		$tp = e107::getParser();

		$attr = array(
			'class="github-button"',
			'href="https://github.com/' . $fullName . '/issues"',
			'data-icon="octicon-issue-opened"',
			'data-style="mega"',
			// 'data-count-api="/repos/' . $fullName . '#open_issues_count"',
			// 'data-count-aria-label="# ' . LAN_E107PROJECTS_FRONT_39 . '"',
			'aria-label="' . $tp->lanVars(LAN_E107PROJECTS_FRONT_37, array('x' => $fullName)) . '"',
		);

		return '<a ' . implode(' ', $attr) . '>' . LAN_E107PROJECTS_FRONT_38 . '</a>';
	}

	/**
	 * Project - button star.
	 */
	public function sc_project_button_star()
	{
		$repository = varset($this->var['repository'], array());
		$fullName = varset($repository['project_user'], '') . '/' . varset($repository['project_name'], '');

		$tp = e107::getParser();

		$attr = array(
			'class="github-button"',
			'href="https://github.com/' . $fullName . '"',
			'data-icon="octicon-star"',
			'data-style="mega"',
			// 'data-count-href="/' . $fullName . '/stargazers"',
			// 'data-count-api="/repos/' . $fullName . '#stargazers_count"',
			// 'data-count-aria-label="# ' . LAN_E107PROJECTS_FRONT_40 . '"',
			'aria-label="' . $tp->lanVars(LAN_E107PROJECTS_FRONT_41, array('x' => $fullName)) . '"',
		);

		return '<a ' . implode(' ', $attr) . '>' . LAN_E107PROJECTS_FRONT_42 . '</a>';
	}

	/**
	 * Project - button fork.
	 */
	public function sc_project_button_fork()
	{
		$repository = varset($this->var['repository'], array());
		$fullName = varset($repository['project_user'], '') . '/' . varset($repository['project_name'], '');

		$tp = e107::getParser();

		$attr = array(
			'class="github-button"',
			'href="https://github.com/' . $fullName . '/fork"',
			'data-icon="octicon-repo-forked"',
			'data-style="mega"',
			// 'data-count-href="/' . $fullName . '/network"',
			// 'data-count-api="/repos/' . $fullName . '#forks_count"',
			// 'data-count-aria-label="# ' . LAN_E107PROJECTS_FRONT_43 . '"',
			'aria-label="' . $tp->lanVars(LAN_E107PROJECTS_FRONT_44, array('x' => $fullName)) . '"',
		);

		return '<a ' . implode(' ', $attr) . '>' . LAN_E107PROJECTS_FRONT_45 . '</a>';
	}

	/**
	 * Project - button watch.
	 */
	public function sc_project_button_watch()
	{
		$repository = varset($this->var['repository'], array());
		$fullName = varset($repository['project_user'], '') . '/' . varset($repository['project_name'], '');

		$tp = e107::getParser();

		$attr = array(
			'class="github-button"',
			'href="https://github.com/' . $fullName . '"',
			'data-icon="octicon-eye"',
			'data-style="mega"',
			// 'data-count-href="/' . $fullName . '/watchers"',
			// 'data-count-api="/repos/' . $fullName . '#subscribers_count"',
			// 'data-count-aria-label="# ' . LAN_E107PROJECTS_FRONT_46 . '"',
			'aria-label="' . $tp->lanVars(LAN_E107PROJECTS_FRONT_47, array('x' => $fullName)) . '"',
		);

		return '<a ' . implode(' ', $attr) . '>' . LAN_E107PROJECTS_FRONT_48 . '</a>';
	}

	/**
	 * Project - button follow.
	 */
	public function sc_project_button_follow()
	{
		$repository = varset($this->var['repository'], array());
		$owner = varset($repository['project_user'], '');

		$tp = e107::getParser();

		$attr = array(
			'class="github-button"',
			'href="https://github.com/' . $owner . '"',
			'data-style="mega"',
			// 'data-count-href="/' . $owner . '/followers"',
			// 'data-count-api="/repos/' . $owner . '#followers"',
			// 'data-count-aria-label="# ' . LAN_E107PROJECTS_FRONT_49 . '"',
			'aria-label="' . $tp->lanVars(LAN_E107PROJECTS_FRONT_50, array('x' => '@' . $owner)) . '"',
		);

		return '<a ' . implode(' ', $attr) . '>' . $tp->lanVars(LAN_E107PROJECTS_FRONT_51, array('x' => '@' . $owner)) . '</a>';
	}

	/**
	 * Project - updated.
	 */
	public function sc_project_updated()
	{
		$repository = varset($this->var['repository'], array());
		$updated = varset($repository['project_updated'], 0);

		if(!empty($updated))
		{
			$tp = e107::getParser();
			return LAN_E107PROJECTS_FRONT_52 . ' ' . $tp->toDate($updated, 'short');
		}

		return '';
	}

	/**
	 * Project - description.
	 */
	public function sc_project_description()
	{
		$repository = varset($this->var['repository'], array());
		return varset($repository['project_description'], '');
	}

}
