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
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    protected $quoteValidator = null; 
    protected $_logger;

  
    // development
    // private $clientId = 'c2936f85-bc47-4504-a7eb-3c5105a2c423';
    // private $clientSecret = '8749fcae-8ee8-4fc3-a401-a60acef5778b';
    // private $Authorization = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkIjp7InJvbGUiOjEwMCwidWlkIjoiN2EyNTFiOTAtMzY2Ni00YTE0LWE1NzktNWIzMTY0MzUwMTAxIn0sIm1lcmNoYW50X3R5cGUiOm51bGwsImNsaWVudF9pZCI6ImJhNGI3ZTkwLTE5NjktNGZlNS1iODRjLWFmZDBiOTliYTZhYiIsImF1ZCI6Imh0dHBzOi8vaWRlbnRpdHl0b29sa2l0Lmdvb2dsZWFwaXMuY29tL2dvb2dsZS5pZGVudGl0eS5pZGVudGl0eXRvb2xraXQudjEuSWRlbnRpdHlUb29sa2l0IiwibmFtZSI6IkhhdHN1biBBZ3JvIFByb2R1Y3QgTHRkIiwidXVpZCI6IjdhMjUxYjkwLTM2NjYtNGExNC1hNTc5LTViMzE2NDM1MDEwMSIsInJvbGUiOjEwMCwiZHVuem9fa2V5IjoiYzJhMTFkN2UtZjdlNi00MzIyLWExZjQtYWJiZjUxYzM5YTIyIiwiZXhwIjoxNzc5NDI3NDE3LCJ2IjowLCJpYXQiOjE2MjM5MDc0MTcsInNlY3JldF9rZXkiOiI0MDVjYWI4MC1lZDk5LTQ0N2ItYTMwMy01MDJlNDQwODJkYTgifQ.y5YVaMY4wFxUDj1eBs1F3N1KP7MKPUw53e448RuJFaM';
    // private $dunzoUrl = 'https://apis-staging.dunzo.in/api/v1/';
    // private $webUrl = "http://13.234.199.227/hatsun/rest/V1/";
    // private $webToken = 'e6ldnlh0xxgzo4ldlmxanf3o5b2s22sw';


    // production
    private $clientId = "ba4b7e90-1969-4fe5-b84c-afd0b99ba6ab";
    private $clientSecret = "35435ece-855e-4a32-a858-53834414bcee";
    private $Authorization = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkIjp7InJvbGUiOjEwMCwidWlkIjoiN2EyNTFiOTAtMzY2Ni00YTE0LWE1NzktNWIzMTY0MzUwMTAxIn0sIm1lcmNoYW50X3R5cGUiOm51bGwsImNsaWVudF9pZCI6ImJhNGI3ZTkwLTE5NjktNGZlNS1iODRjLWFmZDBiOTliYTZhYiIsImF1ZCI6Imh0dHBzOi8vaWRlbnRpdHl0b29sa2l0Lmdvb2dsZWFwaXMuY29tL2dvb2dsZS5pZGVudGl0eS5pZGVudGl0eXRvb2xraXQudjEuSWRlbnRpdHlUb29sa2l0IiwibmFtZSI6IkhhdHN1biBBZ3JvIFByb2R1Y3QgTHRkIiwidXVpZCI6IjdhMjUxYjkwLTM2NjYtNGExNC1hNTc5LTViMzE2NDM1MDEwMSIsInJvbGUiOjEwMCwiZHVuem9fa2V5IjoiYzJhMTFkN2UtZjdlNi00MzIyLWExZjQtYWJiZjUxYzM5YTIyIiwiZXhwIjoxNzc5NDc5MDI3LCJ2IjowLCJpYXQiOjE2MjM5NTkwMjcsInNlY3JldF9rZXkiOiI0MDVjYWI4MC1lZDk5LTQ0N2ItYTMwMy01MDJlNDQwODJkYTgifQ.K3_Uc9G8jTj3mnE3bDy1JvC4ZRBRfKfUwk-PVMsZkyM';

    private $dunzoUrl = 'https://api.dunzo.in/api/v1/';
    private $webUrl = 'http://13.234.199.227/hatsun/rest/V1/';
    private $webToken = 'nmii5me8o62cje2ku7q0q1ojb6ixh04u';

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

        /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
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
        $this->quoteRepository = $quoteRepository;

    }

  public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

       
  // $this->_logger->debug('custom111 dsds',array($items->getData()));
    $address = $shippingAssignment->getShipping()->getAddress();
    $items = $this->_getAddressItems($address);
     // $this->_logger->debug('custom111',array($items->getData()));
    if (!count($items)) {
        return $this;
    }
     $shipingAddress = $quote->getShippingAddress()->getData();
        $shipping_method = $shipingAddress['shipping_method'];
        $this->_logger->debug('shpmthd',array($shipingAddress));
        if($shipping_method){
          $this->_logger->debug('shpmthd',array($shipping_method));
        }
        $quoteId = $shipingAddress['quote_id'];
        if(isset($quoteId)){
          $latAndLongDetails = $this->getLatAndLong($quoteId);
          $this->_logger->debug('lat and long details in DeliveryFee.php',array($latAndLongDetails));
          if(count($latAndLongDetails)>0){
            $latitude = $latAndLongDetails[0]['latitude'];
            $longitude = $latAndLongDetails[0]['longitude'];
            $storeCode = $latAndLongDetails[0]['storeId'];
            if(!is_null($latitude && $longitude)){              
              $shipping_price = $this->getDeliveryAmount($latitude , $longitude , $storeCode);
              if($shipping_price>0){
                   $total->setTotalAmount($this->getCode(), $shipping_price);
                   $total->setBaseTotalAmount($this->getCode(), $shipping_price);
               }else{
                return $this;
               }
             
            }
          }
        }
       
        return $this;
    } 

     /**
    * @param \Magento\Quote\Model\Quote\Address\Total $total
    */
  protected function clearValues(Address\Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }
    
    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total){

      $shipingAddress = $quote->getShippingAddress()->getData();
      $shipping_method = $shipingAddress['shipping_method'];
      $this->_logger->debug('shpmthd',array($shipingAddress));
      if($shipping_method){
        $this->_logger->debug('shpmthd',array($shipping_method));
      }
      $quoteId = $shipingAddress['quote_id'];
      if(isset($quoteId)){
        $latAndLongDetails = $this->getLatAndLong($quoteId);
        try{
          if(count($latAndLongDetails)>0){
            $latitude = $latAndLongDetails[0]['latitude'];
            $longitude = $latAndLongDetails[0]['longitude'];
            $storeCode = $latAndLongDetails[0]['storeId'];
            if(!is_null($latitude && $longitude)){
              if(isset($latAndLongDetails[0]['is_checkout'])){
                if($latAndLongDetails[0]['is_checkout']=="yes"){
                   $shipping_price = $this->getDeliveryAmount($latitude , $longitude , $storeCode);
                    // $total->addTotalAmount($this->getCode(),  $shipping_price );
                    // $total->addBaseTotalAmount($this->getCode(),  $shipping_price );
                    // $quote->setDiscount($shipping_price );
                    return [
                        'code' => "Dunzo Delivery Fee",
                        'title' => 'Dunzo Delivery Fee',
                        'value' => $shipping_price
                        // 'value' => 30
                    ];
                }
              }             
            }
          }else{
            return [
              'code' => $this->getCode(),
              'title' => 'Dunzo Delivery Fee 7',
              'value' => ''
            ];
          }
        
        }catch(Exception $e){
          return [
              'code' => $this->getCode(),
              'title' => 'Dunzo Delivery Fee 8',
              'value' => ''
            ];
        }
      }
        
      
      // $shipingAddress = $quote->getShippingAddress()->getData();
      // $shipping_method = $shipingAddress['shipping_method'];
      // $this->_logger->debug('shpmthd',array($shipingAddress));
      // if($shipping_method){
      //   $this->_logger->debug('shpmthd',array($shipping_method));
      // }
      // $quoteId = $shipingAddress['quote_id'];
      // if(isset($quoteId)){
      //   $latAndLongDetails = $this->getLatAndLong($quoteId);
      //   try{
      //     if(count($latAndLongDetails)>0){
      //       $latitude = $latAndLongDetails[0]['latitude'];
      //       $longitude = $latAndLongDetails[0]['longitude'];
      //       $storeCode = $latAndLongDetails[0]['storeId'];
      //       if(!is_null($latitude && $longitude)){
      //         if(isset($latAndLongDetails[0]['is_checkout'])){
      //           if($latAndLongDetails[0]['is_checkout']=="yes"){
      //              $shipping_price = $this->getDeliveryAmount($latitude , $longitude , $storeCode);
      //               // $total->addTotalAmount($this->getCode(),  $shipping_price );
      //               // $total->addBaseTotalAmount($this->getCode(),  $shipping_price );
      //               // $quote->setDiscount($shipping_price );
      //               return [
      //                   'code' => "Dunzo Delivery Fee",
      //                   'title' => 'Dunzo Delivery Fee',
      //                   'value' => $shipping_price
      //                   // 'value' => 30
      //               ];
      //           }
      //         }             
      //       }
      //     }else{
      //       return [
      //         'code' => $this->getCode(),
      //         'title' => 'Dunzo Delivery Fee 7',
      //         'value' => ''
      //       ];
      //     }
        
      //   }catch(Exception $e){
      //     return [
      //         'code' => $this->getCode(),
      //         'title' => 'Dunzo Delivery Fee 8',
      //         'value' => ''
      //       ];
      //   }
      // }
        
    }

     /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Dunzo fee');
    }

    public function getStoresInfo($source_code){
         $httpHeaders = new \Zend\Http\Headers();
        $httpHeaders->addHeaders([
          'Accept' => 'application/json',
          'Content-Type' => 'application/json',
          'Authorization'=>"Bearer ".$webToken
        ]);
        $request = new \Zend\Http\Request();
        $request->setHeaders($httpHeaders);
        $request->setUri($this->webUrl.'inventory/sources');
        $request->setMethod(\Zend\Http\Request::METHOD_GET);
        $params = new \Zend\Stdlib\Parameters([
            'source_code'=>$source_code
        ]);
        $request->setQuery($params);
        $client = new \Zend\Http\Client();
        $response = $client->send($request);
        $responseobject[] = json_decode($response->getBody(), true);
        return $responseobject;
    }

    public function getLatAndLong($quoteId){
        $collection = $this->collectionFactory->create()->addFieldToSelect('*')->addFieldToFilter("quoteId", $quoteId);       
        return $collection->getData();
    }

    public function getDeliveryAmount($custlatitude='' , $custlongitude='' , $storeCode){        
      try{
        $collection = $this->sourceCollectionFactory->create();
        $collection->addFieldToFilter('source_code',$storeCode);
        $sourceCollection = $collection->getData();
        if(count($sourceCollection)>0){
          $this->_logger->debug('response from sourceCollection ',array($sourceCollection));
          $lat = (float)$sourceCollection[0]['latitude'];
          $longitude= (float)$sourceCollection[0]['longitude'];
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
        $request->setUri($this->dunzoUrl.'quote');
        $request->setMethod(\Zend\Http\Request::METHOD_GET);
        $params = new \Zend\Stdlib\Parameters([
          'pickup_lat'=>$lat,
          'pickup_lng'=>$longitude,
          'drop_lat'=>$custlatitude,
          'drop_lng'=>$custlongitude,
          'category_id'=>"pickup_drop"
        ]);
        $this->_logger->debug('dunzo request params',array(json_encode($params)));
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