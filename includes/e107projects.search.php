<?php

/**
 * @file
 * Main class for search engine.
 */


/**
 * Interface e107ProjectsSearchInterface.
 */
interface e107ProjectsSearchInterface
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
	public function setCondition($field, $value, $operator = '=');

	/**
	 * Set ordering.
	 *
	 * @param string $field
	 *  Field name.
	 * @param string $direction
	 *  ASC or DESC
	 */
	public function orderBy($field, $direction = 'ASC');

	/**
	 * Set limit.
	 *
	 * @param int $limit
	 *  Limit for search query.
	 * @param int $offset
	 *  Offset for limit.
	 */
	public function limit($limit, $offset = 0);

	/**
	 * Run search query.
	 *
	 * @return array
	 */
	public function run();

	/**
	 * Count results.
	 *
	 * @return int
	 */
	public function count();
}


/**
 * Class e107ProjectsSearchManager.
 */
class e107ProjectsSearchManager
{

	/**
	 * Plugin preferences.
	 *
	 * @var array
	 */
	private $plugPrefs = array();

	/**
	 * Search handler.
	 *
	 * @var
	 */
	public $handler = null;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->plugPrefs = e107::getPlugConfig('e107projects')->getPref();

		// Get search handler type.
		$handler = varset($this->plugPrefs['search_handler'], 'db');

		switch($handler)
		{
			case "solr":
				e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.search.solr.php');
				$this->handler = new e107ProjectsSearchApacheSolrAdapter();
				break;
			case "db":
			default:
				e107_require_once(e_PLUGIN . 'e107projects/includes/e107projects.search.db.php');
				$this->handler = new e107ProjectsSearchDatabaseAdapter();
				break;
		}
	}

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
	public function setCondition($field, $value, $operator = '=')
	{
		$this->handler->setCondition($field, $value, $operator);
	}

	/**
	 * Set ordering.
	 *
	 * @param string $field
	 *  Field name.
	 * @param string $direction
	 *  ASC or DESC
	 */
	public function orderBy($field, $direction = 'ASC')
	{
		$this->handler->orderBy($field, $direction);
	}

	/**
	 * Set limit.
	 *
	 * @param int $limit
	 *  Limit for search query.
	 * @param int $offset
	 *  Offset for limit.
	 */
	public function limit($limit, $offset = 0)
	{
		$this->handler->limit($limit, $offset);
	}

	/**
	 * Run search query.
	 *
	 * @return array
	 */
	public function run()
	{
		return $this->handler->run();
	}

	/**
	 * Count results.
	 *
	 * @return int
	 */
	public function count()
	{
		return $this->handler->count();
	}

}

