<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

class RoutePermission
{
    public static function isPublicRoute($_route):bool
    {
        return str_contains($_route, 'public_');
    }


    public static function liberateRoutes($_route):bool
    {
        return in_array($_route,self::getFreeRoutes());
    }

    private static function getFreeRoutes(): array
    {
       return [
           'route_1',
           'route_2',
           'route_3'
       ];
    }

    private static function getCurrentRoute($object){
        if(! $object instanceof Request) return true;
        return $object?->attributes?->get('_route');
    }

}