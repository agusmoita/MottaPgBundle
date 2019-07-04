# MottaPgBundle
---
#### Installation
- Add via composer
    ```console
    $ composer require agusmoita/mottapg-bundle
    ```

#### Usage
- Controller
    ```php
    use MottaPgBundle\Util\Paginator\Paginator;
    
    /**
     * @Route("/", name="person_index", methods={"GET|POST"})
     */
    public function indexAction(Paginator $pg, PersonRepository $repo)
    {
        return $pg
                ->setView('person/index.html.twig')
                ->paginate($repo);
    }
    ```
    
- Repository
    ```php
    // PersonRepository.php
    
    public function buildQuery($query, $pg)
    {
        // return SELECT a FROM AppBundle:Person a
        return $query;
    }
    ```

- View
    ```twig
    {# person/index.html.twig #}
    
    {% extends '@MottaPg/Paginator/table.html.twig' %}
    
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
