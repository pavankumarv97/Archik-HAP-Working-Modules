<?php
namespace Hatsun\CustomeAddressLatAndLong\Model\ResourceModel\LatandLong;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
    * Define resource model
    *
    * @return void
    */
    protected function _construct()
    {
         $this->_init('Hatsun\CustomeAddressLatAndLong\Model\LatandLong', 'Hatsun\CustomeAddressLatAndLong\Model\ResourceModel\LatandLong');
    }

}