<?php
namespace Hatsun\DunzoIntegration\Model\ResourceModel\Dunzo;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
    * Define resource model
    *
    * @return void
    */
    protected function _construct()
    {
         $this->_init('Hatsun\DunzoIntegration\Model\Dunzo', 'Hatsun\DunzoIntegration\Model\ResourceModel\Dunzo');
    }

}