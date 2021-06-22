<?php
namespace Chopserve\Telyport\Model\ResourceModel\Telyport;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
    * Define resource model
    *
    * @return void
    */
    protected function _construct()
    {
         $this->_init('Chopserve\Telyport\Model\Telyport', 'Chopserve\Telyport\Model\ResourceModel\Telyport');
    }

}