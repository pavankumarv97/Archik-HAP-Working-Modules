<?php
namespace Chopserve\Wallet\Model\ResourceModel\Wallet;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
    * Define resource model
    *
    * @return void
    */
    protected function _construct()
    {
         $this->_init('Chopserve\Wallet\Model\Wallet', 'Chopserve\Wallet\Model\ResourceModel\Wallet');
    }

}