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

		return "SELECT * FROM (SELECT pagina.*, ROWNUM AS pagina_rownum FROM (".
				$mainQuery.
				") pagina WHERE ROWNUM <= ".$limit.") WHERE pagina_rownum >= ".$offset;
	}
}
