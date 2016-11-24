<?php

/**
 * @file
 * Simple database search handler.
 */


/**
 * Class e107ProjectsSearchDatabaseAdapter.
 */
class e107ProjectsSearchDatabaseAdapter implements e107ProjectsSearchInterface
{

	/**
	 * Set condition for search query.
	 *
	 * @param string $field
	 *  Options:
	 *  - name: project name
	 *  - author: project author
	 * @param mixed $value
	 *  The value to test the column value against.
	 * @param string $operator
	 *  '=', '<>', '>', '>=', '<', '<=', 'STARTS_WITH', 'CONTAINS'
	 */
	public function setCondition($field, $value, $operator)
	{

	}

	/**
	 * Set limit.
	 *
	 * @param int $limit
	 *  Limit for search query.
	 */
	public function setLimit($limit)
	{

	}

	/**
	 * Run search query.
	 *
	 * @return mixed
	 */
	public function run()
	{

	}

}
