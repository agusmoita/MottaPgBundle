<?php

namespace MottaPgBundle\Util\Export;

/**
* ExportHeader
*/
class Header
{
	/**
	 * @var array
	 */
	private $headers;

	function __construct()
	{
		$this->headers = array();
	}

	/**
	 * @return array
	 */
	public function getCells()
	{
		return $this->headers;
	}

	/**
	 * @param int $i
	 * @return Cell
	 */
	public function getCell($i)
	{
		$cells = $this->getCells();
		return $cells[$i];
	}

	/**
	 * @param int $i
	 * @return int
	 */
	public function getHeaderWidth($i)
	{
		return $this->getCell($i)->getWidth();
	}

	/**
	 * @param string $header
	 * @param int $width
	 * @return ExportHeader
	 */
	public function add($header, $width = 45)
	{
		$this->headers[] = new Cell($header, $width);

		return $this;
	}
}
