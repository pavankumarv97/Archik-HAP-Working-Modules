<?php
namespace Chopserve\Customization\Model\ResourceModel\Customization;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
    * Define resource model
    *
    * @return void
    */
    protected function _construct()
    {
         $this->_init('Chopserve\Customization\Model\Customization', 'Chopserve\Customization\Model\ResourceModel\Customization');
    }

}