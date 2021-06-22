<?php
namespace Hatsun\CustomerViewProduct\Model;

use Hatsun\CustomerViewProduct\Model\AllViewProduct\FileInfo;
use Hatsun\CustomerViewProduct\Api\Data\AllViewProductInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;


class AllViewProduct extends AbstractModel implements AllViewProductInterface
{


    protected function _construct()
    {
        $this->_init('Hatsun\CustomerViewProduct\Model\ResourceModel\AllViewProduct');
    }

    

    public function getId()
    {
        return parent::getData(self::ID);
    }

    public function getProductName()
    {
        return $this->getData(self::PRODUCT_NAME);
    }

    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }


    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    public function setProductName($product_name)
    {
        return $this->setData(self::PRODUCT_NAME, $product_name);
    }

    public function setProductId($product_id)
    {
        return $this->setData(self::PRODUCT_ID, $product_id);
    }

    public function setCustomerId($customer_id)
    {
        return $this->setData(self::CUSTOMER_ID, $customer_id);
    }


    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
    }

    public function setUpdatedAt($updated_at)
    {
        return $this->setData(self::UPDATED_AT, $updated_at);
    }




}
?>
