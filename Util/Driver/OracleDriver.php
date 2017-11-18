<?php

namespace MottaPgBundle\Util\Driver;

/**
* OracleDriver
*/
class OracleDriver implements Driver
{
	public function getQuery($select, $from, $join, $where, $groupBy, $order, $page, $cant)
	{
		$mainQuery = $select.$from.$join.$where.$groupBy.$order;
		$limit = $page * $cant;
		$offset = $page == 1 ? 0 : ($page - 1) * $cant + 1;

		return "SELECT * FROM (SELECT page.*, ROWNUM AS page_rownum FROM (".
				$mainQuery.
				") page WHERE ROWNUM <= ".$limit.") WHERE page_rownum >= ".$offset;
	}
}
