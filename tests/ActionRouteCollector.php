<?php
namespace ngyuki\RouteCollector\Tests;

use ngyuki\RouteCollector\RouteCollector;

class ActionRouteCollector extends RouteCollector
{
    /**
     * @param string $controller
     * @return static
     */
    public function controller($controller)
    {
        return $this->param(__FUNCTION__, $controller);
    }

    /**
     * @param string $action
     * @return static
     */
    public function action($action)
    {
        return $this->param(__FUNCTION__, $action);
    }
}
