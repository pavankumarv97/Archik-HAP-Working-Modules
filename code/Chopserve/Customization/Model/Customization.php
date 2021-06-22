<?php
namespace Chopserve\Customization\Model;
class Customization extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init('Chopserve\Customization\Model\ResourceModel\Customization');
    }
}