<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Cheechstack\Routing\Route;
use Cheechstack\Routing\Router;
use Symfony\Component\HttpFoundation\Request;

final class RouterTest extends TestCase {
    public function testRegistersSingleRoute() : void
    {
        $router = new Router();
        $newRoute = new Route('/', "GET", fn() => 'ok');

        $router->add($newRoute);

        $this->assertCount(1, $router->routes());
    }

    public function testRegistersMultipleRoutes() : void
    {
        $router = new Router();
        $routes = [
            new Route('/', 'GET', fn() => 'ok'),
            new Route('/1', 'GET', fn() => 'ok'),
            new Route('/2', 'GET', fn() => 'ok'),
            new Route('/3', 'GET', fn() => 'ok'),
        ];

        $router->add($routes);

        $this->assertCount(4, $router->routes());
    }
}