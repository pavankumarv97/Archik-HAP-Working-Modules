<?php

namespace Chopserve\SocialLogin\Api;

interface SocialLoginRepositoryInterface
{
    /**
     * POST for sendemail api
     * @param string $platform
     * @return array
     */
    public function getList($platform);


    /**
     * @param mixed $params
     * @return mixed
     */
    public function userinfo($params);

    /**
     * @param mixed $params
     * @return mixed
     */
    public function guestcheckout($params);

    /**
     * @param mixed $params
     * @return mixed
     */
    public function checkmail($params);

    /**
     * @param string $username
     * @param string $password
     * @return string
     */
    public function getCustomerToken($username, $password);

    
    /**
     * @param string $source_code
     * @return mixed
     */
    public function getStoresInfo($source_code);
    

    /**
     * @param string $source_code
     * @return mixed
     */
    public function getSourceItems($source_code);


    /**
     * @param string $source_code
     * @param string $category_id
     * @return mixed
     */    
    public function getStoreProducts($source_code,$category_id);

    /**
     * @param mixed $params
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function createGuestCustomer($params);


    /**
     * @param string $source_code
     * @param string $order_id
     * @return mixed
     */    
    public function setSource($source_code,$order_id);


    /**
    * @param string $customer_id
    * @param string $fcm_key
    * @return mixed
    */    
    public function savefcm($customer_id,$fcm_key);

    /**
    * @return mixed
    */  
    public function sendNotification();

    /**
    * @return mixed
    */  
    public function getTimeToCancel();


    /**
    * @param string $email
    * @param string $order_id
    * @param string $template_id
    * @return mixed
    */    
    public function sendMailToUsers($email,$order_id,$template_id);


    /**
    * @param string $phone_number
    * @param string $message
    * @return mixed
    */    
    public function sendsms($phone_number,$message);

}
