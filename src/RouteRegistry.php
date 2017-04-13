<?php
namespace ngyuki\RouteCollector;

class RouteRegistry implements RouteRegistryInterface
{
    /**
     * @var array
     */
    private $routes = [];

    public function addRoute(array $method, $path, array $params)
    {
        $this->routes[] = func_get_args();
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}
