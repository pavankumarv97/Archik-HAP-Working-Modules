<?php

namespace Chopserve\SocialLogin\Model;

use Magento\Framework\Model\AbstractModel;

class Apiauth extends AbstractModel
{
    protected $_eventPrefix = 'api_auth_credentials';

    protected function _construct()
    {
        $this->_init(\Chopserve\SocialLogin\Model\ResourceModel\Apiauth::class);
    }
}
