<?php

namespace MottaPgBundle\Util\Driver;

/**
* MSDriver
*/
class MSDriver implements Driver
{
	public function getQuery($select, $from, $join, $where, $groupBy, $order, $page, $cant)
	{
		$mainQuery = $select.", ROW_NUMBER() OVER (ORDER BY (SELECT 0)) AS page_rownum ".$from.$join.$where.$groupBy.$order;
		$limit = $page * $cant;
		$offset = $page == 1 ? 0 : ($page - 1) * $cant + 1;

		return "SELECT * FROM (".$mainQuery." OFFSET 0 ROWS) AS doctrine_tbl WHERE page_rownum BETWEEN ".$offset." AND ".$limit;
	}
}
