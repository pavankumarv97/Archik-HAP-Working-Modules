<?php

namespace Hatsun\CustomRazorpay\Api;

/**
 * Interface CustomeRazorpayPaymentInterface
 * @api
 */
interface CustomeRazorpayPaymentRepositoryInterface
{

    

    /**
     * @param mixed $object
     * 
     * @return mixed
     */
    public function saveObject($object);

    /**
     * @param mixed $parmas
     *
     * @return mixed
     */
    public function razorpayApis($params);

     /**
     * @param mixed $parmas
     *
     * @return mixed
     */
    public function refunds($params);

     /**
     * @param mixed $parmas
     *
     * @return mixed
     */
    public function routes($params);

    


}