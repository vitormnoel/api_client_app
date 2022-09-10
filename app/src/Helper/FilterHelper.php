<?php

namespace App\Helper;

use App\Attribute\BrandAttributes;
use App\Attribute\ModelAttributes;
use App\Attribute\VehicleAttributes;
use App\Attribute\VersionModelAttributes;
use App\Entity\Abstract\Vehicle;
use App\Entity\Store;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class FilterHelper
{
    static function setFavorites($values){
        if(!is_array($values)) return $values;
        $vehicles = [];
        foreach ($values as $key  => $vehicle){
            if(is_array($vehicle)){
                if($vehicle[0] instanceof Vehicle) {
                    $current = $vehicle[0];
                    $current->setFavorite($vehicle['favorite'] ?? false);
                    $vehicles[] = $current;
                }else{
                    return $values;
                }
            }else{
                $vehicles[] = $vehicle;
            }
        }
        shuffle($vehicles);
        return $vehicles;
    }

    static function addBrandWhere(QueryBuilder $queryBuilder,$brand){
        $queryBuilder
            ->andWhere('b = :brand or b.id_string = :brand')
            ->setParameter('brand', StringResources::turnIntoId($brand));
    }

    static function addModelWhere(QueryBuilder $queryBuilder,$model){
        $queryBuilder
            ->andWhere('m = :model or m.id_string = :model')
            ->setParameter('model', StringResources::turnIntoId($model));
    }

    static function addVersionWhere(QueryBuilder $queryBuilder,$version){
        $queryBuilder
            ->andWhere('version = :version or version.id_string = :version')
            ->setParameter('version', StringResources::turnIntoId($version));
    }

    static function addVehicleConditions(QueryBuilder $queryBuilder){
        $queryBuilder
            ->andWhere('v.active = 1 and v.complete = 1');
    }

    static function addStoreWhere(QueryBuilder $queryBuilder,$store)
    {
        $queryBuilder
            ->andWhere('v.store = :store')
            ->setParameter('store', $store);
    }

}