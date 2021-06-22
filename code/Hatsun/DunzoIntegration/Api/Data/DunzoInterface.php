<?php
namespace Hatsun\DunzoIntegration\Api\Data;


interface DunzoInterface
{
    const ID = 'id';
    const ORDER_ID  = 'order_id';
    const TRACKING_ID = 'tracking_id';
    const DUNZO_TASK_ID = 'dunzo_task_id';
    const CUSTOMER_ID = "customer_id";
    const DUNZO_AMOUNT = "dunzo_amount";
    const DUNZO_STATUS = "dunzo_status";
    const CREATED_AT = "created_at";
    const UPDATED_AT = 'updated_at';

    /**
     * 
     *
     * @return int|null
     */
    public function getId();

     /**
      * 
      * @return string|null
      */
    public function getOrderId();

    /**
      * 
      * @return string|null
      */
    public function getTrackingId();

    /**
      * 
      * @return string|null
      */
    public function getDunzoTaskId();

    /**
      * 
      * @return string|null
      */
    public function getCustomerId();

    /**
      * 
      * @return string|null
      */
    public function getDunzoAmount();

    /**
      * 
      * @return string|null
      */
    public function getDunzoStatus();

    /**
      * 
      * @return string|null
      */
    public function getCreatedAt();

    /**
      * 
      * @return string|null
      */
    public function getUpdatedAt();
  

    /**
     * 
     *
     * @return string
     */
    public function setId($id);

    /**
     *
     * @param string $order_id
     * @return $this
     */
    public function setOrderId($order_id);


     /**
     *
     * @param string $tracking_id
     * @return $this
     */
    public function setTrackingId($tracking_id);

    /**
     *
     * @param string $dunzo_task_id
     * @return $this
     */
    public function setDunzoTaskId($dunzo_task_id);

    /**
     *
     * @param string $customer_id
     * @return $this
     */
    public function setCustomerId($customer_id);

    /**
     *
     * @param string $dunzo_amount
     * @return $this
     */
    public function setDunzoAmount($dunzo_amount);

    /**
     *
     * @param string $dunzo_status
     * @return $this
     */
    public function setDunzoStatus($dunzo_status);

    /**
     *
     * @param string $created_at
     * @return $this
     */
    public function setCreatedAt($created_at);

    /**
     *
     * @param string $updated_at
     * @return $this
     */
    public function setUpdatedAt($updated_at);

}
?>
