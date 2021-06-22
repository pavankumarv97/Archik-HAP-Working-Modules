<?php
namespace Hatsun\CustomeAddressLatAndLong\Api\Data;



interface LatandLongInterface
{
    const ID = 'id';
    const LATITUDE  = 'latitude';
    const LONGITUDE = 'longitude';
    const  CUSTOMER_ID = 'customerId' ;
    const QUOTE_ID = 'quoteId';
    const STORE_ID = 'storeId';
    const SHIPPING_OPTION  = 'shipping_option';
    const IS_CHECKOUT = 'is_checkout';


    /**
     * 
     *
     * @return int|null
     */
    public function getId();

     /**
      * 
      * @return int|null
      */
    public function getLatitude();

    /**
      * 
      * @return int|null
      */
      public function getLongitude();


      /**
      * 
      * @return int|null
      */
      public function getCustomerId();

      /**
      * 
      * @return int|null
      */
      public function getStoreId();

      /**
      * 
      * @return int|null
      */
      public function getQuoteId();


    /**
      * 
      * @return int|null
      */
      public function getShippingOption();
    
    /**
      * 
      * @return int|null
      */
      public function getIsCheckout();


    /**
     * 
     *
     * @return int
     */
    public function setId($id);

    /**
     *
     * @param string $latitude
     * @return $this
     */
    public function setLatitude($latitude);


    /**
     *
     * @param string $longitude
     * @return $this
     */
    public function setLongitude($longitude);


     /**
     *
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     *
     * @param string $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    

    /**
     *
     * @param string $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId);

    /**
     *
     * @param string $shipping_option
     * @return $this
     */
    public function setShippingOption($shipping_option);

      /**
       *
       * @param string $is_checkout
       * @return $this
       */
      public function setIsCheckout($is_checkout);
  

}
?>
