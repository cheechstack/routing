# CheechStack : Routing

Simple Router and Route classes to support the CheechStack PHP framework. This library depends on `symfony/http-foundation:^7.3`. Full documentation can be found on [GitHub.](https://github.com/cheechstack/routing)

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
        new Route("/foo", "GET", fn() => 'bar'),
        new Route("/marco", "GET", fn() => 'polo'),
    ];
    ```

    ...and add them to the `Router`.

    ```php
    $router->add($routes);
    ```

    Routes may also be added one at a time.
    
    ```php
    $router->add(new Route('/foo', "GET", fn() => 'bar'));
    $router->add(new Route('/marco', "GET", fn() => 'polo'));
    ```

3. Handle the request and send back the Router's response. 

    ```php
    $response = $router->handle($request);
   
    $response->send();
    ```

## Routes:

CheechStack supports two types of routes:

### 1. Static Routes

Static routes match a fixed URL path.

- Example:
    ```php
    $route = new Route("/foo/bar", "GET", fn() => "baz");
    ```
  - This route will **only** match to `/foo/bar`.
  - Great for endpoints that do not change i.e `/login`, `/about`, `/status`, etc...

### 2. Dynamic Routes

Dynamic routes include parameters in the path, defined with a `:` prefix. [Accessing the route parameters is covered here.](#accessing-path-parameters-within-callbacks)


```php
$route = new Route("/foo/:bar", "GET", fn() => "baz");
```
- This route will match to `/foo/bar`, `/foo/baz`, `/foo/ruh_roh_raggie`, etc...
- This route will **NOT** match `/foo/baz/tripped_up`.

## Route Callbacks:

The simplest form of callback for a `Route` is an anonymous function.

```php
// Returns the string "bar" when a request is sent to "/foo"

$route = new Route('/foo', "GET", fn() => 'bar') 
```

Callbacks can also be defined via static methods on Controllers. For example:
```php
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    // Return the "foo" page
    public static function foo() : Response
    {
        // Return "Bar!" as the content for the response.
        
        return new Response("Bar!", 200);
    }
}

$route = new Route('/foo', "GET", [Controller::class, 'foo']);
```

### Accessing Request Values Within Callbacks: 

To access the `Request` object from within the Route's callback, simply declare a `Request` object in the callback's definition. Both examples return the requested url.

As an anonymous function:
```php
$route = new Route('/foo', "GET", fn(Request $request) => $request->getPathInfo());
```
  
As a static method:
```php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    public static function foo(Request $request) : Response
    {
        return new Response($request->getPathInfo(), 200);
    }
}

$route = new Route("/foo", "GET", [Controller::class, 'foo']);
```

### Accessing Path Parameters Within Callbacks:

To access any path parameters used within your route, you may also pass a `$params` variable of `mixed` type **in addition** to the `Request` object. Parameters are then available in an array-like structure and accessible via their placeholder value. 

As an anonymous function:
```php
$route = new Route('/foo/:bar', "GET", fn(Request $request, mixed $params) => $params['bar']);

// Request to /foo/baz => returns "baz"
```

As a static method:
```php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    public static function foo(Request $request, mixed $params) : Response
    {
        return new Response($params['bar'], 200);
    }
}

$route = new Route("/foo/:bar", "GET", [Controller::class, 'foo']);

// Request to /foo/baz => returns "baz"
```

### Accessing Query Parameters Within Callbacks:

Access query parameters the same way you would on the standard Symfony `Request` object. Simply declare the `Request` object in the callback definition and access the parameters via the `$request->get()` method or similar.

```php
$route = new Route('/foo', "GET", fn(Request $request) => $request->get('bar'));

// Request to /foo?bar=baz => returns "baz"
```

---

# Changelog

## 1.0.1
- Better (more) documentation.
- Added support for accessing path parameters directly from callbacks.

## 1.0.0
- Full release!
