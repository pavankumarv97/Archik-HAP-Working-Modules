<?php
namespace Hatsun\CustomerViewProduct\Model\ResourceModel\AllViewProduct;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init('Hatsun\CustomerViewProduct\Model\AllViewProduct', 'Hatsun\CustomerViewProduct\Model\ResourceModel\AllViewProduct');
        // $this->_init('Hatsun\CustomerViewProduct\Model\AllViewProduct', 'Magento\Customer\Model\ResourceModel\Customer');
    }


//     protected function filterOrder($customer_id)
// {
//     $this->customer_entity = "main_table";
//     $this->recently_viewed_product_table = $this->getTable("recently_viewed_product");
//     $this->getSelect()
//         ->join(array('customer_id' =>$this->recently_viewed_product_table), $this->customer_entity . '.customer_id= customer_id.parent_id',
//         array('customer_id' => 'customer_id',
//             'customer_id' => $this->customer_entity.'.customer_id'
//         )
//     );
//     $this->getSelect()->where("customer_id=".$customer_id);
// }


}
?>
