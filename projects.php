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
		
		$tpl = e107::getTemplate('e107projects');
		$sc = e107::getScBatch('e107projects', true);
		$tp = e107::getParser();
		$ajax = e107::getAjax();

		$caption = LAN_E107PROJECTS_FRONT_23;
		$content = '';

		$name = varset($_POST['project_name']);

		// if(!empty($name))
		{
			$orderBy = 'project_name';
			$orderByDir = 'ASC';

			if((int) varset($_POST['order_by']) == 2)
			{
				$orderBy = 'project_stars';
				$orderByDir = 'DESC';
			}

			$limit = (int) varset($_POST['limit'], 25);
			$limit = ($limit > 100 ? 100 : $limit);

			$search = new e107ProjectsSearchManager();
			$search->setCondition('project_status', 1, '=');
			$search->setCondition('project_name', $name, 'STARTS_WITH');
			$search->orderBy($orderBy, $orderByDir);
			$search->limit($limit);
			$repositories = $search->run();

			$content .= $tp->parseTemplate($tpl['projects']['list']['pre'], true, $sc);
			foreach($repositories as $repository)
			{
				$sc->setVars(array(
					'repository' => $repository,
				));
				$content .= $tp->parseTemplate($tpl['projects']['list']['row'], true, $sc);
			}
			$content .= $tp->parseTemplate($tpl['projects']['list']['post'], true, $sc);
		}

		$commands = array();
		$commands[] = $ajax->commandInvoke('section h2.caption', 'html', array($caption));
		$commands[] = $ajax->commandInvoke('#project-search-form button.e-ajax', 'removeClass', array('active'));
		$commands[] = $ajax->commandInvoke('#project-search-form button.e-ajax', 'removeAttr', array('disabled'));
		$commands[] = $ajax->commandInvoke('#project-search-result', 'html', array($content));

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

		$repositories = $this->getPopularRepositories();

		$caption = LAN_E107PROJECTS_FRONT_15;
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

		$content = '<div id="project-search-result">' . $content . '</div>';
		$content = $tp->parseTemplate($tpl['projects']['list']['search'], true, $sc) . $content;

		$ns->tablerender($caption, $content);
	}

	/**
	 * Get popular repositories.
	 *
	 * @return array
	 *  Contains database records. Or empty array.
	 */
	public function getPopularRepositories()
	{
		$search = new e107ProjectsSearchManager();
		$search->setCondition('project_status', 1, '=');
		$search->orderBy('project_stars', 'DESC');
		$search->limit(25);
		return $search->run();
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
