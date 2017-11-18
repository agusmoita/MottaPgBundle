# MottaPgBundle
---
#### Installation
- Add via composer
    ```console
    $ composer require agusmoita/mottapg-bundle
    ```
    and add to AppKernel.php
    
    ```php
    public function registerBundles()
    {
        $bundles = [
            ...
            new MottaPgBundle\MottaPgBundle(),
        ];
    }
    ```

#### Usage
- Controller
    ```php
    /**
     * @Route("/", name="person_index")
     * @Method("GET|POST")
     */
    public function indexAction(Request $request)
    {
        return $this->get('motta.pg')
                ->setView('person/index.html.twig')
                ->paginate('AppBundle:Person');
    }
    ```
    
- Repository
    ```php
    public function buildQuery($query, $pg)
    {
        // return SELECT a FROM AppBundle:Person a
        return $query;
    }
    ```

- View
    ```twig
    {% extends '@MottaPgBundle:Paginator:table.html.twig' %}
    
    {% block title %}
        <h1>List of People</h1>
    {% endblock %}
    
    {% block paginator_table_header %}
        <th>First Name</th>
        <th>Last Name</th>
        <th>Birthday</th>
    {% endblock %}
    
    {% block paginator_table_data %}
        <td>{{ entity.firstName }}</td>
        <td>{{ entity.lastName }}</td>
        <td>{{ entity.birthday|date('m-d-Y') }}</td>
    {% endblock %}
    ```
    
#### License
[MIT License](LICENSE)
