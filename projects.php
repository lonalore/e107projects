<?php

/**
 * @file
 * Class for rendering search page.
 */

if(!defined('e107_INIT'))
{
	require_once('../../class2.php');
}

if(!e107::isInstalled('e107projects'))
{
	e107::redirect(e_BASE . 'index.php');
}

// [PLUGINS]/e107projects/languages/[LANGUAGE]/[LANGUAGE]_front.php
e107::lan('e107projects', false, true);

// Load required class.
e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.search.php');


/**
 * Class e107ProjectsProjects.
 */
class e107ProjectsProjects
{

	/**
	 * Plugin preferences.
	 *
	 * @var array
	 */
	private $plugPrefs = array();

	/**
	 * Constructor.
	 */
	public function __construct($ajax = false)
	{
		$this->plugPrefs = e107::getPlugConfig('e107projects')->getPref();

		if($ajax)
		{
			$this->ajaxHandler();
		}

		$this->renderPage();
	}

	/**
	 * Ajax callback.
	 */
	public function ajaxHandler()
	{
		// Invalid or missing form token.
		if(!e107::getSession()->check(false))
		{
			exit;
		}

		$ajax = e107::getAjax();

		$caption = LAN_E107PROJECTS_FRONT_23;

		// Set defaults.
		$name = varset($_POST['project_name'], '');
		$searchFor = (int) varset($_POST['search_for']);
		$searchBy = 'project_name';
		$orderBy = 'project_name';
		$orderByDir = 'ASC';
		$page = (int) varset($_POST['current_page'], 1);
		$limit = (int) varset($_POST['limit'], 10);
		$limit = ($limit > 100 ? 100 : $limit);
		$offset = $limit * ($page - 1);

		if((int) varset($_POST['search_by']) == 2)
		{
			$searchBy = 'project_user';
		}

		if((int) varset($_POST['order_by']) == 2)
		{
			$orderBy = 'project_stars';
			$orderByDir = 'DESC';
		}

		// Override page title.
		if(empty($name) && $orderBy == 'project_stars')
		{
			$caption = LAN_E107PROJECTS_FRONT_15;
		}

		$search = new e107ProjectsSearchManager();
		$search->setCondition('project_status', 1, '=');
		$search->setCondition($searchBy, $name, 'STARTS_WITH');

		if($searchFor > 0)
		{
			$search->setCondition('project_type', $searchFor, '=');
		}

		// Count results for pager.
		$count = $search->count();

		$search->orderBy($orderBy, $orderByDir);
		$search->limit($limit, $offset);
		$repositories = $search->run();

		$content = $this->renderReults($repositories);
		$pager = $this->renderPager($count, $limit, $page);

		$commands = array();
		$commands[] = $ajax->commandInvoke('section h2.caption', 'html', array($caption));
		$commands[] = $ajax->commandInvoke('#project-search-form button.e-ajax', 'removeClass', array('active'));
		$commands[] = $ajax->commandInvoke('#project-search-form button.e-ajax', 'removeAttr', array('disabled'));
		$commands[] = $ajax->commandInvoke('#project-search-result', 'html', array($content));
		$commands[] = $ajax->commandInvoke('#project-search-pager', 'html', array($pager));

		$commands[] = $ajax->commandInvoke('html, body', 'animate', array(array('scrollTop' => 0), 'slow'));

		$ajax->response($commands);
		exit;
	}

	/**
	 * Render page.
	 */
	public function renderPage()
	{
		$ns = e107::getRender();
		$tpl = e107::getTemplate('e107projects');
		$sc = e107::getScBatch('e107projects', true);
		$tp = e107::getParser();

		$caption = LAN_E107PROJECTS_FRONT_15;
		$content = '';

		$limit = 10;

		// Default values for pagination.
		$_POST['project_name'] = '';
		$_POST['search_for'] = 0;
		$_POST['search_by'] = 1;
		$_POST['order_by'] = 2;
		$_POST['current_page'] = 1;
		$_POST['limit'] = $limit;

		$search = new e107ProjectsSearchManager();
		$search->setCondition('project_status', 1, '=');
		$search->setCondition('project_name', '', 'STARTS_WITH');

		// Count results for pager.
		$count = $search->count();

		$search->orderBy('project_stars', 'DESC');
		$search->limit($limit, 0);
		$repositories = $search->run();

		$content .= $tp->parseTemplate($tpl['projects']['list']['search'], true, $sc);

		$content .= '<div id="project-search-result">';
		$content .= $this->renderReults($repositories);
		$content .= '</div>';

		$content .= '<div id="project-search-pager">';
		$content .= $this->renderPager($count, $limit, 1);
		$content .= '</div>';

		$ns->tablerender($caption, $content);
	}

	/**
	 * Render results table.
	 *
	 * @param array $repositories
	 *  Array contains results.
	 *
	 * @return string
	 */
	public function renderReults($repositories)
	{
		$tpl = e107::getTemplate('e107projects');
		$sc = e107::getScBatch('e107projects', true);
		$tp = e107::getParser();

		$content = '';

		$content .= $tp->parseTemplate($tpl['projects']['list']['pre'], true, $sc);
		foreach($repositories as $repository)
		{
			$sc->setVars(array(
				'repository' => $repository,
			));
			$content .= $tp->parseTemplate($tpl['projects']['list']['row'], true, $sc);
		}
		$content .= $tp->parseTemplate($tpl['projects']['list']['post'], true, $sc);

		return $content;
	}

	/**
	 * Render pager.
	 *
	 * @param $max
	 * @param $limit
	 * @param int $current
	 *
	 * @return string
	 */
	public function renderPager($max, $limit, $current = 1)
	{
		$html = '';
		$hidden = '';

		// If no data, we return with empty pager.
		if(empty($max) || empty($limit))
		{
			return $html;
		}

		// Calculate number of the pages.
		$pages = ceil($max / $limit);

		// If there is no more than one page.
		if($pages < 2)
		{
			return $html;
		}

		$tp = e107::getParser();
		$form = e107::getForm();

		foreach($_POST as $key => $value)
		{
			if($key != 'current_page')
			{
				$hidden .= $form->hidden($key, $value);
			}
		}

		$spinnerIcon = $tp->toGlyph('fa-refresh', array('spin' => 1));

		$html .= '<nav aria-label="Page navigation">';
		$html .= '<ul class="pagination">';
		for($i = 1; $i <= $pages; $i++)
		{
			$html .= '<li class="page-item' . ($i == $current ? ' active' : '') . '">';
			$html .= $form->open('pager-' . $i, 'post', e_SELF, array('class' => 'page-link'));
			$html .= '<a class="page-link e-ajax ajax-action-button has-spinner" href="#">';
			$html .= '<span class="spinner">' . $spinnerIcon . '</span>' . $i;
			$html .= $form->hidden('current_page', $i);
			$html .= $hidden;
			$html .= '</a>';
			$html .= $form->close();
			$html .= '</li>';
		}
		$html .= '</ul>';
		$html .= '</nav>';

		return $html;
	}

}


if(!e_AJAX_REQUEST)
{
	require_once(HEADERF);
	new e107ProjectsProjects();
	require_once(FOOTERF);
	exit;
}
else
{
	new e107ProjectsProjects(true);
}
