<?php

namespace Chopserve\SocialLogin\Model\ResourceModel\Apiauth;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Chopserve\SocialLogin\Model\Apiauth;
use Chopserve\SocialLogin\Model\ResourceModel\Apiauth as ApiauthResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(Apiauth::class, ApiauthResource::class);
    }
}
