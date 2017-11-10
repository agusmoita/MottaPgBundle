<?php

namespace MottaPgBundle\Util\Paginator;

/**
* Page
*/
class Page
{
	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var Form
	 */
	public $form;

	/**
	 * @var array
	 */
	public $entities;

	/**
	 * @var int
	 */
	public $totalPages;

	/**
	 * @var int
	 */
	public $totalRows;

	/**
	 * @var Export[]
	 */
	public $exports;

	/**
	 * @var MassAction[]
	 */
	public $massActions;

	/**
	 * @var string
	 */
	public $panelState;

	/**
	 * @var array
	 */
	public $hide;

	/**
	 * @var string
	 */
	public $filterTheme;

	/**
	 * @var string
	 */
	public $layout;

	/**
	 * @param string $id
	 * @param Form $form
	 * @param array $entities
	 * @param int $totalPages
	 * @param int $totalRows
	 * @param array $exports
	 * @param array $massActions
	 * @param string $panelState
	 * @param array $hide
	 * @param string $filterTheme
	 * @param string $layout
	 */
	function __construct($id, $form, $entities, $totalPages, $totalRows, $exports, $massActions, $panelState, $hide, $filterTheme, $layout)
	{
		$this->id = $id;
		$this->form = $form;
		$this->entities = $entities;
		$this->totalPages = $totalPages;
		$this->totalRows = $totalRows;
		$this->exports = $exports;
		$this->massActions = $massActions;
		$this->panelState = $panelState;
		$this->hide = $hide;
		$this->filterTheme = $filterTheme;
		$this->layout = $layout;
	}
}
