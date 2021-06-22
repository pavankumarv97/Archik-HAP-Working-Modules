<?php

namespace Chopserve\AuthCustomer\Model;

use Magento\Framework\Model\AbstractModel;

class AuthOtp extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Chopserve\AuthCustomer\Model\ResourceModel\AuthOtp::class);
    }
}
