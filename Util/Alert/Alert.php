<?php

namespace MottaPgBundle\Util\Alert;

/**
* Alert
*/
class Alert
{
	/**
	 * @var Session
	 */
	private $session;

	/**
	 * @param Session $session
	 */
	function __construct($session)
	{
		$this->session = $session;
	}

	/**
	 * @param string $msj
	 * @return Alert
	 */
	public function addSuccess($msj)
	{
		$this->add('success', $msj);

		return $this;
	}

	/**
	 * @param string $msj
	 * @return Alert
	 */
	public function addError($msj)
	{
		$this->add('error', $msj);

		return $this;
	}

	/**
	 * @param string $msj
	 * @return Alert
	 */
	public function addWarning($msj)
	{
		$this->add('warning', $msj);

		return $this;
	}

	/**
	 * @param string $msj
	 * @return Alert
	 */
	public function addInfo($msj)
	{
		$this->add('info', $msj);

		return $this;
	}

	/**
	 * @param string $type
	 * @param string $msj
	 * @return Alert
	 */
	public function add($type, $msj)
	{
		$this->session->getFlashBag()->add('alert_'.$type, $msj);

		return $this;
	}
}
