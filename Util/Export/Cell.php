<?php

namespace MottaPgBundle\Util\Export;

/**
* Cell
*/
class Cell
{
	private $value;

	private $width;

	function __construct($value, $width = 45)
	{
		$this->value = $value;
		$this->width = $width;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getWidth()
	{
		return $this->width;
	}
}
