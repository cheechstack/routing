<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Cheechstack\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

final class RouteTest extends TestCase {

    public function testStoresPathAndMethod() : void
    {
        $route = new Route('/test/path', "GET", fn() => 'ok');

        $this->assertEquals("GET", $route->method);
        $this->assertEquals("/test/path", $route->path);
    }

    public function testStoresParameters() : void
    {
        $route_w_o_params = new Route('/test/path', "GET", fn() => 'ok');
        $route_w_params = new Route('/test/:params/in/path', "GET", fn() => 'ok');

        $this->assertEmpty($route_w_o_params->getPathParameters(), "Route should not have parameters.");

        $this->assertCount(1, $route_w_params->getPathParameters(), "Wrong number of parameters");
        $this->assertArrayHasKey('params', $route_w_params->getPathParameters(), "Incorrectly parsed parameter label.");
    }

    public function testStoresCallback() : void
    {
        $route = new Route('/test/path', "GET", fn() => 'ok');

        $this->assertEquals('ok', call_user_func($route->getCallable()));
    }

    public function testMatchesStaticRoute() : void
    {
        $request = Request::create(
            "https://localhost:8080/test/path",
            Request::METHOD_GET
        );

        $route = new Route('/test/path', "GET", fn() => 'ok');

        $this->assertTrue($route->matches($request->getPathInfo()));
    }

    public function testMatchesDynamicRoute() : void
    {
        $request = Request::create(
            "https://localhost:8080/test/this_is_my_value",
            Request::METHOD_GET
        );

        $route = new Route('/test/:path', "GET", fn() => 'ok');
        $this->assertTrue($route->matches($request->getPathInfo()));
    }
}