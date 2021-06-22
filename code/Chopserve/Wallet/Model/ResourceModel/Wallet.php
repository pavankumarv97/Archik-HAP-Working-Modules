<?php
namespace Chopserve\Wallet\Model\ResourceModel;
class Wallet extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
     \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('razerpay_log', 'id'); //id : Primary key of your database table
    }

}