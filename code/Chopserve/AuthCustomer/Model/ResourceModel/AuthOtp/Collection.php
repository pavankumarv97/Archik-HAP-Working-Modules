<?php

namespace Chopserve\AuthCustomer\Model\ResourceModel\AuthOtp;

use Chopserve\AuthCustomer\Model\AuthOtp;
use Chopserve\AuthCustomer\Model\ResourceModel\AuthOtp as AuthOtpResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(AuthOtp::class, AuthOtpResource::class);
    }
}
