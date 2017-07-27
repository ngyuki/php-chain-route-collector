<?php
namespace ngyuki\RouteCollector\Tests;

use FastRoute\RouteCollector as FastRouteCollector;
use ngyuki\RouteCollector\RouteRegistryInterface;

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
