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
		return '<strong>' . $formatted . '</strong><br/>' . LAN_E107PROJECTS_FRONT_02;
	}

	/**
	 * Contents for third column in summary menu.
	 */
	public function sc_summary_menu_col_4()
	{
		$count = (int) $this->var['col_4'];
		$formatted = number_format($count);
		return '<strong>' . $formatted . '</strong><br/>' . LAN_E107PROJECTS_FRONT_04;
	}

	/**
	 * Submit page - empty text.
	 */
	public function sc_submit_project_empty_text()
	{
		return LAN_E107PROJECTS_FRONT_09;
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

		if($submitted)
		{
			$url = e107::getUrl()->create('project', array(
				'user'       => $repository['owner']['login'],
				'repository' => $repository['name'],
			), array('full' => true));

			return '<a href="' . $url . '" target="_self">' . $repository['name'] . '</a>';
		}

		return varset($repository['name'], '');
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

		$html = '';

		if(!$submitted)
		{
			$form = e107::getForm();
			$tp = e107::getParser();

			$html .= $form->open('submit-repository-' . $repository['id']);
			$html .= $form->hidden('repository', $repository['id']);
			$html .= $form->submit('submit', LAN_E107PROJECTS_FRONT_08, array(
				'class'          => 'btn btn-primary e-ajax',
				'data-event'     => 'click',
				'data-ajax-type' => 'POST',
			));
			$html .= $form->close();
		}

		return $html;
	}

}
