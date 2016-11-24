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
	public function setCondition($field, $value, $operator);

	/**
	 * Set limit.
	 *
	 * @param int $limit
	 *  Limit for search query.
	 */
	public function setLimit($limit);

	/**
	 * Run search query.
	 *
	 * @return mixed
	 */
	public function run();
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
	public function setCondition($field, $value, $operator)
	{
		$this->handler->setCondition($field, $value, $operator);
	}

	/**
	 * Set limit.
	 *
	 * @param int $limit
	 *  Limit for search query.
	 */
	public function setLimit($limit)
	{
		$this->handler->setLimit($limit);
	}

	/**
	 * Run search query.
	 *
	 * @return mixed
	 */
	public function run()
	{
		return $this->handler->run();
	}

}

