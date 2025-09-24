<?php

namespace Cheechstack\Routing;

use Cheechstack\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{
    protected array $routeBag;

    public function __construct()
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

    /** Handle incoming requests.
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request) : Response
    {
        // Search through the route bag for a matching route
        $routeIndex = $this->findRoute($request->getPathInfo(), $request->getMethod());

        // Route could not be located. Return 404
        if ($routeIndex < 0) {
            return $this->errorResponse(404);
        }

        /** Set the target route and fill out the route's parameter array
         * @var Route $targetRoute */
        $targetRoute = $this->routeBag[$routeIndex];
        $targetRoute->resolvePathParams($request->getPathInfo());

        // All Controller Methods should return a response object
        $response = call_user_func($targetRoute->getCallable(), $request, $targetRoute->getPathParameters());

        if ($response instanceof Response) {
            return $response;
        } else {
            return new Response($response, 200);
        }
    }

    /** Returns the index of the matching route if found. Returns -1 on failure.
     *
     * @param string $path
     * @param string $method
     * @return int
     */
    public function findRoute(string $path, string $method) : int
    {
        $bagSize = count($this->routeBag);
        for ($i = 0; $i < $bagSize; $i++) {
            $route = $this->routeBag[$i];

            if ($route->matches($path, $method)) {
                return $i;
            }
        }

        return -1;
    }

    /** Returns an error response.
     *
     * @param int $code
     * @param ?string $message
     * @return Response
     */
    public function errorResponse(int $code = 500, ?string $message = null) : Response
    {
        $statusLookup = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
        ];

        if (is_null($message)) {
            $message = $statusLookup[$code] ?? "Unknown Error";
        }

        return new Response($message, $code);
    }

    /** Return the routes from the route bag.
     *
     * @return Route[]
     */
    public function routes() : array {
        return $this->routeBag;
    }


}