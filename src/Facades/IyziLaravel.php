<?php

namespace Rodosgrup\IyziLaravel\Facedes;

use Illuminate\Support\Facades\Facade;

class IyziLaravel extends Facede
{
    protected static function getFacadeAccessor()
    {
        return 'iyzi-laravel';
    }
}
