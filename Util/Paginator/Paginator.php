<?php

namespace MottaPgBundle\Util\Paginator;

use Symfony\Component\HttpFoundation\JsonResponse;
use MottaPgBundle\Form\PaginatorFormType;

/**
 * Paginador
 */
class Paginator implements PaginatorInterface
{
    /**
     * Id del formulario
     *
     * @var string
     */
    private $id;

    /**
     * Servicio para acceder a otros servicios
     *
     * @var Container
     */
    private $container;

    /**
     * Servicio encargado de hacer submit del formulario
     *
     * @var FormRemember
     */
    private $formRemember;

    /**
     * Servicio encargado de hacer las consultas a la base de datos
     *
     * @var DqlQueryHelper
     */
    private $queryHelper;

    /**
     * Servicio encargado de hacer las consultas a la base de datos
     *
     * @var SqlQueryHelper
     */
    private $sqlQueryHelper;

    /**
     * Request actual
     *
     * @var Request
     */
    private $request;

    /**
     * Determina si se debe recordar la última búsqueda realizada
     *
     * @var bool
     */
    private $remember;

    /**
     * Arreglo con los valores por defecto que toma el formulario
     *
     * @var array
     */
    private $defaultData;

    /**
     * Arreglo donde se setea el formulario de filtros,
     * las opciones de orden y las de cantidad de resultados por página
     *
     * @var array
     */
    private $options;

    /**
     * Arreglo que determina que componentes de la vista hay que ocultar
     *
     * @var array
     */
    private $hide;

    /**
     * Listado de exportaciones en excel y/o pdf
     *
     * @var Export[]
     */
    private $exports;

    /**
     * Listado de MassAction
     *
     * @var MassAction[]
     */
    private $massActions;

    /**
     * Arreglo con los parámetros adicionales que se le quieren pasar a la vista
     *
     * @var array
     */
    public $params;

    /**
     * Arreglo con los parámetros adicionales que se le quieren pasar
     * a la consulta que realiza el queryHelper
     *
     * @var array
     */
    private $extraData;

    /**
     * Arreglo con los nombres de los campos que se quieren
     * eliminar de los filtros de búsqueda en la vista
     *
     * @var string[]
     */
    private $remove;

    /**
     * Valor que define si el panel de filtros se debe
     * mostrar abierto o cerrado al cargar la página
     *
     * @var string
     */
    private $panelState;

    /**
     * Nombre del archivo twig del cual extiende la vista del paginador
     *
     * @var string
     */
    private $layout;

    /**
     * Nombre de la vista que se debe entregar
     *
     * @var string
     */
    private $view;

    /**
     * Valor que define si los filtros se muestran dentro
     * de un panel desplegable o no
     *
     * @var string
     */
    private $filterTheme;

    /**
     * Valor que define si usa una consulta sql
     *
     * @var boolean
     */
    private $sql;

    /**
     * Valor que define si se tiene que reiniciar la tabla
     *
     * @var boolean
     */
    private $clean;

    /**
     * Constructor
     *
     * @param Container $container
     * @param FormRemember $formRemember
     * @param DqlQueryHelper $queryHelper
     * @param SqlQueryHelper $sqlQueryHelper
     */
    public function __construct($container, $formRemember, $queryHelper, $sqlQueryHelper)
    {
        $this->id = uniqid("form-");
        $this->container = $container;
        $this->formRemember = $formRemember;
        $this->queryHelper = $queryHelper;
        $this->sqlQueryHelper = $sqlQueryHelper;
        $this->request = $formRemember->getRequest();
        $this->remember = true;
        $this->defaultData = array(
            'page' => 1,
            'rowsPerPage' => 10,
            'orderBy' => null,
            'direction' => 'asc',
            'massIds' => '',
            'massAll' => false,
            'clean' => false
        );
        $this->options = array(
            'orderByChoices' => array()
        );
        $this->exports = array();
        $this->massActions = array();
        $this->extraData = array();
        $this->remove = array();
        $this->sql = false;
        $this->clean = false;
    }

    /**
     * Carga los valores definidos en config.yml o los por defecto
     *
     * @param string $filterTheme
     * @param string $layout
     */
    public function setConfig($filterTheme, $layout, $panelState, $showOrder, $showCant, $showRows, $pageNav)
    {
        $this->filterTheme = $filterTheme;
        $this->layout = $layout;
        $this->panelState = $panelState;
        $this->showOrder($showOrder)
             ->showRowsPerPage($showCant)
             ->showRowsAtFirst($showRows)
             ->alwaysShowPageNav($pageNav);
    }

    /**
     * Método encargado de devolver la respuesta
     * Debe ser el último en llamarse
     *
     * @param string $repoName
     * @param string $queryFunc
     * @param string $em
     * @return Response
     */
    public function paginate($repoName, $queryFunc = 'buildQuery', $em = null)
    {
        $form = $this->createForm();

        $this->setRepository($repoName, $em);

        $this->removeFilters($form);

        $data = $this->getFormData($form);

        $validFilters = $this->getDataFilters($data);

        $this->addData($data);

        if ($mass = $this->getMassRequest()) {
            return $mass->process($this->getQueryHelper(), $queryFunc, $data, $this->request);
        }
        if ($export = $this->getExportRequest()) {
            return $export->process($this->findAll($queryFunc, $data));
        }

        $entities = $this->getEntities($queryFunc, $totalRows, $totalPages, $data, $validFilters);

        return $this->createResponse($form, $entities, $totalRows, $totalPages);
    }

    /**
     * Método encargado de devolver la respuesta
     * usando una consulta sql
     * Debe ser el último en llamarse
     *
     * @param string $repoName
     * @param string $queryFunc
     * @param string $em
     * @return Response
     */
    public function sqlPaginate($repoName, $queryFunc = 'buildQuery', $em = null)
    {
        return $this->sql()->paginate($repoName, $queryFunc, $em);
    }

    /**
     * Determina que se va a usar una
     * consulta sql
     *
     * @return Paginador
     */
    private function sql()
    {
        $this->sql = true;

        return $this;
    }


    /**
     * Define el nombre de la vista que se debe entregar
     *
     * @param string $html
     * @return Paginador
     */
    public function setView($html)
    {
        $this->view = $html;

        return $this;
    }

    /**
     * Setea el nombre del formulario a usar para
     * el filtro de búsqueda
     *
     * @param string $filter
     * @return Paginador
     */
    public function setFilter($filter)
    {
        $this->options['filter'] = $filter;

        return $this;
    }

    /**
     * Setea el nombre del formulario a usar para
     * los MassAction
     *
     * @param string $filter
     * @return Paginador
     */
    public function setMassActionForm($form)
    {
        $this->options['massactionForm'] = $form;

        return $this;
    }

    /**
     * Setea los nombres de los campos que se deben remove
     * del filtro de búsqueda en la vista
     *
     * @param string[] $filters
     * @return Paginador
     */
    public function removeFilters(array $filters)
    {
        $this->remove = $filters;

        return $this;
    }

    /**
     * Setea los parámetros adicionales que se le quieren
     * pasar a la vista
     *
     * @param array $params
     * @return Paginador
     */
    public function addViewParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Setea los parámetros adicionales que se le quieren
     * pasar a la consulta que realiza el queryHelper
     *
     * @param array $params
     * @return Paginador
     */
    public function addQueryParams(array $params)
    {
        $this->extraData = $params;

        return $this;
    }

    /**
     * Agrega una opción de exportación a excel
     *
     * @param string $header
     * @param string $row
     * @param string $fileName
     * @param string $tooltip
     * @return Paginador
     */
    public function addExcelExport($header, $row, $fileName, $tooltip = null)
    {
        $tooltip = $tooltip ?: $this->get('translator')->trans('Download');
        $i = count($this->exports) + 1;
        $export = $this->container
                    ->get('motta.export.excel')
                    ->create($i, $header, $row, $fileName, $tooltip);
        $this->exports[$export->id] = $export;

        return $this;
    }

    /**
     * Agrega una opción de exportación a pdf
     *
     * @param string $header
     * @param string $row
     * @param string $fileName
     * @param string $title
     * @param string $tooltip
     * @return Paginador
     */
    public function addPdfExport($header, $row, $fileName, $title = null, $tooltip = null)
    {
        $tooltip = $tooltip ?: $this->get('translator')->trans('Download');
        $i = count($this->exports) + 1;
        $export = $this->container
                    ->get('motta.export.pdf')
                    ->create($i, $header, $row, $fileName, $title, $tooltip);
        $this->exports[$export->id] = $export;

        return $this;
    }

    /**
     * Agrega una opción de acción en masa
     *
     * @param string $title
     * @param string $callback
     * @param array $parameters
     * @param string $confirmationModal
     * @return Paginador
     */
    public function addMassAction($title, $callback, array $parameters = array(), $confirmationModal = '')
    {
        $i = count($this->massActions) + 1;
        $massaction = $this->container
                        ->get('motta.massaction')
                        ->create($i, $title, $callback, $parameters, $confirmationModal);
        $this->massActions[$massaction->id] = $massaction;
        return $this;
    }

    /**
     * Setea las opciones por las que el usuario puede
     * ordenar la tabla
     *
     * @param array $choices
     * @param string $default
     * @param string $direction
     * @return Paginador
     */
    public function setOrder(array $choices, $default = null, $direction = 'asc')
    {
        $this->options['orderByChoices'] = $choices;
        if (!$default) {
            reset($choices);
            $default = $choices[key($choices)];
        }
        $this->defaultData['orderBy'] = $default;
        $this->defaultData['direction'] = $direction;

        return $this;
    }

    /**
     * Determina si el combo con las opciones
     * de orden se debe mostrar
     *
     * @return Paginador
     */
    public function showOrder($show = true)
    {
        $this->hide['order'] = !$show;

        return $this;
    }

    /**
     * Setea la página por defecto
     *
     * @param int $page
     * @return Paginador
     */
    public function setPage($page)
    {
        $this->defaultData['page'] = $page;

        return $this;
    }

    /**
     * Setea las cantidades de resultados que se muestran por página
     *
     * @param int $rows
     * @param array $choices
     * @return Paginador
     */
    public function setRowsPerPage($rows, $choices = null)
    {
        $this->defaultData['rowsPerPage'] = $rows;

        if ($choices) {
            $ch = array();
            foreach ($choices as $key => $value) {
                $ch[intval($value)] = $value;
            }
            $this->options['rowsPerPageOptions'] = $ch;
        }

        return $this;
    }

    /**
     * Determina si el combo de cantidades de
     * resultados se debe mostrar
     *
     * @return Paginador
     */
    public function showRowsPerPage($show = true)
    {
        $this->hide['rowsPerPage'] = !$show;

        return $this;
    }

    /**
     * Determina si se debe hacer la consulta a la
     * base de datos a pesar de no hacer una búsqueda
     * con los filtros
     *
     * @return Paginador
     */
    public function showRowsAtFirst($show = true)
    {
        $this->hide['list'] = !$show;

        return $this;
    }

    /**
     * El panel desplegable se muestra cerrado al
     * cargar la página
     *
     * @return Paginador
     */
    public function closePanel()
    {
        $this->panelState = 'closed';

        return $this;
    }

    /**
     * El panel desplegable se muestra abierto al
     * cargar la página
     *
     * @return Paginador
     */
    public function openPanel()
    {
        $this->panelState = 'open';

        return $this;
    }

    /**
     * Determina que no se debe recordar la última
     * búsqueda al cargar la página
     *
     * @return Paginador
     */
    public function noRemember()
    {
        $this->remember = false;

        return $this;
    }

    /**
     * Determina si los filtros se muestran dentro
     * de un panel desplegable
     *
     * @param string $theme
     * @return Paginador
     */
    public function setFiltersTheme($theme)
    {
        $this->filterTheme = $theme;

        return $this;
    }

    /**
     * @param string $layout
     * @return Paginador
     */
    public function setBaseLayout($layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Define si muestra la navegación de páginas
     * si sólo hay una página
     * @param  boolean $pageNav
     * @return Paginador
     */
    public function alwaysShowPageNav($pageNav = true)
    {
        $this->hide['pageNav'] = $pageNav;

        return $this;
    }

    /**
     * @return Form
     */
    private function createForm()
    {
        return $this->container->get('form.factory')
               ->create(
                   PaginatorFormType::class,
                   $this->defaultData,
                   $this->options
                );
    }

    /**
     * @param string $repoName
     * @param string $em
     * @return Paginador
     */
    private function setRepository($repoName, $em)
    {
        $this->getQueryHelper()->setRepository($repoName, $em);

        return $this;
    }

    /**
     * @return QueryHelper
     */
    private function getQueryHelper()
    {
        return $this->sql ? $this->sqlQueryHelper : $this->queryHelper;
    }

    /**
     * @param Form $form
     * @return Paginador
     */
    private function removeFilters(&$form)
    {
        if ($this->remove) {
            foreach ($this->remove as $filter) {
                $form->get('filters')->remove($filter);
            }
        }

        return $this;
    }

    /**
     * @param Form $form
     * @return array
     */
    private function getFormData(&$form)
    {
        if ($this->isAjax()) {
            $ajaxData = $this->request->request->get($form->getName());
            $form->setData($ajaxData);
        }

        return $this->submit($form);
    }

    /**
     * @param Form $form
     * @return array
     */
    private function submit(&$form)
    {
        $this->clean = $this->formRemember->remember($form, $this->remember, $this->defaultData);
        $data = $this->clean ? $this->defaultData : $form->getData();

        return $this->dataValida($data);
    }

    /**
     * @return boolean
     */
    private function isAjax()
    {
        return $this->request->isXmlHttpRequest();
    }

    /**
     * @return boolean
     */
    private function isGET()
    {
        return $this->request->isMethod('GET') || $this->clean == "true";
    }

    /**
     * @param array $data
     * @return array
     */
    private function dataValida($data)
    {
        foreach ($data as $key => $value) {
            if ($key != 'filtros') {
                if ($value == null && $this->defaultData[$key] != $value) {
                    return $this->defaultData;
                }
            }
            else {
                return $value != null ? $data : $this->defaultData;
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @return boolean
     */
    private function getDataFilters($data)
    {
        $filters = isset($data['filters']) ? $data['filters'] : null;
        $filtersHasValues = $this->filtersHasValues($filters);
        $filterOption = isset($this->options['filter']) ? true : false;

        return ( (!$filters && !$filterOption) || $filtersHasValues );
    }

    /**
     * @param array $filtros
     * @return boolean
     */
    private function filtersHasValues($filters)
    {
        return $this->getQueryHelper()->filtersHasValues($filters);
    }

    /**
     * @param array $data
     * @return Paginador
     */
    private function addData(&$data)
    {
        if ($this->extraData) {
            $data['filters'] = isset($data['filters'])
                                ? array_merge($data['filters'], $this->extraData)
                                : $this->extraData;
        }

        return $this;
    }

    /**
     * @return MassAction
     */
    private function getMassRequest()
    {
        if ($massId = $this->getRequestQuery('mass')) {
            return $this->getMassAction($massId);
        }

        return null;
    }

    /**
     * @param string $id
     * @return MassAction
     */
    private function getMassAction($id)
    {
        return $this->massActions[$id];
    }

    /**
     * @return Export
     */
    private function getExportRequest()
    {
        if ($this->isGET()) {
            if ($exportId = $this->getRequestQuery('export')) {
                return $this->getExport($exportId);
            }
        }

        return null;
    }

    /**
     * @param string $id
     * @return Export
     */
    private function getExport($id)
    {
        return $this->exports[$id];
    }

    /**
     * @param string $q
     * @return string
     */
    private function getRequestQuery($q)
    {
        return $this->request->query->get($q);
    }

    /**
     * @param string $queryFunc
     * @param int $totalPages
     * @param int $totalRows
     * @param array $data
     * @return array
     */
    private function find($queryFunc, &$totalPages, &$totalRows, $data)
    {
        return $this->getQueryHelper()->find($queryFunc, $totalPages, $totalRows, $data);
    }

    /**
     * @param string $queryFunc
     * @param array $data
     * @return array
     */
    private function findAll($queryFunc, $data)
    {
        return $this->getQueryHelper()->findAll($queryFunc, $data);
    }

    /**
     * @param string $queryFunc
     * @param int $totalRows
     * @param int $totalPages
     * @param array $data
     * @param boolean $validFilters
     * @return array
     */
    private function getEntities($queryFunc, &$totalRows = 0, &$totalPages = 0, $data, $validFilters)
    {
        $entities = array();
        $totalRows = 0;
        $totalPages = 0;
        if (!$this->isGET() || !$this->hide['list'] || $validFilters || $this->defaultDataChanged($data)) {
            $entities = $this->find($queryFunc, $totalPages, $totalRows, $data);
            $this->hide['list'] = false;
        }

        return $entities;
    }

    /**
     * @param array $data
     * @return boolean
     */
    private function defaultDataChanged($data)
    {
        if (
            $data['page'] != $this->defaultData['page']
            || $data['rowsPerPage'] != $this->defaultData['rowsPerPage']
            || $data['orderBy'] != $this->defaultData['orderBy']
            || $data['direction'] != $this->defaultData['direction']
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param Form $form
     * @param array $entities
     * @param int $totalRows
     * @param int $totalPages
     * @return Response|JsonResponse|array
     */
    private function createResponse($form, $entities, $totalRows, $totalPages)
    {
        return $this->isAjax()
                ? $this->createAjaxPage($entities, $totalRows, $totalPages)
                : $this->createPage($form, $entities, $totalRows, $totalPages);
    }

    /**
     * @param array $entities
     * @param int $totalRows
     * @param int $totalPages
     * @return JsonResponse
     */
    private function createAjaxPage($entities, $totalRows, $totalPages)
    {
        $twig = $this->container->get('twig');
        $_template = $this->request->get('_template');
        if ($_template) {
            $templateBundle = $_template->get('bundle').':';
            $templateController = $_template->get('controller').':';
            $templateName = $_template->get('name').'.html.twig';
            $template = $twig->loadTemplate($templateBundle.$templateController.$templateName);
        }
        else {
            $template = $twig->loadTemplate($this->view);
        }
        $params = array_merge(
            array(
                'paginator' => array(
                    'entities' => $entities,
                    'massActions' => $this->massActions
                )
            ), $this->params ? $this->params : array()
        );
        $html = $template->renderBlock('page_row', $twig->mergeGlobals($params), array(), false);

        $response = array(
            'html' => $html,
            'totalRows' => $totalRows,
            'totalPages' => $totalPages
        );

        return new JsonResponse($response);
    }

    /**
     * @param Form $form
     * @param array $entities
     * @param int $totalRows
     * @param int $totalPages
     * @return Response|array
     */
    private function createPage($form, $entities, $totalRows, $totalPages)
    {
        $templating = $this->container->get('templating');
        $paginator = array_merge(
            array(
                'paginator' => new Page(
                    $this->id,
                    $form->createView(),
                    $entities,
                    $totalPages,
                    $totalRows,
                    $this->exports,
                    $this->massActions,
                    $this->panelState,
                    $this->hide,
                    $this->filterTheme,
                    $this->layout
                )
            ), $this->params ? $this->params : array()
        );
        return $this->view ? $templating->renderResponse($this->view, $paginator) : $paginator;
    }

}
