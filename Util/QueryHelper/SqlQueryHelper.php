<?php

namespace MottaPgBundle\Util\QueryHelper;

/**
* SqlQueryHelper
*/
class SqlQueryHelper extends QueryHelper
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param string $repoName
     * @param string $em
     */
    public function setRepository($repoName, $em)
    {
        $this->repo = $this->doctrine->getRepository($repoName, $em);
        $this->em = $this->doctrine->getManager($em);
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
        $limit = $criteria['rowsPerPage'];
        $page = $criteria['page'];

        $queryBuilder = $this->repo->{$queryFunc}(new SqlQueryBuilder(), $this)
                ->setPage($page)
                ->setMaxResults($limit);

        $totalQuery = $this->em
                ->getConnection()
                ->prepare(
                    $queryBuilder->getTotalRows()
                )
        ;
        foreach ($queryBuilder->getParameters() as $param) {
            $totalQuery->bindValue($param['key'], $param['value'], $param['type']);
        }
        $totalQuery->execute();
        $totalRows = $totalQuery->fetchAll();
        $totalRows = $totalRows[0]['total'];

        $totalPages = $totalRows == 0 ? 1 : ceil($totalRows / $limit);

        $query = $this->em
                ->getConnection()
                ->prepare(
                    $queryBuilder->getQuery(
                        $this->em
                        ->getConnection()
                        ->getDriver()
                        ->getName()
                    )
                )
        ;
        foreach ($queryBuilder->getParameters() as $param) {
            $query->bindValue($param['key'], $param['value'], $param['type']);
        }
        $query->execute();

        return $query->fetchAll();
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

        $queryBuilder = $this->repo->{$queryFunc}(new SqlQueryBuilder(), $this);

        $query = $this->em
                ->getConnection()
                ->prepare(
                    $queryBuilder->getAllQuery()
                )
        ;
        foreach ($queryBuilder->getParameters() as $param) {
            $query->bindValue($param['key'], $param['value'], $param['type']);
        }
        $query->execute();

        return $query->fetchAll();
    }
}
