<?php

namespace Hatsun\CustomeAddressLatAndLong\Api;

/**
 * Interface LatandLongRepositoryInterface
 * @api
 */
interface RouteRepositoryInterface
{

    

    /**
     * 
     * @param mixed $object
     * @return mixed
     * 
    */
    public function routeAmount($object);


    /**
     * 
     * @param mixed $object
     * @return mixed
     * 
    */
    public function reversalAmount($object);


    /**
     * 
     * @param mixed $object
     * @return mixed
     * 
    */
    public function refundAmount($object);


}