<?php
namespace Hatsun\CustomerViewProduct\Api\Data;

interface AllViewProductInterface
{
    const ID = 'id';
    const PRODUCT_NAME  = 'product_name';
    const PRODUCT_ID = 'product_id';
    const CUSTOMER_ID = 'customer_id';
    const CREATED_AT = 'created_at';
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
    public function getProductId();

    /**
      * 
      * @return string|null
      */
      public function getCustomerId();

    // /**
    //  * 
    //  * @return string|null
    //  */
    // public function getProductName();

   
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
     * @param string $product_id
     * @return $this
     */
    public function setProductId($product_id);


     /**
     *
     * @param string $customer_id
     * @return $this
     */
    public function setCustomerId($product_id);

    // /**
    //  *
    //  * @param string $product_name
    //  * @return $this
    //  */
    // public function setProductName($product_name);


    /**
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($created_at);

    /**
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updated_at);


}
?>
