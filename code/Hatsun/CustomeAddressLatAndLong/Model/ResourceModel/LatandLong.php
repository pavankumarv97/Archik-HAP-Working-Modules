<?php
namespace Hatsun\CustomeAddressLatAndLong\Model\ResourceModel;
class LatandLong extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
     \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('custome_latitude_longitude', 'id'); //id : Primary key of your database table
    }

}