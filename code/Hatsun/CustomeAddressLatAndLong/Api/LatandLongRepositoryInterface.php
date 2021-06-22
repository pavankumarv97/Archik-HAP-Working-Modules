<?php

namespace Hatsun\CustomeAddressLatAndLong\Api;

/**
 * Interface LatandLongRepositoryInterface
 * @api
 */
interface LatandLongRepositoryInterface
{

    

    /**
     * @param mixed $object
     * 
     * @return mixed
     */
    public function saveObject($object);

    

    // /**
    //  * Create Order On Your Store
    //  * 
    //  * @param mixed $orderData
    //  * @return mixed
    //  * 
    // */
    // public function createOrder($orderData);


    // /**
    //  * 
    //  * @param mixed $object
    //  * @return mixed
    //  * 
    // */
    // public function routeAmount($object);


}