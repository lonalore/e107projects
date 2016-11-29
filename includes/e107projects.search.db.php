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

	private $conditions = array();

	private $orderBy = '';

	private $limit = '';

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

		$tp = e107::getParser();

		switch($operator)
		{
			case 'STARTS_WITH':
				$this->conditions[] = $field . ' LIKE "' . $tp->toDB($value) . '%"';
				break;

			case 'CONTAINS':
				$this->conditions[] = $field . ' LIKE "%' . $tp->toDB($value) . '%"';
				break;

			case '=':
			case '<>':
			case '>':
			case '>=':
			case '<':
			case '<=':
				if(!is_numeric($value))
				{
					$value = '"' . $tp->toDB($value) . '"';
				}

				$this->conditions[] = $field . ' ' . $operator . ' ' . $value;
				break;
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

		$this->orderBy = ' ORDER BY ' . $field . ' ' . $direction;
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

		$this->limit = ' LIMIT ' . $offset . ',' . $limit;
	}

	/**
	 * Run search query.
	 *
	 * @return array
	 */
	public function run()
	{
		$args = '';

		if(!empty($this->conditions))
		{
			$args .= implode(' AND ', $this->conditions);
		}

		$args .= $this->orderBy;
		$args .= $this->limit;

		$db = e107::getDb('e107projects_serach');

		if($db->select('e107projects_project', '*', $args, empty($this->conditions)))
		{
			return $db->rows();
		}

		return array();
	}

}
