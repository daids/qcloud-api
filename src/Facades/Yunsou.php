<?php

namespace Daids\QcloudApi\Facades;

use Illuminate\Support\Facades\Facade;

class Yunsou extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'qcloud.yunsou';
    }
}
