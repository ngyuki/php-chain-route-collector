<?php
namespace ngyuki\RouteCollector\Tests;

use PHPUnit\Framework\TestCase;
use FastRoute;

class FastRouteTest extends TestCase
{
    function generateFastRouteCallback(callable $callback)
    {
        return function(FastRoute\RouteCollector $collector) use ($callback) {
            $callback(new ActionRouteCollector(new FastRouteRegistry($collector)));
        };
    }

    /**
     * @test
     * @dataProvider fast_route_integrate_data
     *
     * @param $method
     * @param $path
     * @param $handler
     * @param $params
     */
    function fast_route_integrate($method, $path, $handler, $params)
    {
        $dispatcher = FastRoute\simpleDispatcher($this->generateFastRouteCallback(function (ActionRouteCollector $r) {

            // GET / -> HomeController::index
            $r->path('/')->get()->controller('HomeController')->action('index');

            // GET|POST /both -> HomeController::both
            $r->path('/both')->get()->post()->controller('HomeController')->action('both');

            $r->controller('UserController')->group(function (ActionRouteCollector $r) {
                $r->path('/user')->group(function (ActionRouteCollector $r) {

                    // GET /user -> UserController::index
                    $r->get()->action('index');

                    // GET /user/create -> UserController::create
                    $r->path('/create')->get()->action('create');

                    // POST /user/create -> UserController::store
                    $r->path('/create')->post()->action('store');
                });
                $r->path('/user/{id}')->group(function (ActionRouteCollector $r) {

                    // GET /user/{id} -> UserController::show
                    $r->get()->action('show');

                    // GET /user/{id}/edit -> UserController::edit
                    $r->path('/edit')->get()->action('edit');

                    // PUT /user/{id}/edit -> UserController::update
                    $r->path('/edit')->put()->action('update');

                    // DELETE /user/{id}/edit -> UserController::delete
                    $r->path('/edit')->delete()->action('delete');
                });
            });
        }));

        $res = $dispatcher->dispatch($method, $path);

        self::assertEquals($handler, $res[1]);
        self::assertEquals($params, $res[2]);
    }

    function fast_route_integrate_data()
    {
        return [
            ['GET', '/', ['controller' => 'HomeController', 'action' => 'index'], []],
            ['GET', '/', ['controller' => 'HomeController', 'action' => 'index'], []],
            ['GET', '/both', ['controller' => 'HomeController', 'action' => 'both'], []],
            ['POST', '/both', ['controller' => 'HomeController', 'action' => 'both'], []],
            ['GET', '/user', ['controller' => 'UserController', 'action' => 'index'], []],
            ['GET', '/user/create', ['controller' => 'UserController', 'action' => 'create'], []],
            ['POST', '/user/create', ['controller' => 'UserController', 'action' => 'store'], []],
            ['GET', '/user/123', ['controller' => 'UserController', 'action' => 'show'], ['id' => 123]],
            ['GET', '/user/456/edit', ['controller' => 'UserController', 'action' => 'edit'], ['id' => 456]],
            ['PUT', '/user/789/edit', ['controller' => 'UserController', 'action' => 'update'], ['id' => 789]],
            ['DELETE', '/user/999/edit', ['controller' => 'UserController', 'action' => 'delete'], ['id' => 999]],
        ];
    }
}
