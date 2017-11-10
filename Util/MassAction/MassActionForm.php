<?php

namespace MottaPgBundle\Util\MassAction;

/**
* MassActionForm
*/
class MassActionForm
{
	/**
	 * Campos del formulario
	 * @var array
	 */
	private $values;

	function __construct($data)
	{
		$this->values = $data;
	}

	/**
	 * Obtiene un campo del formulario
	 * @param  string $key
	 * @return mixed
	 */
	public function get($key)
	{
		return isset($this->values[$key]) ? $this->values[$key] : null;
	}
}
