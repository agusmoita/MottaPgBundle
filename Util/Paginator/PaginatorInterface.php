<?php

namespace MottaPgBundle\Util\Paginator;

interface PaginatorInterface
{
	/**
       * @param string $repoName
       * @param string $queryFunc
       * @param string $em
       * @return Response
       */
	public function paginate($repoName, $queryFunc = 'buildQuery', $em = null);

      /**
       * @param string $repoName
       * @param string $queryFunc
       * @param string $em
       * @return Response
       */
      public function sqlPaginate($repoName, $queryFunc = 'buildQuery', $em = null);

	/**
       * @param string $html
       * @return PaginadorInterface
       */
	public function setView($html);

	/**
       * @param string $filter
       * @return PaginadorInterface
       */
	public function setFilter($filter);

      /**
       * @param string $filter
       * @return PaginadorInterface
       */
      public function setMassActionForm($form);

	/**
       * @param array $filters
       * @return PaginadorInterface
       */
	public function removeFilters(array $filters);

	/**
       * @param array $params
       * @return PaginadorInterface
       */
	public function addViewParams(array $params);

	/**
       * @param array $params
       * @return PaginadorInterface
       */
	public function addQueryParams(array $params);

	/**
       * @param string $header
       * @param string $row
       * @param string $fileName
       * @param string $tooltip
       * @return PaginadorInterface
       */
	public function addExcelExport($header, $row, $fileName, $tooltip = null);

	/**
       * @param string $header
       * @param string $row
       * @param string $fileName
       * @param string $title
       * @param string $tooltip
       * @return PaginadorInterface
       */
	public function addPdfExport($header, $row, $fileName, $title = null, $tooltip = null);

	/**
       * @param string $title
       * @param string $callback
       * @param array $parameters
       * @param string $confirmationModal
       * @return PaginadorInterface
       */
	public function addMassAction($title, $callback, array $parameters = array(), $confirmationModal = '');

	/**
       * @param array $choices
       * @param string $default
       * @param string $direction
       * @return PaginadorInterface
       */
	public function setOrder(array $choices, $default = null, $direction = 'asc');

	/**
       * @param boolean $show
       * @return PaginadorInterface
       */
	public function showOrder($show = true);

	/**
       * @param boolean $show
       * @return PaginadorInterface
       */
	public function showRowsAtFirst($show = true);

	/**
       * @param int $page
       * @return PaginadorInterface
       */
	public function setPage($page);

	/**
       * @param int $rows
       * @param array $choices
       * @return PaginadorInterface
       */
	public function setRowsPerPage($rows, $choices = null);

	/**
       * @param boolean $show
       * @return PaginadorInterface
       */
	public function showRowsPerPage($show = true);

	/**
       * @return PaginadorInterface
       */
	public function closePanel();

     /**
      * @return PaginadorInterface
      */
     public function openPanel();

	/**
       * @return PaginadorInterface
       */
	public function noRemember();

     /**
      * @param string $theme
      * @return PaginadorInterface
      */
     public function setFiltersTheme($theme);

	/**
       * @param string $layout
       * @return PaginadorInterface
       */
	public function setBaseLayout($layout);

      /**
       * Define si muestra la navegación de páginas
       * si sólo hay una página
       * @param  boolean $pageNav
       * @return PaginadorInterface
       */
      public function alwaysShowPageNav($pageNav);
}
