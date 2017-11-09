<?php

namespace MottaPgBundle\Util\Driver;

/**
* MySqlDriver
*/
class MySqlDriver implements Driver
{
	public function getQuery($select, $from, $join, $where, $groupBy, $order, $page, $cant)
	{
		$mainQuery = $select.$from.$join.$where.$groupBy.$order;
		$limit = $cant;
		$offset = $page == 1 ? 0 : ($page - 1) * $limit;

		return $mainQuery." LIMIT ".$limit." OFFSET ".$offset;
	}
}
