<?php

namespace MottaPgBundle\Util\Export;

/**
* ExportRow
*/
class Row
{
	/**
	 * @var array
	 */
	private $cells;

	function __construct()
	{
		$this->cells = array();
	}

	/**
	 * @return array
	 */
	public function getCells()
	{
		return $this->cells;
	}

	/**
	 * @param string|int|float $value
	 * @return ExportHeader
	 */
	public function add($value)
	{
		$this->cells[] = new Cell($value);

		return $this;
	}
}
