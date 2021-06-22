<?php
namespace Hatsun\CustomerViewProduct\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

class AllViewProduct extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{



    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init('recently_viewed_product', 'id');
    }



}
?>
