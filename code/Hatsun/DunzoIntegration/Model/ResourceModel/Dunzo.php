<?php
namespace Hatsun\DunzoIntegration\Model\ResourceModel;
class Dunzo extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
     \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('dunzo_info', 'id'); //id : Primary key of your database table
    }

}