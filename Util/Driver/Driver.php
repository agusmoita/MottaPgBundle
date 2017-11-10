<?php

namespace MottaPgBundle\Util\Driver;

/**
* Driver
*/
interface Driver
{
	public function getQuery($select, $from, $join, $where, $groupBy, $order, $page, $cant);
}
