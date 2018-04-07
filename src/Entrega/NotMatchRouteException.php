<?php

namespace Entrega;

class NotMatchRouteException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Not match route');
    }
}
