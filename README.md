# CheechStack/Routing

Simple Router and Route classes to support the CheechStack PHP framework. This library depends on `symfony/http-foundation:^7.3`.

> **Note:** This library is under active development and is subject to change at any point.

## Installation:

CheechStack/Routing is available as a standalone package from Composer.

`composer require cheechstack/routing`

## Usage:

1. Create a `Router` object.

    ```php
    use Cheechstack\Routing\Router;
    
    $router = new Router();
    ```

2. Create your `Routes`...

    ```php
    use Cheechstack\Routing\Route;
    
    $routes = [
        new Route("/route1", "GET", fn() => 'callback1'),
        new Route("/route2", "GET", fn() => 'callback2'),
    ];
    ```

    ...and add them to the `Router`.

    ```php
    $router->add($routes);
    ```

    Routes may also be added one at a time.
    
    ```php
    $router->add(new Route('/', "GET", fn() => 'callback'));
    ```

3. Handle the request and send back the Router's response. 

    ```php
    $response = $router->handle($request);
   
    $response->send();
    ```
