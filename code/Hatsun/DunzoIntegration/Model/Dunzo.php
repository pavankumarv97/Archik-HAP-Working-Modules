<?php
namespace Hatsun\DunzoIntegration\Model;

use Hatsun\DunzoIntegration\Api\Data\DunzoInterface;

class Dunzo extends \Magento\Framework\Model\AbstractModel implements DunzoInterface
{
    public function _construct()
    {
        $this->_init('Hatsun\DunzoIntegration\Model\ResourceModel\Dunzo');
    }


public function getId()
    {
        return parent::getData(self::ID);
    }

    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    public function getTrackingId()
    {
        return $this->getData(self::TRACKING_ID);
    }


    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    public function setOrderId($order_id)
    {
        return $this->setData(self::ORDER_ID, $order_id);
    }

    public function setTrackingId($tracking_id)
    {
        return $this->setData(self::TRACKING_ID, $tracking_id);
    }


    public function getDunzoTaskId(){
       return $this->getData(self::DUNZO_TASK_ID); 
    }


    public function getCustomerId(){
        return $this->getData(self::CUSTOMER_ID); 
    }

    public function getDunzoAmount(){
        return $this->getData(self::DUNZO_AMOUNT); 
    }

    public function getDunzoStatus(){
        return $this->getData(self::DUNZO_STATUS); 
    }
    
    public function getCreatedAt(){
         return $this->getData(self::CREATED_AT); 
    }
    public function getUpdatedAt(){
         return $this->getData(self::UPDATED_AT); 
    }

    public function setDunzoTaskId($dunzo_task_id){
        return $this->setData(self::DUNZO_TASK_ID, $dunzo_task_id);
    }
    public function setCustomerId($customer_id){
         return $this->setData(self::CUSTOMER_ID, $customer_id);
    }

    public function setDunzoAmount($dunzo_amount){
         return $this->setData(self::DUNZO_AMOUNT, $dunzo_amount);
    }

    public function setDunzoStatus($dunzo_status){
        return $this->setData(self::DUNZO_STATUS, $dunzo_status);
    }

    public function setCreatedAt($created_at){
        return $this->setData(self::CREATED_AT, $created_at);
    }
    public function setUpdatedAt($updated_at){
        return $this->setData(self::UPDATED_AT, $updated_at);
    }


}