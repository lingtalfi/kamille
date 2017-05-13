<?php


namespace Kamille\Utils\Routsy\LinkGenerator;


use Kamille\Services\XLog;

class LinkGenerator implements LinkGeneratorInterface
{

    private $routes;

    public function __construct()
    {
        $this->routes = [];
    }

    public static function create()
    {
        return new static();
    }


    public function getUri($routeId, array $params = [])
    {
        if (array_key_exists($routeId, $this->routes)) {
            $route = $this->routes[$routeId];
            $uri = str_replace(['+}', '{/'], ['}', '{'], $route[0]);
            return str_replace(array_map(function ($v) {
                return '{' . $v . '}';
            }, array_keys($params)), array_values($params), $uri);
        }
        XLog::error("Route not found: $routeId");
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
        return $this;
    }


}