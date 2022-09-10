<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

class ExtractParamsRequestHelper
{
    static function getCarParameters(Request $request):array
    {
        $parameters = [];
        $queryString = $request->query->all();

        foreach (self::carParmeters() as $key => $parameter ){
            if(array_key_exists($parameter, $queryString)){
                $parameters[$parameter] = $queryString[$parameter];
                unset($queryString[$parameter]);
            }
        }

        foreach (self::commonParameters() as $key => $parameter ){
            if(array_key_exists($parameter, $queryString)){
                $parameters[$parameter] = $queryString[$parameter];
                unset($queryString[$parameter]);
            }
        }
        return $parameters;
    }

    static function getMotorcycleParameters(Request $request):array
    {
        $parameters = [];
        $queryString = $request->query->all();

        foreach (self::motorcycleParmeters() as $key => $parameter ){
            if(array_key_exists($parameter, $queryString)){
                $parameters[$parameter] = $queryString[$parameter];
                unset($queryString[$parameter]);
            }
        }

        foreach (self::commonParameters() as $key => $parameter ){
            if(array_key_exists($parameter, $queryString)){
                $parameters[$parameter] = $queryString[$parameter];
                unset($queryString[$parameter]);
            }
        }
        return $parameters;
    }

    static function getVehicleParameters(Request $request):array
    {
        $parameters = [];
        $queryString = $request->query->all();


        foreach (self::commonParameters() as $key => $parameter ){
            if(array_key_exists($parameter, $queryString)){
                $parameters[$parameter] = $queryString[$parameter];
                unset($queryString[$parameter]);
            }
        }
        return $parameters;
    }


    private static function commonParameters(): array
    {
        return array_merge(self::fipeParmeters(),self::vehicleParameters());
    }

    private static function vehicleParameters(): array
    {
        return [
            'identifier',
            'category',
            'year_manufacture',
            'color',
            'new',
            'fuel',
            'fuel_system',
            'items',
            'gear_shift',
            'gear_number',
            'price_min',
            'price_max',
            'year_min',
            'year_max',
            'mileage_traveled_min',
            'mileage_traveled_max',
            'last_digit_plate',
            'characteristics',
            'brake',
        ];
    }

    private static function motorcycleParmeters(): array
    {
        return [
            'starter',
            'cylinder',
            'motor_type',
        ];
    }

    private static function carParmeters(): array
    {
        return [
            'number_doors',
            'direction'
        ];
    }

    private static function fipeParmeters(): array
    {
        return [
            'brand',
            'model',
            'version'
        ];
    }
}