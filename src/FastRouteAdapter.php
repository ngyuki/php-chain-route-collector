<?php
namespace ngyuki\RouteCollector;

use FastRoute\RouteCollector as FastRouteCollector;

class FastRouteAdapter
{
    public static function callback(callable $callback)
    {
        return function(FastRouteCollector $fastRouteCollector) use ($callback) {
            $callback(new RouteCollector(new FastRouteRegistry($fastRouteCollector)));
        };
    }
}
