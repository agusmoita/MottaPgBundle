<?php

namespace MottaPgBundle\Util\Export;

/**
* Export
*/
abstract class Export
{
	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var string
	 */
	public $headerCall;

	/**
	 * @var string
	 */
	public $rowCall;

	/**
	 * @var string
	 */
	public $fileName;

	/**
	 * @var string
	 */
	public $tooltip;

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @param int $i
	 * @param string $headerCall
	 * @param string $rowCall
	 * @param string $fileName
	 * @param string $tooltip
	 * @return Export
	 */
	public function init($i, $headerCall, $rowCall, $fileName, $tooltip)
    {
        $this->headerCall = $this->getCallback($headerCall);
        $this->rowCall = $this->getCallback($rowCall);
        $this->fileName = $fileName;
        $this->tooltip = $tooltip;
        $this->id = $this->type.'-'.$i;

        return $this;
    }

    /**
	 * @param string $rowCall
	 * @return string
     */
	private function getCallback($rowCall)
	{
		list($bundle, $ctrl, $action) = explode(':', $rowCall);

		return $bundle."\Controller\\".$ctrl."Controller::".$action;
	}

	/**
	 * @return ExportHeader
	 */
	protected function getHeader()
	{
		$header = new Header();
        call_user_func_array($this->headerCall, array(&$header));

		return $header;
	}

	/**
	 * @return ExportRow
	 */
	protected function getRow($entity)
	{
		$row = new Row();
        call_user_func_array($this->rowCall, array($entity, &$row));

		return $row;
	}

	/**
	 * @param array $entities
	 * @return Response
	 */
	public abstract function process($entities);
}
