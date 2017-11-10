<?php

namespace MottaPgBundle\Util\QueryHelper;

/**
* QueryHelper
*/
abstract class QueryHelper
{
    /**
     * @var Registry
     */
	protected $doctrine;

    /**
     * @var EntityRepository
     */
	protected $repo;

    /**
     * @var array
     */
	protected $filters;

    /**
     * @var string
     */
	protected $orderBy;

    /**
     * @var string
     */
	public $direction;

    /**
     * @param Registry $doctrine
     */
	function __construct($doctrine)
	{
		$this->doctrine = $doctrine;
	}

    /**
     * @param string $repoName
     * @param string $em
     */
	public abstract function setRepository($repoName, $em);

    /**
     * @param string $queryFunc
     * @param int $totalPages
     * @param int $totalRows
     * @param array $criteria
     * @return array
     */
	public abstract function find($queryFunc, &$totalPages = 0, &$totalRows = 0, array $criteria);

    /**
     * @param string $queryFunc
     * @param array $criteria
     * @return array
     */
	public abstract function findAll($queryFunc, array $criteria);

    /**
     * @param string $idsString
     * @return array
     */
    public function getIds($idsString)
    {
        return explode('#', trim($idsString, "#"));
    }

    /**
     * @param string $filter
     * @return mixed
     */
	public function getFilterValue($filter)
    {
        return isset($this->filters[$filter])
            ? $this->filters[$filter]
            : null
        ;
    }

    /**
     * @param string $key
     * @return boolean
     */
    public function orderBy($key)
    {
        return $this->orderBy == $key;
    }

    /**
     * @param array $filters
     * @return boolean
     */
    public function filtersHasValues($filters)
    {
        if ($filters) {
            foreach ($filters as $key => $value) {
                if ($value !== null) {
                    return true;
                }
            }
        }

        return false;
    }
}
