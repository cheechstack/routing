<?php

namespace Cheechstack\Routing;

use Cheechstack\Routing\Route;

class Router
{
    protected array $routeBag;
    public function __construct(array $routesFiles = [])
    {
        // todo: load routes from files
        $this->routeBag = array();
    }

    /** Add new Route objects to the Router's bag.
     *
     * @param Route[]|Route $routes
     */
    public function add(array|Route $routes) : Router
    {
        // Add a bunch of Route objects
        if (is_array($routes)) {
            foreach ($routes as $route) {
                // Check that $route is of type Route
                if ($route instanceof Route) {
                    $this->routeBag[] = $route;
                } else {
                    // Warn that the route wasn't added.
                    trigger_error("Expected Route object, got " . gettype($route), E_USER_WARNING);
                }
            }
        }
        // Add a single Route object
        else {
            // Check that $route is of type Route
            if ($routes instanceof Route) {
                $this->routeBag[] = $routes;
            } else {
                // Warn that the route wasn't added.
                trigger_error("Expected Route, got " . gettype($routes), E_USER_WARNING);
            }
        }

        return $this;
    }
}