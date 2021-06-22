<?php

namespace Hatsun\DunzoIntegration\Model\Total;

use Magento\Inventory\Model\ResourceModel\Source as SourceResourceModel;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Chopserve\SocialLogin\Model\SocialLoginRepository as SocialLogin;
use Hatsun\CustomeAddressLatAndLong\Model\ResourceModel\LatandLong\CollectionFactory;
use Hatsun\CustomeAddressLatAndLong\Model\LatandLongFactory;


class DeliveryFee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal

{
   /**
     * Collect grand total address amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    protected $quoteValidator = null; 
    protected $_logger;
    private $clientId = '5674abe4-14f7-4c5a-bce1-1bc896aa8f05';
    private $clientSecret = 'a29d4a17-a828-4d1f-8816-4d5e31521463';
    private $Authorization = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkIjp7InJvbGUiOjEwMCwidWlkIjoiZWM4OTI4ZmYtMGJmMS00NzczLTk2MDAtMWIxNDJiNDA5NTAwIn0sIm1lcmNoYW50X3R5cGUiOm51bGwsImNsaWVudF9pZCI6IjU2NzRhYmU0LTE0ZjctNGM1YS1iY2UxLTFiYzg5NmFhOGYwNSIsImF1ZCI6Imh0dHBzOi8vaWRlbnRpdHl0b29sa2l0Lmdvb2dsZWFwaXMuY29tL2dvb2dsZS5pZGVudGl0eS5pZGVudGl0eXRvb2xraXQudjEuSWRlbnRpdHlUb29sa2l0IiwibmFtZSI6InRlc3RfNjY3MTY4NTk0NyIsInV1aWQiOiJlYzg5MjhmZi0wYmYxLTQ3NzMtOTYwMC0xYjE0MmI0MDk1MDAiLCJyb2xlIjoxMDAsImR1bnpvX2tleSI6ImNmMDU2M2UzLTAzMjYtNDVkNC1iNTZlLTlhN2QzMzZmOTljMiIsImV4cCI6MTc3MzQ2MjU1OCwidiI6MCwiaWF0IjoxNjE3OTQyNTU4LCJzZWNyZXRfa2V5IjoiZDc3MTMwNTEtYWRiMi00NTNiLWE3ODktZjY2YzY3NjJkOWQxIn0.4gLpii3cTz9RWl2UzTEpg1oVXvQ3I6hfQ6YczL0VqBQ';

    /**
     * @var SourceResourceModel
     */
    private $sourceResourceModel;

     /**
     * @var SourceRepositoryInterface
     */
    private $sourceRepository;
    private $SocialLoginRepository;
    protected $customerSession;
    private $collectionFactory;
    private $latitude;
    private $longitude;
    private $latandLongFactory;
    protected $sourceCollectionFactory;

    public function __construct(
        
        \Magento\Quote\Model\QuoteValidator $quoteValidator,
        SourceResourceModel $sourceResourceModel, 
        SourceRepositoryInterface $sourceRepository,
        \Psr\Log\LoggerInterface $logger,
        SocialLogin $SocialLoginRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        CollectionFactory $collectionFactory,
        LatandLongFactory $latandLongFactory,
        \Magento\Inventory\Model\ResourceModel\Source\CollectionFactory $sourceCollectionFactory
        )
    {
        $this->quoteValidator = $quoteValidator;
        $this->_logger = $logger;
        $this->sourceRepository = $sourceRepository;
        $this->sourceResourceModel = $sourceResourceModel;
        $this->SocialLoginRepository = $SocialLoginRepository;
        $this->customerSession = $customerSession;
        $this->quoteFactory = $quoteFactory;
        $this->collectionFactory = $collectionFactory;
        $this->latandLongFactory = $latandLongFactory;
         $this->sourceCollectionFactory = $sourceCollectionFactory;



    }

  public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);


         // $this->_logger->debug('shipment address',array($quote->getShippingAddress()->getData()));

          // $this->_logger->debug('Billing address',array($quote->getBillingAddress()->getData()));

        

        // $deliveryFees = $this->getDeliveryAmount();
        // $balance = $deliveryFees;

        // $this->_logger->debug('shipment address',array($balance));
        

        // $total->setGrandTotal($total->getGrandTotal() + $balance);
        // $total->setBaseGrandTotal($total->getBaseGrandTotal() + $balance);
        return $this;
    } 

      public function getStoresInfo($source_code){
         $httpHeaders = new \Zend\Http\Headers();
        $httpHeaders->addHeaders([
          'Accept' => 'application/json',
          'Content-Type' => 'application/json',
          'Authorization'=>"Bearer e6ldnlh0xxgzo4ldlmxanf3o5b2s22sw"
        ]);


        $request = new \Zend\Http\Request();
        $request->setHeaders($httpHeaders);
        $request->setUri('http://13.234.199.227/hatsun/rest/V1/inventory/sources');
        $request->setMethod(\Zend\Http\Request::METHOD_GET);


        $params = new \Zend\Stdlib\Parameters([
            'source_code'=>$source_code
        ]);

        $request->setQuery($params);
        $client = new \Zend\Http\Client();
        $response = $client->send($request);
        $responseobject[] = json_decode($response->getBody(), true);
        // $this->_logger->debug('response',array($responseobject));
        // $deliveryCharge = $responseobject[0]['estimated_price'];
        return $responseobject;
    }
    
    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
      // $a = $this->SocialLoginRepository->getStoresInfo(560050);
       $shipingAddress = $quote->getShippingAddress()->getData();
       $quoteId = $shipingAddress['quote_id'];
        if(isset($quoteId)){
          $latAndLongDetails = $this->getLatAndLong($quoteId);
           $this->_logger->debug('lat and long details',array($latAndLongDetails));
           if(count($latAndLongDetails)>0){
             $latitude = $latAndLongDetails[0]['latitude'];
             $longitude = $latAndLongDetails[0]['longitude'];
             $storeCode = $latAndLongDetails[0]['storeId'];

             if(!is_null($latitude && $longitude)){
              $this->_logger->debug('inside function',array($latAndLongDetails));
                return [
                    'code' => 'Delivery Fees',
                    'title' => 'Delivery Fee',
                    'value' => $this->getDeliveryAmount($latitude , $longitude , $storeCode)
                ];
             }
           }else{
              return [
                    'code' => 'Delivery Fees',
                    'title' => 'Delivery Fee',
                    'value' => ''
                ];
               return $this;
           }
           
          
         
        }

      //  $this->_logger->debug('response for quote new',array($quoteId));
      // $quote2 = $this->quoteFactory->create()->getCollection()->addFieldToFilter('customer_id',2)->addFieldToFilter('entity_id',31);
      //  $this->_logger->debug('response for quote new2',array($quote2->getData()));
        

        
    }

    public function getDeliveryAmountForGuest(){        
        try{
          $storeInfo = $this->getStoresInfo(560050);
          if(count($storeInfo)>0){
            $lat = $storeInfo[0]['items'][0]['latitude'];
            $longitude = $storeInfo[0]['items'][0]['longitude'];
          }

          $httpHeaders = new \Zend\Http\Headers();
          $httpHeaders->addHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'client-id' => $this->clientId,
            'Authorization'=>$this->Authorization
          ]);


          $request = new \Zend\Http\Request();
          $request->setHeaders($httpHeaders);
          $request->setUri('https://apis-staging.dunzo.in/api/v1/quote');
          $request->setMethod(\Zend\Http\Request::METHOD_GET);



          $params = new \Zend\Stdlib\Parameters([
              'pickup_lat'=>$lat,
              'pickup_lng'=>$longitude,
              'drop_lat'=>12.1234,
              'drop_lng'=>77.5678,
              'category_id'=>"pickup_drop"
          ]);

          $request->setQuery($params);
          $client = new \Zend\Http\Client();
          $response = $client->send($request);
          $responseobject[] = json_decode($response->getBody(), true);
          $this->_logger->debug('response',array($responseobject));
          if(isset($responseobject[0]['estimated_price']) && !is_null($responseobject[0]['estimated_price'])){
            $deliveryCharge = $responseobject[0]['estimated_price'];
            return $deliveryCharge;
          }else{
            $result['message'] = $responseobject[0]['message'];            
            return $result;
          }
        }catch(Exception $e){
          $this->_logger->debug('response',$e);
        }
      }

    public function getLatAndLong($quoteId){

        $collection = $this->collectionFactory->create()->addFieldToSelect('*')->addFieldToFilter("quoteId", $quoteId);
       
        return $collection->getData();
    }



    public function getDeliveryAmount($custlatitude='' , $custlongitude=''){        
        try{
          $storeInfo = $this->getStoresInfo("560050-1");
          if(count($storeInfo)>0){
            $this->_logger->debug('response for logged in customer',array($storeInfo[0]['items'][0]));
            $lat = $storeInfo[0]['items'][0]['latitude'];
            $longitude = $storeInfo[0]['items'][0]['longitude'];
          }
          

          $httpHeaders = new \Zend\Http\Headers();
          $httpHeaders->addHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'client-id' => $this->clientId,
            'Authorization'=>$this->Authorization
          ]);


          $request = new \Zend\Http\Request();
          $request->setHeaders($httpHeaders);
          $request->setUri('https://apis-staging.dunzo.in/api/v1/quote');
          $request->setMethod(\Zend\Http\Request::METHOD_GET);

          $this->_logger->debug('customer lat',array($custlatitude));
          $this->_logger->debug('customer long',array($custlongitude));          

          $params = new \Zend\Stdlib\Parameters([
              'pickup_lat'=>$lat,
              'pickup_lng'=>$longitude,
              'drop_lat'=>$custlatitude,
              'drop_lng'=>$custlongitude,
              'category_id'=>"pickup_drop"
          ]);

          $request->setQuery($params);
          $client = new \Zend\Http\Client();
          $response = $client->send($request);
          $responseobject[] = json_decode($response->getBody(), true);

          $this->_logger->debug('response for logged in customer',array($responseobject));
          if(isset($responseobject[0]['estimated_price']) && !is_null($responseobject[0]['estimated_price'])){
            $deliveryCharge = $responseobject[0]['estimated_price'];
            return $deliveryCharge;
          }else{
            $result['message'] = $responseobject[0]['message'];            
            return $result;
          }
        }catch(Exception $e){
          $this->_logger->debug('response',$e);
        }
      }


   
}