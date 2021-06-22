<?php

namespace Chopserve\SocialLogin\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Apiauth extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('api_auth_credentials', 'id');
    }
}
