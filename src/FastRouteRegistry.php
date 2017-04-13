<?php
namespace ngyuki\RouteCollector;

use FastRoute\RouteCollector as FastRouteCollector;

class FastRouteRegistry implements RouteRegistryInterface
{
    private $fastRouteCollector;

    public function __construct(FastRouteCollector $fastRouteCollector)
    {
        $this->fastRouteCollector = $fastRouteCollector;
    }

    public function addRoute(array $method, $path, array $params)
    {
        $this->fastRouteCollector->addRoute($method, $path, $params);
    }
}
