<?php

/**
 * @file
 * Callback functions for metatag integration.
 */


/**
 * Detects Project Search page.
 *
 * @return bool
 */
function e107projects_meta_project_search_detect()
{
	$page = defset('e_REQUEST_URI', '');

	if($page == '/projects' && !e_QUERY)
	{
		return true;
	}

	return false;
}

/**
 * Detects Project pages.
 *
 * @return array|bool
 */
function e107projects_meta_project_detect()
{
	if(!empty($_GET['user']) && !empty($_GET['repository']))
	{
		$db = e107::getDb();
		$tp = e107::getParser();

		$u = $tp->filter($_GET['user']);
		$r = $tp->filter($_GET['repository']);

		$where = 'project_user = "' . $u . '" AND project_name = "' . $r . '" ';

		return $db->retrieve('e107projects_project', 'project_id', $where);
	}

	return false;
}

/**
 * Loads Project details.
 *
 * @param $project_id
 * @return array
 */
function e107projects_meta_project_load($project_id)
{
	$db = e107::getDb();
	$db->select('e107projects_project', '*', 'project_id = ' . (int) $project_id);

	$entity = array();

	while($row = $db->fetch())
	{
		$entity = $row;
	}

	return $entity;
}

/**
 * Project Title token.
 *
 * @param $entity
 * @return string
 */
function e107projects_meta_token_project_title($entity)
{
	if(!empty($entity['project_user']) && !empty($entity['project_name']))
	{
		return $entity['project_user'] . '/' . $entity['project_name'];
	}

	return '';
}

/**
 * Project Description token.
 *
 * @param $entity
 * @return string
 */
function e107projects_meta_token_project_description($entity)
{
	if(!empty($entity['project_description']))
	{
		return $entity['project_description'];
	}

	return '';
}
