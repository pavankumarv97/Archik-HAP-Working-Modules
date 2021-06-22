<?php

namespace Hatsun\CustomerViewProduct\Model;


use Hatsun\CustomerViewProduct\Api\Data\AllViewProductInterface;
use Hatsun\CustomerViewProduct\Model\ResourceModel\AllViewProduct\CollectionFactory;
use Hatsun\CustomerViewProduct\Api\AllViewProductRepositoryInterface;
use Hatsun\CustomerViewProduct\Api\Data;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Hatsun\CustomerViewProduct\Model\ResourceModel\AllViewProduct as ResourceAllViewProduct;
use Magento\Store\Model\StoreManagerInterface;
use Hatsun\CustomerViewProduct\Model\AllViewProductFactory;

class AllViewProductRepository implements AllViewProductRepositoryInterface
{
    protected $resource;

    private $storeManager;

    private $collectionFactory;

    private $allViewProductFactory;
    protected $request;
    protected $_logger;

    public function __construct(
        ResourceAllViewProduct $resource,
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        AllViewProductFactory $allViewProductFactory,
        \Magento\Framework\Webapi\Rest\Request $request,
        \Psr\Log\LoggerInterface $logger

    ) {
        $this->resource = $resource;
        $this->storeManager = $storeManager;
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
        $this->allViewProductFactory = $allViewProductFactory;
        $this->_logger = $logger;
    }

    public function save(\Hatsun\CustomerViewProduct\Api\Data\AllViewProductInterface $product)
    {
         

        $collection =  $this->collectionFactory->create()->addFieldToSelect('*')->addFieldToFilter("customer_id", $product->getCustomerId())->addFieldToFilter("product_id",$product->getProductId())->setPageSize(10);
        $collection->setOrder('created_at','DESC');
        if($collection->getData()){
            return $product;
        }else{
            if($product->getId() == null){
                $this->resource->save($product);
                return $product;
            }
        }
        
        // if(isset($productsIds)&&count($productsIds)>0){
        //    if(in_array($product->getId(), $productsIds)){
        //         $this->_logger->debug('recently viewed products',array($productsIds));
        //     } 
        // }
        
        

        // if(is_null($collection->getData())){
            
        // }   
         


    }



    /**
     * @param int $customer_id
     * @return \Hatsun\CustomerViewProduct\Api\Data\AllViewProductInterface[]
     */
public function getList($customer_id)   
{
      $collection = $this->collectionFactory->create()->addFieldToSelect('*')->addFieldToFilter("customer_id", $customer_id);
      return $collection->getData();
}





}





?>
