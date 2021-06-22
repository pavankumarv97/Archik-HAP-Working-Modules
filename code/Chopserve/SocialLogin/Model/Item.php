<?php

namespace Chopserve\SocialLogin\Model;

use Magento\Framework\Model\AbstractModel;

class Item extends AbstractModel
{
    protected $_eventPrefix = 'social_login_records';

    protected function _construct()
    {
        $this->_init(\Chopserve\SocialLogin\Model\ResourceModel\Item::class);
    }
}
