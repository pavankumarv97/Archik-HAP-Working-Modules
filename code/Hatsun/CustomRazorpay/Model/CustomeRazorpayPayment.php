<?php
namespace Hatsun\CustomRazorpay\Model;

use Hatsun\CustomRazorpay\Api\Data\CustomeRazorpayPaymentInterface;

class CustomeRazorpayPayment extends \Magento\Framework\Model\AbstractModel implements CustomeRazorpayPaymentInterface
{
    public function _construct()
    {
        $this->_init('Hatsun\CustomRazorpay\Model\ResourceModel\CustomeRazorpayPayment');
    }


    public function getId()
    {
        return parent::getData(self::ID);
    }

    public function getRzpOrderId()
    {
        return $this->getData(self::RZP_ORDER_ID);
    }

    public function getRzpPaymentId()
    {
        return $this->getData(self::RZP_PAYMENT_ID);
    }

    public function getRzpSignature()
    {
        return $this->getData(self::RZP_SIGNATURE);
    }

    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
    }


    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    public function setRzpOrderId($rzp_order_id)
    {
        return $this->setData(self::RZP_ORDER_ID, $rzp_order_id);
    }

    public function setRzpPaymentId($rzp_payment_id)
    {
        return $this->setData(self::RZP_PAYMENT_ID, $rzp_payment_id);
    }

    public function setRzpSignature($rzp_signature)
    {
        return $this->setData(self::RZP_SIGNATURE, $rzp_signature);
    }

    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    public function setQuoteId($quoteId)
    {
        return $this->setData(self::QUOTE_ID, $quoteId);
    }

    


}