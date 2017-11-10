<?php

namespace MottaPgBundle\Util\QueryHelper;

/**
* QueryHelper
*/
class DqlQueryHelper extends QueryHelper
{
    /**
     * @param string $repoName
     * @param string $em
     */
	public function setRepository($repoName, $em)
	{
		$this->repo = $this->doctrine->getRepository($repoName, $em);
	}

    /**
     * @param string $queryFunc
     * @param int $totalPages
     * @param int $totalRows
     * @param array $criteria
     * @return array
     */
	public function find($queryFunc, &$totalPages = 0, &$totalRows = 0, array $criteria)
	{
		$this->filters = isset($criteria['filters']) ? $criteria['filters'] : array();
        $this->orderBy = $criteria['orderBy'];
        $this->direction = $criteria['direction'];

        // Cantidad de registros
        $totalRows = $this->repo->{$queryFunc}($this->repo->createQueryBuilder('a'), $this)
                ->select('COUNT(DISTINCT a)')
                ->resetDQLPart('groupBy')
                ->resetDQLPart('orderBy')
                ->getQuery()
                ->getSingleScalarResult();

        $limit = $criteria['rowsPerPage'];
        $offset = $criteria['page'] - 1;

        $totalPages = $totalRows == 0 ? 1 : ceil($totalRows / $limit);
        $firstResult = $offset * $limit;

        $query = $this->repo->{$queryFunc}($this->repo->createQueryBuilder('a'), $this)
                ->setFirstResult($firstResult)
                ->setMaxResults($limit);

        return $query->getQuery()->getResult();
	}

    /**
     * @param string $queryFunc
     * @param array $criteria
     * @return array
     */
	public function findAll($queryFunc, array $criteria)
	{
		$this->filters = isset($criteria['filters']) ? $criteria['filters'] : array();
        $this->orderBy = $criteria['orderBy'];
        $this->direction = $criteria['direction'];

        $query = $this->repo->{$queryFunc}($this->repo->createQueryBuilder('a'), $this);

        return $query->getQuery()->getResult();
	}
}
