<?php
namespace Hatsun\CustomeAddressLatAndLong\Model;

use Hatsun\CustomeAddressLatAndLong\Api\Data\LatandLongInterface;

class LatandLong extends \Magento\Framework\Model\AbstractModel implements LatandLongInterface
{
    public function _construct()
    {
        $this->_init('Hatsun\CustomeAddressLatAndLong\Model\ResourceModel\LatandLong');
    }


public function getId()
    {
        return parent::getData(self::ID);
    }

    public function getLatitude()
    {
        return $this->getData(self::LATITUDE);
    }

    public function getLongitude()
    {
        return $this->getData(self::LONGITUDE);
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

    public function getShippingOption()
    {
        return $this->getData(self::SHIPPING_OPTION);
    }
     public function getIsCheckout()
    {
        return $this->getData(self::IS_CHECKOUT);
    }



    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    public function setLatitude($latitude)
    {
        return $this->setData(self::LATITUDE, $latitude);
    }

    public function setLongitude($longitude)
    {
        return $this->setData(self::LONGITUDE, $longitude);
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

    public function setShippingOption($shipping_option)
    {
        return $this->setData(self::SHIPPING_OPTION, $shipping_option);
    }
    
     public function setIsCheckout($is_checkout)
    {
        return $this->setData(self::IS_CHECKOUT, $is_checkout);
    }

}