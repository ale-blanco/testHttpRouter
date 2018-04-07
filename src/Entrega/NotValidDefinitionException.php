<?php

namespace Entrega;

class NotValidDefinitionException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Not valid definition of routes');
    }
}
