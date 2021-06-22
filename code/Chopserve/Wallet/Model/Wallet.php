<?php
namespace Chopserve\Wallet\Model;
class Wallet extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init('Chopserve\Wallet\Model\ResourceModel\Wallet');
    }
}