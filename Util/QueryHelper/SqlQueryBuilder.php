<?php

namespace MottaPgBundle\Util\QueryHelper;

use MottaPgBundle\Util\Driver\MSDriver;
use MottaPgBundle\Util\Driver\MySqlDriver;
use MottaPgBundle\Util\Driver\OracleDriver;
use MottaPgBundle\Util\Driver\PgDriver;

/**
* SqlQueryBuilder
*/
class SqlQueryBuilder
{
	const ORACLE 	 = 'pdo_oracle';
	const POSTGRESQL = 'pdo_pgsql';
	const SQLSRV 	 = 'pdo_sqlsrv';
	const MSSQL 	 = 'mssql';
	const MYSQL 	 = 'pdo_mysql';

	private $id = null;

	private $idSelect = null;

	private $select = null;

	private $selects = array();

	private $from = null;

	private $joins = array();

	private $where = null;

	private $wheres = array();

	private $groupBy = null;

	private $groupBys = array();

	private $order = null;

	private $orders = array();

	private $parameters = array();

	private $page = 0;

	private $offset = 0;

	private $limit = 0;

	function __construct()
	{
	}

	public function id($id)
	{
		$this->id = $id;
		$this->idSelect = "SELECT ".$id." AS id";

		return $this;
	}

	public function select($select)
	{
		$this->select = $select;
		$this->selects = array();

		return $this;
	}

	public function addSelect($select)
	{
		$this->selects[] = $select;

		return $this;
	}

	public function from($from)
	{
		$this->from = " FROM ".$from." ";

		return $this;
	}

	public function join($join)
	{
		$this->innerJoin($join);

		return $this;
	}

	public function innerJoin($join)
	{
		$this->joins[] = "INNER JOIN ".$join;

		return $this;
	}

	public function leftJoin($join)
	{
		$this->joins[] = "LEFT JOIN ".$join;

		return $this;
	}

	public function where($where)
	{
		$this->where = $where;
		$this->wheres = array();

		return $this;
	}

	public function andWhere($where)
	{
		$this->wheres[] = "AND ".$where;

		return $this;
	}

	public function orWhere($where)
	{
		$this->wheres[] = "OR ".$where;

		return $this;
	}

	public function groupBy($groupBy)
	{
		$this->groupBy = $groupBy;
		$this->groupBys = array();

		return $this;
	}

	public function addGroupBy($groupBy)
	{
		$this->groupBys[] = $groupBy;

		return $this;
	}

	public function orderBy($order)
	{
		$this->order = $order;
		$this->orders = array();

		return $this;
	}

	public function addOrderBy($order)
	{
		$this->orders[] = $order;

		return $this;
	}

	public function setParameter($key, $value, $type = null)
	{
		$this->parameters[] = array(
			'key' => $key,
			'value' => $value,
			'type' => $type
		);

		return $this;
	}

	protected function getParameters()
	{
		return $this->parameters;
	}

	protected function setPage($page)
	{
		$this->page = $page;

		return $this;
	}

	protected function setMaxResults($limit)
	{
		$this->limit = $limit;

		return $this;
	}

	protected function getQuery($driverName = self::ORACLE)
	{
		$select = $this->selects;
		if ($this->select) {
			array_unshift($select, $this->select);
		}
		array_unshift($select, $this->idSelect);

		$select = implode(', ', $select);
		$from = $this->from;
		$joins = implode(' ', $this->joins);

		$where = $this->wheres;
		if ($this->where) {
			array_unshift($where, $this->where);
		}
		$where = implode(' ', $where);
		$where = $where != "" ? " WHERE ".$where : " ";

		$groupBy = $this->groupBys;
		if ($this->groupBy) {
			array_unshift($groupBy, $this->groupBy);
		}
		$groupBy = implode(', ', $groupBy);
		$groupBy = $groupBy != "" ? "GROUP BY ".$groupBy : " ";

		$order = $this->orders;
		if ($this->order) {
			array_unshift($order, $this->order);
		}
		$order = implode(', ', $order);
		$order = $order != "" ? "ORDER BY ".$order : "";

		switch ($driverName) {
			case self::POSTGRESQL:
				$driver = new PgDriver();
				break;

			case self::ORACLE:
				$driver = new OracleDriver();
				break;

			case self::SQLSRV:
				$driver = new MSDriver();
				break;

			case self::MSSQL:
				$driver = new MSDriver();
				break;

			case self::MYSQL:
				$driver = new MySqlDriver();
				break;
		}

		return $driver->getQuery($select, $from, $join, $where, $groupBy, $order, $this->page, $this->limit);
	}

	protected function getAllQuery()
	{
		$select = $this->selects;
		if ($this->select) {
			array_unshift($select, $this->select);
		}
		array_unshift($select, $this->idSelect);

		$select = implode(', ', $select);
		$from = $this->from;
		$joins = implode(' ', $this->joins);

		$where = $this->wheres;
		if ($this->where) {
			array_unshift($where, $this->where);
		}
		$where = implode(' ', $where);
		$where = $where != "" ? " WHERE ".$where : " ";

		$groupBy = $this->groupBys;
		if ($this->groupBy) {
			array_unshift($groupBy, $this->groupBy);
		}
		$groupBy = implode(', ', $groupBy);
		$groupBy = $groupBy != "" ? "GROUP BY ".$groupBy : " ";

		$order = $this->orders;
		if ($this->order) {
			array_unshift($order, $this->order);
		}
		$order = implode(', ', $order);
		$order = $order != "" ? "ORDER BY ".$order : "";

		$mainQuery = $select.$from.$joins.$where.$groupBy.$order;

		return $mainQuery;
	}

	protected function getTotalRows()
	{
		$select = "SELECT COUNT(".$this->id.") AS total ";
		$from = $this->from;
		$joins = implode(' ', $this->joins);

		$where = $this->wheres;
		if ($this->where) {
			array_unshift($where, $this->where);
		}
		$where = implode(' ', $where);
		$where = $where != "" ? " WHERE ".$where : " ";

		return $select.$from.$joins.$where;
	}
}
