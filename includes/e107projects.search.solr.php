<?php

/**
 * @file
 * Apache Solr search handler.
 */


/**
 * Class e107ProjectsSearchApacheSolrAdapter.
 */
class e107ProjectsSearchApacheSolrAdapter implements e107ProjectsSearchInterface
{

	private $conditions = array();

	private $orderBy = '';

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
	public function setCondition($field = '', $value = '', $operator = '=')
	{
		if(empty($field) || empty($value))
		{
			return;
		}
	}

	/**
	 * Set ordering.
	 *
	 * @param string $field
	 *  Field name.
	 * @param string $direction
	 *  ASC or DESC
	 */
	public function orderBy($field = '', $direction = 'ASC')
	{
		if(empty($field))
		{
			return;
		}

		$this->orderBy = $field . ' ' . $direction;
	}

	/**
	 * Set limit.
	 *
	 * @param int $limit
	 *  Limit for search query.
	 * @param int $offset
	 *  Offset for limit.
	 */
	public function limit($limit = 0, $offset = 0)
	{
		if(empty($limit))
		{
			return;
		}
	}

	/**
	 * Run search query.
	 *
	 * @return array
	 */
	public function run()
	{

	}

	/**
	 * Count results.
	 *
	 * @return int
	 */
	public function count()
	{

	}

}
