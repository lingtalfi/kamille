<?php


namespace Kamille\Utils\Routsy\LinkGenerator;


use Core\Services\X;
use Kamille\Utils\Routsy\RoutsyRouter;

/**
 * This is a service to generate links, provided by the framework, for the kamille developers/users.
 *
 *
 * Usage
 * ============
 * ApplicationLinkGenerator::getUri("Core_myRouteId5", [
 *      'dynamic' => 46,
 * ]);
 *
 * See Routsy system documentation for more information.
 *
 */
class ApplicationLinkGenerator
{
    /**
     * @var $linkGen LinkGeneratorInterface
     */
    private static $linkGen;


    public static function getUri($routeId, array $params = [])
    {
        return self::getLinkGenerator()->getUri($routeId, $params);
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getLinkGenerator()
    {
        if (null === self::$linkGen) {
            /**
             * @var $router RoutsyRouter
             */
            $router = X::get("Core_RoutsyRouter");
            $routes = $router->getRoutes();
            self::$linkGen = LinkGenerator::create()->setRoutes($routes);
        }
        return self::$linkGen;
    }
}