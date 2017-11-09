# DesarrolloBundle

   _DesarrolloBundle fue creado con el propósito de incluir funcionalidades que sean de utilidad para los sistemas desarrollados en Symfony._

## Índice

   * [Instalación](#user-content-instalación)
   * [Paginador](#user-content-paginador)
     * [Uso Básico](#user-content-uso-básico)
     * [Métodos](#user-content-métodos)
     * [Armar Query](#user-content-armar-query)
     * [Vista](#user-content-vista)
       * [Bloques](#user-content-bloques)
       * [Ordenar Tabla](#user-content-ordenar-tabla)
     * [Exportar](#user-content-exportar)
     * [MassAction](#user-content-massaction)
     * [Ajax](#user-content-ajax)
   * [Anotaciones](#user-content-anotaciones)
     * [Breadcrumb](#user-content-breadcrumb)
       * [Configuración](#user-content-configuración)
       * [Uso](#user-content-uso)
   * [Avisos](#user-content-avisos)
   * [Componentes](#user-content-componentes)
   * [Validadores](#user-content-validadores)
   
## Instalación

  1. Copiar el bundle en **src/**
  2. Agregar el bundle al **AppKernel** de Symfony

  ```php
  # app/AppKernel.php

  public function registerBundles()
  {
      $bundles = array(
          ...
          new DesarrolloBundle\DesarrolloBundle()
      );
  }
  ```

## Paginador

### Uso básico
   
   - **Controller**

      ```php
      public function indexAction()
      {
        return $this->get('minsaludba.paginador')
              ->setView('factura/index.html.twig')
              ->paginate('AppBundle:Demo');
      }

      /**
       * @Template()
      */
      public function index2Action()
      {
        return $this->get('minsaludba.paginador')
              ->paginate('AppBundle:Demo');
      }
      ```

   - **Repository**

      ```php
      public function armarQuery($query, $paginador)
      {
        return $query;
      }
      ```

   - **View**

      ```twig
      {% extends 'DesarrolloBundle:Paginador:tabla.html.twig' %}

      {% block titulo %}
          Mi Listado
      {% endblock %}

      {% block paginado_tabla_encabezados %}
          <th>Campo 1</th>
          <th>Campo 2</th>
          <th>Campo 3</th>
      {% endblock %}

      {% block paginado_tabla_datos %}
          <td>{{ entity.campo1 }}</td>
          <td>{{ entity.campo2 }}</td>
          <td>{{ entity.campo3 }}</td>
      {% endblock %}
      ```
    
### Métodos

   - Usados en el Controller

     - **paginate**
     
        Parámetro | Tipo | Default | Descripción
        --- | --- | --- | --- 
        repoName | string | | Nombre del repositorio donde se encuentra la consulta
        queryFunc | string | armarQuery | Nombre de la función que arma la consulta
        em | string | null | Nombre del entity manager. Si es null usa el em por defecto
        
     - **setView**
     
        Parámetro | Tipo | Default | Descripción
        --- | --- | --- | ---
        html | string | | Html que va a mostrar

     - **setFilter**
     
        Parámetro | Tipo | Default | Descripción
        --- | --- | --- | --- 
        filter | string | | Setea el filtro de búsqueda

     - **removeFilters**
     
        Parámetro | Tipo | Default | Descripción
        --- | --- | --- | --- 
        filters | array | | Los filtros que se quieren eliminar de la vista

     - **addViewParams**
     
        Parámetro | Tipo | Default | Descripción
        --- | --- | --- | --- 
        params | array | | Parámetros adicionales que se le quieren pasar a la vista

     - **addQueryParams**
     
        Parámetro | Tipo | Default | Descripción
        --- | --- | --- | --- 
        params | array | | Datos extras que se le quieren pasar a la consulta

     - **addExcelExport**
     
        Parámetro | Tipo | Default | Descripción
        --- | --- | --- | --- 
        header | string | | Función que arma el encabezado
        row | string | | Función que arma cada fila de la tabla
        fileName | string | | Nombre del archivo generado (sin extensión)
        tooltip | striing | Descargar | Texto del tooltip cuando se pasa el mouse sobre el botón de exportar

     - **addPdfExport**
     
        Parámetro | Tipo | Default | Descripción
        --- | --- | --- | --- 
        header | string | | Función que arma el encabezado
        row | string | | Función que arma cada fila de la tabla 
        fileName | string | | Nombre del archivo generado (sin extensión)
        title | string | null | Título de los archivos pdf
        tooltip | striing | Descargar | Texto del tooltip cuando se pasa el mouse sobre el botón de exportar

     - **addMassAction**

       Parámetro | Tipo | Default | Descripción
       --- | --- | --- | ---
       title | string | | El nombre que se le muestra al usuario
       callback | string | | Nombre de la función que va a procesar los ids seleccionados
       parameters | array | array() | Parámetros adicionales que se le quieren pasar al callback
       confirmationModal | string | '' | Id del modal que se mostrará si se necesita una confirmación para realizar la acción

     - **setOrder**
     
        Parámetro | Tipo | Default | Descripción
        --- | --- | --- | --- 
        choices | array | | Las opciones por las que puede ordenar el usuario
        default | string | null | La opción por defecto por la que se va a ordenar
        direction | string | asc | El sentido del orden (asc ó desc)

     - **showOrder**
      
       Muestra la sección donde se muestran las opciones para ordenar la tabla

     - **showRowsAtFirst**

       Permite cargar resultados aunque no haya hecho una búsqueda

     - **setPage**
     
        Parámetro | Tipo | Default | Descripción
        --- | --- | --- | --- 
        page | integer | | Página que carga por defecto

     - **setRowsPerPage**
     
        Parámetro | Tipo | Default | Descripción
        --- | --- | --- | --- 
        rows | integer | | La cantidad de registros que muestra por página
        choices | array | array(10, 20, 30, 40, 50) | Las cantidades que puede seleccionar el usuario

     - **hideRowsPerPage**
      
       Oculta el combo con las opciones de cantidad por página


     - **closePanel**

       Cierra el panel desplegable con los filtros de búsqueda

     - **noRemember**

       Al entrar nuevamente a la página, no recuerda la última búsqueda.

      ```php
      # EJEMPLO
      return $this->get('minsaludba.paginador')
            ->setOrder(
              array('campo1' => 'Campo 1', 'campo2' => 'Campo 2'),
              'campo2',
              'desc'
            )
            ->setPage(1)
            ->setRowsPerPage(15, array(15, 30, 45))
            ->hideRowsPerPage()
            ->showOrder()
            ->closePanel()
            ->noRemember()
            ->showRowsAtFirst()
            ->setFilter(FiltroDemoType::class)
            ->removeFilters(array('campo3'))
            ->addQueryParams(array(
              'campo4' => 'S'
            ))
            ->addViewParams(array(
              'establecimiento' => $establecimiento
            ))
            ->addExcelExport(
              'AppBundle:Demo:exportHeader',
              'AppBundle:Demo:exportRow',
              'exportados'
            )
            ->addMassAction(
              'Ver Ids', 
              'AppBundle:Demo:verIds',
              array('establecimiento' => 4480),
              'modalVer'
            )
            ->setView('demo/index.html.twig')
            ->paginate(
              'AppBundle:Demo',
              'armarQuery',
              'pacientes'
            );
      ```

   - Usados en el Repository
      ```php
      # Obtiene el valor de un filtro
      public function getFilterValue($filter);

      # Me dice si la tabla está ordenada por un campo específico
      public function orderBy($key);
      ```
    
### Armar Query

  Esta operación se realiza en el repositorio en el método definido en **procesar** (_armarQuery_ por defecto), que recibe dos parámetros. El parámetro **$query** es una instancia de la clase _Doctrine\ORM\QueryBuilder_, por lo tanto se le puede aplicar todas las operaciones de consultas DQL.

  Además se dispone del parámetro **$paginador** para hacer uso de las funciones **getFilterValue** y **orderBy** del paginador.

   - Ejemplo

      ```php
      public function armarQuery($query, $paginador)
      {
          $query->leftJoin('a.campo2', 'b');
          $query->addSelect('b');

          if ($campo2 = $paginador->getFilterValue('campo2')) {
              $query->andWhere("UPPER(b.descripcion) LIKE UPPER('%{$campo2}%')");
          }

          if ($paginador->orderBy('campo2')) {
              $query->addOrderBy("b.descripcion", $paginador->direction);
          }

          return $query;
      }
      ```

   ###### Aclaración: 'a' es el alias de la tabla de la entidad que se quiere listar

### Vista

   - #### Bloques

     - **pg_header**

       *Bloque que contiene los elementos anteriores a la tabla*

     - **panelFiltros**

       *El panel que contiene al formulario de filtros*

     - **filtros**

       *Donde se muestran los filtros*

     - **botones**

       *Para agregar botones arriba de la tabla*

     - **listado**

       *Contiene la tabla*

     - **paginado_tabla_encabezados**

       *Donde se definen las columnas de la tabla*

     - **paginado_tabla_datos**

       *Donde se definen los valores de cada columna*

    - **massId y massValue**

       *Para definir los ids y valores de los checkbox de los MassAction si es necesario cambiarlo*

     - **pg_footer**

       *Para agregar elementos abajo de la tabla*

   - #### Ordenar tabla
   
      Existe la posibilidad de ordenar la tabla haciendo click en el encabezado de una columna. Para ello hay que agregarle la clase **ordenable** y el atributo **sort**, con un valor que corresponda a una clave definida en el método *setOrderBy* del paginador, al encabezado por el cual queremos ordenar la tabla.

      - Ejemplo
      
        ```twig
        {% block paginado_tabla_encabezados %}
          <th class="ordenable" sort="campo1">Campo 1</th>
          <th class="ordenable" sort="campo2">Campo 2</th>
          <th>Campo 3</th>
        {% endblock %}
        ```
        
### Exportar

Para exportar una tabla a excel ó pdf se usan los métodos **addExcelExport** y **addPdfExport** del paginador.

```php
public function indexAction()
{
    return $this->get('.paginador')
        ->addExcelExport(
            'AppBundle:Paciente:exportHeader', 
            'AppBundle:Paciente:exportRow',
            'pacientes')
        ->addPdfExport(
            'AppBundle:Paciente:exportHeader', 
            'AppBundle:Paciente:exportRow',
            'pacientes',
            'Listado de Pacientes')
        ->setView('paciente/index.html.twig')
        ->paginate('AppBundle:Paciente');
}
```

```php
    public static function exportHeader($header)
    {
        $header
            ->add('Nombre')
            ->add('Documento')
            ->add('Fecha de Nacimiento');
    }

    public static function exportRow($entity, $row)
    {
        $row
            ->add($entity->getNombre())
            ->add($entity->getDocumento())
            ->add($entity->getFechaNacimiento()->format('d/m/Y'));
    }
```

### MassAction

Para poder realizar una acción sobre un conjunto de filas se usa el método **addMassAction** y hay que agregar una función que reciba los ids de las filas seleccionadas, y parámetros adicionales de ser necesario.

```php
public function indexAction()
{
    return $this->get('.paginador')
        ->addMassAction(
            'Actualizar pacientes', 
            'AppBundle:Paciente:actualizar',
            array('valor' => 'S')),
            'modalActualizar'
        ->paginate('AppBundle:Paciente');
}

public function actualizarAction($ids, $valor)
{
  $em = $this->getDoctrine()->getManager();
  foreach ($ids as $id)
  {
    $paciente = $em->getRepository('AppBundle:Paciente')->find($id);
    
    $paciente->setValor($valor);

    $em->persist($paciente);
  }
  $em->flush();

  return $this->redirect($this->generateUrl('paciente'));
}
```

###### Aclaración: el primer parámetro de la función **debe** llamarse $ids

  Si se especificó un modal de confirmación (como en el ejemplo anterior), en el html hay que agregar el modal dentro del bloque **modals**:

  ```twig
    {% extends 'DesarrolloBundle:Paginador:tabla.html.twig' %}

    {% block modals %}
        {% embed 'DesarrolloBundle:Paginador:modal.html.twig' with {
            modalId: 'modalActualizar',
            modalTitle: 'Actualizar Pacientes',
            modalSubmit: 'Actualizar'
        } %}
            {% block modal_body %}
                <p><strong>¿Está seguro que quiere actualizar los pacientes seleccionados?</strong></p>
            {% endblock %}
        {% endembed %}
    {% endblock %}
  ```

- Parámetros y bloques:

  **modalId:** Id del modal

  **modalTitle:** Título del modal

  **modalSubmit:** Texto del botón de confirmación

  **modal_body:** Descripción de la acción

### Ajax
   Para que la tabla pagine usando ajax se tiene que extender de **tabla_ajax.html.twig** en lugar de tabla.html.twig. Todo lo demás se mantiene igual.

   - Ejemplo

     ```twig
     {% extends 'DesarrolloBundle:Paginador:tabla_ajax.html.twig' %}

     {% block paginado_tabla_encabezados %}
          <th>Campo 1</th>
          <th>Campo 2</th>
          <th>Campo 3</th>
      {% endblock %}

      {% block paginado_tabla_datos %}
          <td>{{ entity.campo1 }}</td>
          <td>{{ entity.campo2 }}</td>
          <td>{{ entity.campo3 }}</td>
      {% endblock %}
     ```
   
## Anotaciones

### Breadcrumb

#### Configuración
   Se debe agregar al archivo config.yml los siguientes parámetros dentro de la opción *breadcrumb* de min_salud_ba_desarrollo :
   - **indice**
      
     El sufijo del índice del breadcrumb en sesión. (por defecto: )

   - **root_title**
     
     El título que se mostrará para identificar a la página principal del sistema. (por defecto: Sistema)

   - **root_name**

     El name de la ruta de la página principal del sistema. (por defecto: default)

   Estos valores se deben obtener del archivo parameters.yml

  ```yaml
  # app/config/config.yml

 min_salud_ba_desarrollo:

    breadcrumb:
        root_title: %root_title%
        indice: %indice_breadcrumb%
        root_name: %root_name%
  ```

#### Uso
   
   Se debe agregar una anotación por cada ruta que se quiera mostrar en el breadcrumb (excepto la ruta de inicio).
   
   ```php

    use DesarrolloBundle\Annotations\Breadcrumb;

    /**
     * @Route("/{id}/show", name="paciente_show")
     * @Template()
     * @Breadcrumb("Listado de Pacientes", route="paciente")
     * @Breadcrumb("Ver Paciente")
     */
    public function showAction(Paciente $paciente)
    {
        return array(
            'entity' => $paciente,
        );
    }
   ```

   En este caso el breadcrumb generado sería **Sistema de Pacientes / Listado de Pacientes / Ver Paciente**

   A cada **@Breadcrumb** se le debe pasar como primer parámetro el texto que va a mostrar. Además se le puede pasar los siguientes parámetros:

   - **route**: El name del action.
   
   - **params**: Los parámetros que recibe la ruta.
   
   - **titleParams**: Los parámetros que recibe el texto a mostrar en el breadcrumb.
   
   - **dinamic**: Determina si la ruta se va a agregar al final del breadcrumb en lugar de generar un breadcrumb estático para esa ruta. Se usa cuando una página puede ser accedida desde distintas rutas.

   Para la ruta actual, los parámetros **route** y **params** no son necesarios definir.

##### Ejemplo
   ```php

   use DesarrolloBundle\Annotations\Breadcrumb;

    /**
     * @Route("/{id}/show", name="factura_show")
     * @Template()
     * @Breadcrumb("Listado de Facturas", route="factura")
     * @Breadcrumb("Factura %d", route="factura_show", params={"id": "$detalle.factura.id"}, titleParams={"$detalle.factura.numero"})
     * @Breadcrumb("Ver Detalle")
     */
    public function showAction(DetalleFactura $detalle)
    {
        return array(
            'entity' => $detalle,
        );
    }

    /**
     * @Route("/seleccionar", name="paciente_seleccionar")
     * @Template()
     * @Breadcrumb("Seleccionar Paciente", dinamic=true)
     */
    public function seleccionarAction()
    {
        return $this->get('.paginador')
                ->paginate('AppBundle:Paciente');
    }

   ```

   También se puede hacer uso de la anotación en el Controller para definir las rutas que comparten los breadcrumbs de todas las acciones de ese controlador.

##### Ejemplo
   ```php

   use DesarrolloBundle\Annotations\Breadcrumb;

   /**
     * Factura controller.
     *
     * @Route("/factura")
     * @Breadcrumb("Listado de Facturas", route="factura")
     */
   class FacturaController extends Controller
   {
     /**
     * @Route("/", name="factura")
     * @Template()
     */
     public function indexAction()
     {
       return $this->get('.paginador')
                ->paginate('AppBundle:Factura');
     }

     /**
     * @Route("/{id}/show", name="factura_show")
     * @Template()
     * @Breadcrumb("Factura %d", titleParams={"$factura.numero"})
     */
     public function showAction(Factura $factura)
     {
        return array(
            'entity' => $factura,
        );
     }
   }

   ```

   En estos casos los breadcrumbs comenzarían con **_root_title_ / Listado de Facturas** .

## Avisos

   Sirven para agregar mensajes de FlashBag con mayor facilidad.

   - Ejemplo

     ```php

     public function demoAction()
     {
       $this->get('.avisos')->addError('Hubo un error.');
       $this->get('.avisos')->addSuccess('Tarea completada con éxito');
       $this->get('.avisos')->addWarning('Advertencia!');
     }

     ```

## Componentes

   A continuación se detallan los componentes twig que dispone el bundle. 

   - #### acciones

     _Genera un dropdown para definir las acciones que puede acceder el usuario desde el listado de una entidad_.

     El listado de acciones se define adentro del bloque **acciones**.

     - Ejemplo
     
       ```twig
       {% extends 'DesarrolloBundle:Paginador:tabla.html.twig' %}

       {% block paginado_tabla_encabezados %}
           <th>Campo 1</th>
           <th>Campo 2</th>
           <th>Acciones</th>
       {% endblock %}

       {% block paginado_tabla_datos %}
           <td>{{ entity.campo1 }}</td>
           <td>{{ entity.campo2 }}</td>
           {% embed 'DesarrolloBundle:Componente:acciones.html.twig' %}
             {% block acciones %}
               <li>
                 <a href="{{ path('demo_show', { 'id': entity.id }) }}">
                   Ver
                 </a>
               </li>
               <li>
                 <a href="{{ path('demo_edit', { 'id': entity.id }) }}">
                   Modificar
                 </a>
               </li>
             {% endblock %}
           {% endembed %}
       {% endblock %}
       ```

   - #### flash_exito, flash_error y flash_warning
    
     _Se incluye para mostrar los mensajes de éxito, error y advertencia guardados en el FlashBag_

     - Ejemplo
     
       ```twig
       {% extends 'base.html.twig' %}

       {% block contenido %}
           {% include 'DesarrolloBundle:Componente:flash_error.html.twig' %}
           {% include 'DesarrolloBundle:Componente:flash_exito.html.twig' %}
           {% include 'DesarrolloBundle:Componente:flash_warning.html.twig' %}
       {% endblock %}
       ```

   - #### flash_message

     _Se usa para incluir los tres de componentes anteriores en una sola línea_

     - Ejemplo

       ```twig
       {% extends 'base.html.twig' %}

       {% block contenido %}
           {% include 'DesarrolloBundle:Componente:flash_message.html.twig' %}
       {% endblock %}
       ```

   - #### panel_ayuda

     _Genera un panel desplegable donde se muestra una ayuda de la página actual_.

     La ayuda se define adentro del bloque **descripcion**.

     - Ejemplo
     
       ```twig
       {% extends 'base.html.twig' %}

       {% block contenido %}
           {% embed 'DesarrolloBundle:Componente:panel_ayuda.html.twig' %}
               {% block descripcion %}
                   Esto es una ayuda
               {% endblock %}
           {% endembed %}
       {% endblock %}
       ```

   - #### boton_ayuda

     _Genera un botón que al hacer click despliega un panel de ayuda_

     La ayuda se define adentro del bloque **descripcion**. El Componente debe ir dentro del bloque **ayuda** del base.

     - Ejemplo

      ```twig
      {% extends 'base.html.twig' %}

      {% block ayuda -%}
          {% embed 'DesarrolloBundle:Componente:boton_ayuda.html.twig' %}
              {% block descripcion %}
                  Esto es una aiudaaa
              {% endblock %}
          {% endembed %}
      {% endblock %}
      ```

   - #### show_column
    
     _Genera un bloque que ocupa 6 columnas de Bootstrap que contiene un label y un valor_.

     Al componente hay que definirle los valores de los parámetros **label** y **field**.

     - Ejemplo
       ```twig
       {% extends 'base.html.twig' %}

       {% block contenido %}
           {% include 'DesarrolloBundle:Componente:show_column.html.twig' with {
             'label': 'Campo 1',
             'field': entity.campo1
           } %}
       {% endblock %}
       ```

## Validadores
 
   Por el momento el bundle dispone de los siguientes validadores:

   - #### Existe Expediente
    
     Valida que el expediente exista en el sistema de expedientes.

     La entidad debe tener las propiedades **expeEnte**, **expeNro**, **expeAnio** y **expeAlcance**.

     - Uso
     
       ```php
       use Doctrine\ORM\Mapping as ORM;
       use DesarrolloBundle\Validator\Constraints as Validar;

       /**
        * Expediente
        *
        * @ORM\Table()
        * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ExpedienteRepository")
        * @Validar\ExisteExpediente()
       */
       class Expediente
       {
           ...
           private $expeEnte;

           private $expeNro;

           private $expeAnio;

           private $expeAlcance;
           ...
       }
       ```

   - #### Cuil

     Valida que un cuil sea válido.

     La entidad debe tener la propiedad **cuil**.

     - Uso
     
       ```php
       use Doctrine\ORM\Mapping as ORM;
       use DesarrolloBundle\Validator\Constraints as Validar;

       /**
        * Persona
        *
        * @ORM\Table()
        * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\PersonaRepository")
        * @Validar\Cuil()
       */
       class Persona
       {
           ...
           private $cuil;
           ...
       }
       ```

   - #### Cuil y Documento
    
     Valida que un cuil sea válido y que corresponda a un número de documento.

     La entidad debe tener las propiedades **cuil** y **documento**.

     - Uso
     
       ```php
       use Doctrine\ORM\Mapping as ORM;
       use DesarrolloBundle\Validator\Constraints as Validar;

       /**
        * Persona
        *
        * @ORM\Table()
        * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\PersonaRepository")
        * @Validar\CuilDocumento()
       */
       class Persona
       {
           ...
           private $cuil;

           private $documento;
           ...
       }
       ```

