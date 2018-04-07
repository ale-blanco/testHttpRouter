<?php

namespace Entrega;

class HttpRouter
{
    public function getController(array $routes, string $path): string
    {
        foreach ($routes as $route) {
            if (!isset($route['path']) || !isset($route['controller'])) {
                throw new NotValidDefinitionException();
            }

            $regExp = $this->getRegularExpression($route);
            if (preg_match('&^' . $regExp . '$&', $path)) {
                return $route['controller'];
            }
        }

        throw new NotMatchRouteException();
    }

    private function getRegularExpression(array $route): string
    {
        $regExp = $route['path'];
        if (isset($route['requeriments'])) {
            foreach ($route['requeriments'] as $paramName => $requeriment) {
                $regExp = str_replace('{' . $paramName . '}', '(' . $requeriment . ')', $regExp);
            }
        }

        return preg_replace('&{[^}]+}&', '([^/]+)', $regExp);
    }
}
