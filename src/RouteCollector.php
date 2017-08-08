<?php
namespace ngyuki\RouteCollector;

class RouteCollector
{
    /**
     * @var RouteRegistryInterface
     */
    private $registry;

    /**
     * @var array
     */
    private $method = [];

    /**
     * @var string|null
     */
    private $path;

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var bool
     */
    private $last = false;

    public function __construct(RouteRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function __destruct()
    {
        if ($this->last) {
            $this->registry->addRoute($this->method, $this->path, $this->params);
        }
    }

    /**
     * @return static
     */
    private function chain()
    {
        $this->last = false;
        $new = clone $this;
        $new->last = true;
        return $new;
    }

    /**
     * @param string $method
     * @return static
     */
    public function method($method)
    {
        $new = $this->chain();
        foreach (explode('|', $method) as $m) {
            $new->method[] = strtoupper($m);
        }
        return $new;
    }

    /**
     * @return static
     */
    public function get()
    {
        $new = $this->chain();
        $new->method[] = strtoupper(__FUNCTION__);
        return $new;
    }

    /**
     * @return static
     */
    public function post()
    {
        $new = $this->chain();
        $new->method[] = strtoupper(__FUNCTION__);
        return $new;
    }

    /**
     * @return static
     */
    public function put()
    {
        $new = $this->chain();
        $new->method[] = strtoupper(__FUNCTION__);
        return $new;
    }

    /**
     * @return static
     */
    public function patch()
    {
        $new = $this->chain();
        $new->method[] = strtoupper(__FUNCTION__);
        return $new;
    }

    /**
     * @return static
     */
    public function delete()
    {
        $new = $this->chain();
        $new->method[] = strtoupper(__FUNCTION__);
        return $new;
    }

    /**
     * @param string $path
     *
     * @return static
     */
    public function path($path)
    {
        $new = $this->chain();
        $new->path .= $path;
        return $new;
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return static
     */
    public function param($name, $value)
    {
        $new = $this->chain();
        $new->params[$name] = $value;
        return $new;
    }

    /**
     * @param array $params
     *
     * @return static
     */
    public function params(array $params)
    {
        $new = $this->chain();
        $new->params = array_merge($new->params, $params);
        return $new;
    }

    /**
     * @param string $name
     * @param array $arguments [$value]
     *
     * @return static
     */
    public function __call($name, $arguments)
    {
        return $this->param($name, $arguments[0]);
    }

    /**
     * @param callable $callback
     *
     * @return static
     */
    public function group(callable $callback)
    {
        $new = $this->chain();
        $new->last = false;
        $callback($new);
        return $new;
    }
}
