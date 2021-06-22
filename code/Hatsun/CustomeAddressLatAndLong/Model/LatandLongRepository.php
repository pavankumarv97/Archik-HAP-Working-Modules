<?php


namespace Hatsun\CustomeAddressLatAndLong\Model;

use Hatsun\CustomeAddressLatAndLong\Api\LatandLongRepositoryInterface;
use Magento\Framework\HTTP\ZendClientFactory;
use Hatsun\CustomeAddressLatAndLong\Model\ResourceModel\LatandLong\CollectionFactory;
use Hatsun\CustomeAddressLatAndLong\Model\ResourceModel\LatandLong as ResourceLatandLong;
use Hatsun\CustomeAddressLatAndLong\Model\LatandLongFactory;
use Magento\Checkout\Model\Session as CheckoutSession;




class LatandLongRepository implements LatandLongRepositoryInterface
{


    private $latandLongFactory;
    private $logger;
    private $collectionFactory;
    protected $resource;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    public function __construct(
        ResourceLatandLong $resource,
        \Psr\Log\LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        LatandLongFactory $latandLongFactory,
        CheckoutSession $checkoutSession
    )
    {
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->resource = $resource;
        $this->latandLongFactory = $latandLongFactory;
        $this->checkoutSession = $checkoutSession;
    }


        /**
         * Checkout quote id
         *
         * @return int
         */
        public function getQouteId()
        {
            return (int)$this->checkoutSession->getQuote()->getId();
        }
    
        public function saveObject($object){   
           $updateFactory = $this->latandLongFactory->create()->load($object['quoteId'],"quoteId");
            if($updateFactory->getData()){
                foreach($object as $key => $value){
                    $updateFactory->setData($key ,$value); 
                }
               $obj =  $this->resource->save($updateFactory);
               $result[0]['data'] = $updateFactory->getData();
               $result[0]['msg'] = "uploaded successfully";              
            }else{
                $factory =  $this->latandLongFactory->create();
                $factory->setLatitude($object['latitude']);
                $factory->setLongitude($object['longitude']);
                $factory->setCustomerId($object['customerId']);
                $factory->setQuoteId($object['quoteId']);
                $factory->setStoreId($object['storeId']);
                if(isset($object['shipping_option'])&&isset($object['is_checkout'])){
                    $factory->setShippingOption($object['shipping_option']);
                    $factory->setIsCheckout($object['is_checkout']);   
                }
    	                     
                $factory->save();
                $result[0]['data'] = $factory->getData();
                $result[0]['msg'] = "uploaded successfully";
            }
             return $result;
        }
    
      
    }

