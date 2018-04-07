<?php

namespace TestEntrega;

use Entrega\HttpRouter;
use Entrega\NotMatchRouteException;
use Entrega\NotValidDefinitionException;

class HttpRouterTest extends \PHPUnit_Framework_TestCase
{
    private $httpRouter;
    private $listRoutes;

    protected function setUp()
    {
        $this->httpRouter = new HttpRouter();
        $this->listRoutes = [
            [
                'path' => '/article/',
                'controller' => 'Article:list'
            ],
            [
                'path' => '/article/{id}/',
                'requeriments' => [
                    'id' => '\d+'
                ],
                'controller' => 'Article:article'
            ],
            [
                'path' => '/article/{slug}/',
                'controller' => 'Article:slug'
            ],
            [
                'path' => '/article/{slug1}/{slug2}',
                'requeriments' => [
                    'slug1' => '[0-9A-F]+'
                ],
                'controller' => 'Article:slug2'
            ],
            [
                'path' => '/comment/{slug1}/{slug2}',
                'controller' => 'Comment:slug'
            ],
            [
                'path' => '/user/{name}/{id}',
                'requeriments' => [
                    'name' => '\w+',
                    'id' => '\d+'
                ],
                'controller' => 'User:name'
            ],
            [
                'path' => '/',
                'controller' => 'Root:root'
            ]
        ];
    }

    protected function tearDown()
    {
        $this->httpRouter = null;
    }

    public function testBadRoutesDefinitionThrowAnException()
    {
        $this->expectException(NotValidDefinitionException::class);
        $this->httpRouter->getController([[]], '');
    }

    public function testWhenNoPathThrowAnException()
    {
        $this->expectException(NotMatchRouteException::class);
        $this->httpRouter->getController($this->listRoutes, '');
    }

    public function testWhenRouteNoMatchThrowAnException()
    {
        $this->expectException(NotMatchRouteException::class);
        $this->httpRouter->getController($this->listRoutes, '/no/existe/');
    }

    public function testWhenRouteIsRootMatch()
    {
        $controller = $this->httpRouter->getController($this->listRoutes, '/');
        $this->assertEquals($controller, 'Root:root');
    }

    public function testWhenPathContainsAPartMatch()
    {
        $controller = $this->httpRouter->getController($this->listRoutes, '/article/');
        $this->assertEquals($controller, 'Article:list');
    }

    public function testWhenNoFinalBarNoMatch()
    {
        $this->expectException(NotMatchRouteException::class);
        $this->httpRouter->getController($this->listRoutes, '/article');
    }

    public function testWhenPassVariableOkMatch()
    {
        $controller = $this->httpRouter->getController($this->listRoutes, '/article/45/');
        $this->assertEquals($controller, 'Article:article');
        $controller = $this->httpRouter->getController($this->listRoutes, '/article/algo-valido/');
        $this->assertEquals($controller, 'Article:slug');
        $controller = $this->httpRouter->getController($this->listRoutes, '/article/AF65/algo-valido');
        $this->assertEquals($controller, 'Article:slug2');
        $controller = $this->httpRouter->getController($this->listRoutes, '/comment/aldaskfj/algo-valido');
        $this->assertEquals($controller, 'Comment:slug');
        $controller = $this->httpRouter->getController($this->listRoutes, '/user/asdfdsaf/6679');
        $this->assertEquals($controller, 'User:name');
    }

    public function testWhenPathNotSatisfyRequeriment()
    {
        $controller = $this->httpRouter->getController($this->listRoutes, '/article/notNumber/');
        $this->assertEquals($controller, 'Article:slug');
        $this->expectException(NotMatchRouteException::class);
        $this->httpRouter->getController($this->listRoutes, '/article//');
    }

    public function testWhenPathNotSatisfyRequeriment2()
    {
        $this->expectException(NotMatchRouteException::class);
        $this->httpRouter->getController($this->listRoutes, '/article/AFrr5/algo-valido');
    }

    public function testWhenPathNotSatisfyRequeriment3()
    {
        $this->expectException(NotMatchRouteException::class);
        $this->httpRouter->getController($this->listRoutes, '/article/af5/algo-valido');
    }

    public function testWhenPathNotSatisfyRequeriment4()
    {
        $this->expectException(NotMatchRouteException::class);
        $this->httpRouter->getController($this->listRoutes, '/user/asdfds.-af/6679');
    }

    public function testWhenPathNotSatisfyRequeriment5()
    {
        $this->expectException(NotMatchRouteException::class);
        $this->httpRouter->getController($this->listRoutes, '/user/asdfdsaf/66s79');
    }
}
