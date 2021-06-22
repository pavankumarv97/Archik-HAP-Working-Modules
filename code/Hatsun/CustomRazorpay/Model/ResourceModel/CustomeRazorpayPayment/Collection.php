<?php
namespace Hatsun\CustomRazorpay\Model\ResourceModel\CustomeRazorpayPayment;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
    * Define resource model
    *
    * @return void
    */
    protected function _construct()
    {
         $this->_init('Hatsun\CustomRazorpay\Model\CustomeRazorpayPayment', 'Hatsun\CustomRazorpay\Model\ResourceModel\CustomeRazorpayPayment');
    }

}