<?php
namespace Hatsun\CustomRazorpay\Api\Data;



interface CustomeRazorpayPaymentInterface
{
    const ID = 'id';
    const CUSTOMER_ID = 'customerId' ;
    const QUOTE_ID = 'quoteId';
    const STORE_ID = 'storeId';
    const RZP_ORDER_ID  = 'rzp_order_id';
    const RZP_PAYMENT_ID = 'rzp_payment_id';
    const RZP_SIGNATURE = 'rzp_signature';


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
      public function getCustomerId();

      /**
      * 
      * @return string|null
      */
    public function getRzpOrderId();

    /**
      * 
      * @return string|null
      */
      public function getRzpPaymentId();

      /**
      * 
      * @return string|null
      */
      public function getRzpSignature();

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
     *
     * @return int
     */
    public function setId($id);

    /**
     *
     * @param string $rzp_order_id
     * @return $this
     */
    public function setRzpOrderId($rzp_order_id);


    /**
     *
     * @param string $rzp_payment_id
     * @return $this
     */
    public function setRzpPaymentId($rzp_payment_id);

    /**
     *
     * @param string $rzp_signature
     * @return $this
     */
    public function setRzpSignature($rzp_signature);

    
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

  

}
?>
