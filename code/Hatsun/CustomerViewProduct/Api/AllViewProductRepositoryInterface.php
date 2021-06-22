<?php
namespace Hatsun\CustomerViewProduct\Api;

interface AllViewProductRepositoryInterface
{
     /**
     * Create product
     *
     * @param \Hatsun\CustomerViewProduct\Api\Data\AllViewProductInterface $product
     * @param bool $saveOptions
     * @return \Hatsun\CustomerViewProduct\Api\Data\AllViewProductInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Hatsun\CustomerViewProduct\Api\Data\AllViewProductInterface $product);


    /**
     * @param int $customer_id
     * @return \Hatsun\CustomerViewProduct\Api\Data\AllViewProductInterface[]
     */
    public function getList($customer_id);


    // /**
    //  * @param string $customer_id
    //  * @return \Hatsun\CustomerViewProduct\Api\Data\AllViewProductInterface
    //  */
    // public function getProductById($customer_id);


}


?>
