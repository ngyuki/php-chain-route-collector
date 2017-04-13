<?php
namespace ngyuki\RouteCollector;

interface RouteRegistryInterface
{
    public function addRoute(array $method, $path, array $params);
}
