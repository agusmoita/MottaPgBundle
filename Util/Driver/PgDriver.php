<?php

namespace MottaPgBundle\Util\Driver;

/**
* PgDriver
*/
class PgDriver implements Driver
{
	public function getQuery($select, $from, $join, $where, $groupBy, $order, $page, $cant)
	{
		$mainQuery = $select.$from.$join.$where.$groupBy.$order;
		$limit = $cant;
		$offset = $page == 1 ? 0 : ($page - 1) * $cant;

		return $mainQuery." LIMIT ".$limit." OFFSET ".$offset;
	}
}
