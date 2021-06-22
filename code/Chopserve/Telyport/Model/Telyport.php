<?php
namespace Chopserve\Telyport\Model;
class Telyport extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init('Chopserve\Telyport\Model\ResourceModel\Telyport');
    }
}