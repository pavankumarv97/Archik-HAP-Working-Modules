<?php
namespace Hatsun\DunzoIntegration\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\InventoryShipping\Model\ResourceModel\ShipmentSource\GetSourceCodeByShipmentId;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Chopserve\SourceMapping\Model\ResourceModel\Mapping\CollectionFactory as MappingCollectionFactory;
use Hatsun\CustomeAddressLatAndLong\Model\ResourceModel\LatandLong\CollectionFactory;
use Hatsun\CustomRazorpay\Model\ResourceModel\CustomeRazorpayPayment\CollectionFactory as RazorpayCollectionFactory;
use Chopserve\SocialLogin\Model\SocialLoginRepository as SocialLoginRepository;
class SalesOrderAfterSave implements ObserverInterface
{
	protected $_logger;
	protected $_httpClientFactory;
	protected $_dunzoFactory;
	protected $orderRepository;
	protected $_customerRepository;
	protected $sourceRepository;
	protected $_sourceMapping;
	protected $mappingCollectionFactory;
	protected $sourceCollectionFactory;
	protected $razorpayCollectionFactory;
	protected $socialLoginRepository;

    public function __construct(
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
		\Hatsun\DunzoIntegration\Model\DunzoFactory $dunzoFactory,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		GetSourceCodeByShipmentId $sourceCodeByShipmentId,
		SourceRepositoryInterface $sourceRepository,
		\Chopserve\SourceMapping\Model\MappingRepository $sourceMapping,
		MappingCollectionFactory $mappingCollectionFactory,
		CollectionFactory $collectionFactory,
		\Magento\Inventory\Model\ResourceModel\Source\CollectionFactory $sourceCollectionFactory,
		 RazorpayCollectionFactory $razorpayCollectionFactory,
		 SocialLoginRepository $socialLoginRepository

    ) {
        $this->_logger = $logger;
		$this->_httpClientFactory   = $httpClientFactory;
		$this->_dunzoFactory = $dunzoFactory;
		$this->orderRepository = $orderRepository;
		$this->_customerRepository = $customerRepository;
		$this->sourceCodeByShipmentId = $sourceCodeByShipmentId;
		$this->sourceRepository = $sourceRepository;
		$this->_sourceMapping = $sourceMapping;
		$this->mappingCollectionFactory = $mappingCollectionFactory;
		$this->collectionFactory = $collectionFactory;
		$this->sourceCollectionFactory = $sourceCollectionFactory;
		$this->razorpayCollectionFactory = $razorpayCollectionFactory;
		$this->socialLoginRepository = $socialLoginRepository;
    }
	
    // development
	// private $clientId = 'c2936f85-bc47-4504-a7eb-3c5105a2c423';
 //    private $clientSecret = '8749fcae-8ee8-4fc3-a401-a60acef5778b';
 //    private $Authorization = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkIjp7InJvbGUiOjEwMCwidWlkIjoiZGNmNmYzMTAtMjliNi00ODM3LTg3MTEtNGM0ODkyODI4MmJkIn0sIm1lcmNoYW50X3R5cGUiOm51bGwsImNsaWVudF9pZCI6ImMyOTM2Zjg1LWJjNDctNDUwNC1hN2ViLTNjNTEwNWEyYzQyMyIsImF1ZCI6Imh0dHBzOi8vaWRlbnRpdHl0b29sa2l0Lmdvb2dsZWFwaXMuY29tL2dvb2dsZS5pZGVudGl0eS5pZGVudGl0eXRvb2xraXQudjEuSWRlbnRpdHlUb29sa2l0IiwibmFtZSI6InRlc3RfMjMwMDIzOTQyNiIsInV1aWQiOiJkY2Y2ZjMxMC0yOWI2LTQ4MzctODcxMS00YzQ4OTI4MjgyYmQiLCJyb2xlIjoxMDAsImR1bnpvX2tleSI6IjQyNGQwZmQ3LTc4MjgtNGMwYy1iMDM4LWQxYjdlYzg0M2EyNyIsImV4cCI6MTc3ODMwOTc3NywidiI6MCwiaWF0IjoxNjIyNzg5Nzc3LCJzZWNyZXRfa2V5IjoiZDc3MTMwNTEtYWRiMi00NTNiLWE3ODktZjY2YzY3NjJkOWQxIn0.OULDgUoHY4-T5XbdaHlJUToVBcEHkguKM09gG5vy14A';

 //    // development
	// private  $username = "rzp_test_RKxG1LoLkdTy0s";
 //    private $password = "53siy3KRQD3oRo5ni3uwouQJ";


    // production
    private $clientId = "ba4b7e90-1969-4fe5-b84c-afd0b99ba6ab";
    private $clientSecret = "35435ece-855e-4a32-a858-53834414bcee";
    private $Authorization = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkIjp7InJvbGUiOjEwMCwidWlkIjoiN2EyNTFiOTAtMzY2Ni00YTE0LWE1NzktNWIzMTY0MzUwMTAxIn0sIm1lcmNoYW50X3R5cGUiOm51bGwsImNsaWVudF9pZCI6ImJhNGI3ZTkwLTE5NjktNGZlNS1iODRjLWFmZDBiOTliYTZhYiIsImF1ZCI6Imh0dHBzOi8vaWRlbnRpdHl0b29sa2l0Lmdvb2dsZWFwaXMuY29tL2dvb2dsZS5pZGVudGl0eS5pZGVudGl0eXRvb2xraXQudjEuSWRlbnRpdHlUb29sa2l0IiwibmFtZSI6IkhhdHN1biBBZ3JvIFByb2R1Y3QgTHRkIiwidXVpZCI6IjdhMjUxYjkwLTM2NjYtNGExNC1hNTc5LTViMzE2NDM1MDEwMSIsInJvbGUiOjEwMCwiZHVuem9fa2V5IjoiYzJhMTFkN2UtZjdlNi00MzIyLWExZjQtYWJiZjUxYzM5YTIyIiwiZXhwIjoxNzc5NDc5MDI3LCJ2IjowLCJpYXQiOjE2MjM5NTkwMjcsInNlY3JldF9rZXkiOiI0MDVjYWI4MC1lZDk5LTQ0N2ItYTMwMy01MDJlNDQwODJkYTgifQ.K3_Uc9G8jTj3mnE3bDy1JvC4ZRBRfKfUwk-PVMsZkyM';
    // production
    private  $username = "rzp_live_Vz8uIp1N7EfRBF";
    private $password = "gloEUqmhsls7QuSpshv2kf1W";

	public function execute(\Magento\Framework\Event\Observer $observer){
		try{
			$orderData = $observer->getEvent()->getOrder();
		//	$this->_logger->debug('shipping method',array($orderData->getShippingDescription()));
			$this->_logger->debug('order data in sales order after save',array($orderData->getData()));		
			$orderId = $observer->getEvent()->getOrder()->getId();
			
			$dunzo_data = $this->_dunzoFactory->create();
	        $order_id_data = $dunzo_data->getCollection()->addFieldToSelect('*')->addFieldToFilter("order_id", $orderId);
	        $dunzo_info_data = $order_id_data->getData();
	        if(isset($dunzo_info_data[0]['order_id'])&&!is_null($dunzo_info_data[0]['order_id'])){
	            return $this;
	        }else{

	        
			// $this->_logger->debug('dunzo db order status',array($dunzo_info_data->getOrderId()));
			// $this->_logger->debug('dunzo db order status',array($dunzo_info_data['tracking_id']));
			// $this->_logger->debug('dunzo db order status',array($dunzo_info_data['order_id']));
			// $this->_logger->debug('dunzo db order status',array($dunzo_info_data[0]['tracking_id']));
			// $this->_logger->debug('dunzo db status',array($dunzo_data_e->getData()));
				
				if ($orderData instanceof \Magento\Framework\Model\AbstractModel) {			
				$this->_logger->debug('order status',array($orderData->getData()));
                $orderDetails = $orderData->getData();
	        	$base_grand_total = $orderDetails['base_subtotal'];
	        	$base_grand_total = $base_grand_total * 100;
	        	$increment_id = $orderDetails['increment_id'];
		        $this->_logger->debug('base_grand_total Details',array($base_grand_total));
				$billingAddress = $orderData->getBillingAddress()->getData();
				$shippingAddress = $orderData->getShippingAddress()->getData();

				// $shipping_method = $shipingAddress['shipping_method'];
				// if($shipping_method){
				//   $this->_logger->debug('shpmthd',array($shipping_method));
				// }


				$quoteId =  $orderData->getQuoteId();	
				$latAndLongDetails = $this->getLatAndLong($quoteId);
				$customerLat = $latAndLongDetails[0]['latitude'];
				$customerLong = $latAndLongDetails[0]['longitude'];
				$storeCode = $latAndLongDetails[0]['storeId'];
				$shipping_option = $latAndLongDetails[0]['shipping_option'];
				$this->_logger->debug('shipping option Details',array($shipping_option));
				$collection = $this->sourceCollectionFactory->create();
				$collection->addFieldToFilter('source_code',$storeCode);
				$sourceCollection = $collection->getData();

				$this->_logger->debug('source123',array($sourceCollection));
        		if(isset($sourceCollection[0]['email'])){
        			$this->socialLoginRepository->sendMailToUsers($sourceCollection[0]['email'],$orderId,'storeownermail');

        		}  
        		$smsMessage = $this->socialLoginRepository->sendsms($sourceCollection[0]['phone_number'],'Greetings from Hatsun, Your order with order number '.$orderId.' is processed successfully');
        		$emailMessage = $this->socialLoginRepository->sendMailToUsers($sourceCollection[0]['email'],$orderId,'storeownermail');
        		$this->_logger->debug('SourceCollection123',array($sourceCollection));
        		$this->_logger->debug('SourceCollection123 shippingoption',array($shipping_option));

				if($shipping_option=="delivery"){
	        		$this->_logger->debug('shipping_option inside if loop if $shipping_option is zero',array($shipping_option));   
	                $deliveryFees = $this->getDeliveryAmount($customerLat , $customerLong , $storeCode);
	                if(!is_numeric($deliveryFees)){
	                	$this->_logger->critical('deliveryFees Details critical',array($deliveryFees['message']));
	                	return $this;
	                }else{
						$this->_logger->debug('quote id',array($quoteId));
						$this->_logger->debug('shipping address from order data',array($shippingAddress));
						$payment_id_new = '';
						$customRazorpayData = $this->razorpayCollectionFactory->create();
				        $customRazorpayData->addFieldToFilter('quoteId',$quoteId);
				        $custRazorpayDetail = $customRazorpayData->getData();
				        if(isset($custRazorpayDetail)&&count($custRazorpayDetail)>0){
				            $payment_id_new =  $custRazorpayDetail[0]['rzp_payment_id'];
				        }
						$collection = $this->sourceCollectionFactory->create();
						$collection->addFieldToFilter('source_code',$storeCode);						
	                	if(isset($sourceCollection)){
	                		$this->_logger->debug('Source code data before passing to email',array($sourceCollection));
	                		if(isset($sourceCollection[0]['email'])){
	                			$this->socialLoginRepository->sendMailToUsers($sourceCollection[0]['email'],$orderId,'storeownermail');
	                		}                		
							$this->_logger->debug('Source code data before passing to route',array($sourceCollection));
							$object = array (
								"franchise_amount"=> $base_grand_total,
								"dunzo_amount" => $deliveryFees,
								"payment_id"=> $payment_id_new,
								"source_code" => $storeCode,
								"source_acc_id" => $sourceCollection[0]['account_id'],
								"source_acc_name" => $sourceCollection[0]['name']
							);							
							$this->_logger->debug('object body',array($object));
							$routeResponse = $this->routeAmount($object);
							$this->_logger->debug('route response',array($routeResponse));

							// Dunzo API requirements FOR CREATING DUNZO TASK

		                 	$sender_details = array(
								"name"=>  $sourceCollection[0]['name'],
								"phone_number"=> $sourceCollection[0]['phone']
							);					
							$receiver_details = array(
								"name" => $shippingAddress['firstname'],
								"phone_number"=> $shippingAddress['telephone']
							);
							$pickup_details = array(
								"lat"=> (float)$sourceCollection[0]['latitude'],
								"lng"=> (float)$sourceCollection[0]['longitude'],
								"address"=> array(
									"apartment_address" => $sourceCollection[0]['contact_name'],
									"street_address_1" => $sourceCollection[0]['street'],
									"landmark"=>  $sourceCollection[0]['street'],
									"city" =>  $sourceCollection[0]['city'],
									"state"=> $sourceCollection[0]['region'],
									"pincode"=> $sourceCollection[0]['longitude'],
									"country"=> $sourceCollection[0]['country_id']
								)
							);
							$drop_details = array(
								"lat"=> (float)$customerLat,
								"lng"=> (float)$customerLong,
								"address"=> array(
									"apartment_address" => $shippingAddress['firstname'],
									"street_address_1"=> $shippingAddress['street'],
									"landmark"=> $shippingAddress['street'],
									"city"=> $shippingAddress['city'],
									// "state"=>$shippingAddress['region'],
									"state"=>"TamilNadu",
									"pincode"=> $shippingAddress['postcode'],
									"country"=> $shippingAddress['country_id']
								)
							);
							$request_id = md5(uniqid($shippingAddress['telephone'], true));
			            	$package_content =   ["Documents | Books", "Clothes | Accessories", "Electronic Items"];
			            	$package_approx_value = 250;
			            	$special_instructions = "Fragile items. Handle with great care!!";
							$grandTotal = $orderData->getGrandTotal();			
							$shipType = "standard";
							$paymentMethod = $orderData->getPayment()->getMethod();
							$data['request_id'] = $request_id;
				            $data['pickup_details'] = $pickup_details;
							$data['drop_details'] = $drop_details;
							$data['sender_details'] = $sender_details;
							$data['receiver_details'] = $receiver_details;
							$data['package_content'] = $package_content;
							$data['package_approx_value'] = $package_approx_value;
							$data['special_instructions'] = $special_instructions;                  
							$dataInfo = $data;
						    $this->_logger->debug('drop details ',array($drop_details));
							$this->_logger->info('-data Object-'.json_encode($dataInfo));
							$curl = curl_init("https://api.dunzo.in/api/v1/tasks");
					        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
					        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataInfo));
					        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "client-id: " .$this->clientId , "Authorization: ".$this->Authorization));
							$response = curl_exec($curl);
							curl_close($curl);
							$this->_logger->debug('-response without array-'.json_encode($response));
		        			$responseobject[] = json_decode($response, true);
		        			$this->_logger->debug('response object items',array($responseobject));
				            $this->_logger->debug('tracking id ',array ($responseobject[0]['task_id']));
							if(!empty($responseobject)&&$responseobject[0]['task_id']){
								$trackingId = $responseobject[0]['task_id'];
								$dunzo_model = $this->_dunzoFactory->create();

								$dunzo_model->setOrderId($orderId);
								$dunzo_model->setTrackingId($trackingId);
								$dunzo_model->save();							
						    }else{
								$this->_logger->info('--ordersave else--');
							}

						}else{
							return $this;
						}
					}
				}else{
					return $this;
				}
        	}
		
		}
		}catch (\Exception $e) {
			$this->_logger->info('--ordersave observer--'.$e->getMessage());
		}
		return $this;
		
				

	}


 //    public function sourceCodeAdmin($source_code,$increment_id){
 //    	$this->_logger->debug('set source inside increment_id',array($increment_id));
 //    	$this->_logger->debug('set source inside source_code',array($source_code));
 //    	if(isset($source_code) && isset($increment_id)){
 //            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
 //            $orderInterface = $objectManager->create('Magento\Sales\Api\Data\OrderInterface'); 
 //            $order = $orderInterface->loadByIncrementId($increment_id);
 //            if($order->getId()){
 //                $order->setSourceCode($source_code);
 //                $order->save();
 //                $connection = $this->_objectManager->create('\Magento\Framework\App\ResourceConnection');
 //                $conn = $connection->getConnection();
 //                $query = "update HATSUN_SHOP.sales_order_grid set source_code = ".$source_code." where increment_id =".$increment_id;
 //                $data = $conn->query($query);
 //                $resultArr['msg'] = 'Your Data updated Succesfully!';
 //                $resultArr['status'] = 1;
 //            }else{
 //                $resultArr['msg'] = 'Order does not exists!';
 //                $resultArr['status'] = 0;
 //            }

 //        }else{
 //            $resultArr['msg'] = 'Invalid parameters!';
 //            $resultArr['status'] = 0;
 //        }
 //        return $resultArr;
	// }

	public function getLatAndLong($quoteId){
		$collection = $this->collectionFactory->create()->addFieldToSelect('*')->addFieldToFilter("quoteId", $quoteId);   
		return $collection->getData();
	} 

	public function routeAmount($object){
		 // t+1 day plus 3 hours 
        
        // $nexttime = date('d-m-Y h:i:s', strtotime('+1 minutes'));
        // $afterthreehrs = strtotime(strval($nexttime));

        // {"franchise_amount":6000,"dunzo_amount":0,"payment_id":"pay_HNiqqMZ4brpyCY","source_code":"600014","source_acc_id":"acc_H7kr6yMbynaKyU","source_acc_name":"J.K Agency"}
        date_default_timezone_set('Asia/Kolkata');
        $object = [
            "franchise_amount"=>200,
            "payment_id"=>"pay_HOD6ebsWJQFXTh",
            "source_acc_id"=>"acc_H7kr6yMbynaKyU",
            "source_acc_name"=>"J.K Agency",
            "dunzo_amount"=>500
        ];
        $nexttime = date('d-m-Y h:i:s', strtotime('+1 days'));
        $afterthreehrs = strtotime(strval($nexttime));
        $currentTime = date('d-m-Y h:i:s',time());       
        $currentTime1 = time();
        // echo " currentTime ".$currentTime1;
        // echo "current time".$currentTime;
        $payment_id = $object['payment_id'];
        // echo $object['franchise_amount'];
        $dataInfo =   array(  
            "transfers" => [ array(
                "account"=> $object['source_acc_id'],
                "amount"=> $object['franchise_amount'],
                "currency"=> "INR",
                "notes"=> array(
                  "name"=> $object['source_acc_name'],
                  "roll_no"=> "IEC2011025"
                ),
                "linked_account_notes"=> ["roll_no"],
                // "on_hold"=> true,
                // "on_hold_until"=> (int)$afterthreehrs
              ),array (
                "account"=> "acc_H0eUsFSsqBvA9f",
                "amount"=> $object['dunzo_amount'],
                "currency"=> "INR",
                "notes"=> array(                  
                  "name"=> "Dunzo Amount",
                  "roll_no"=> "IEC2011026"
                ),
                "linked_account_notes"=> ["roll_no"],
                // "on_hold"=> true,
                // "on_hold_until"=> (int)$afterthreehrs
              )
            ]
        );         
        $this->_logger->debug('data information in route repository',array ($dataInfo));
        $curl = curl_init("https://api.razorpay.com/v1/payments/".$payment_id."/transfers");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataInfo));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($curl, CURLOPT_USERPWD, "$this->username:$this->password");
        $response = curl_exec($curl);
        curl_close($curl);
        $this->_logger->debug('-response without array-'.json_encode($response));
        $responseobject[] = json_decode($response, true);       
        return $responseobject;    
    }

    public function getDeliveryAmount($custlatitude='' , $custlongitude='' , $storeCode){        
        try{
			$collection = $this->sourceCollectionFactory->create();
			$collection->addFieldToFilter('source_code',$storeCode);
			$sourceCollection = $collection->getData();
			if(count($sourceCollection)>0){
				$this->_logger->debug('response from sourceCollection sales order after save ',array($sourceCollection));
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
			$request->setUri('https://apis-staging.dunzo.in/api/v1/quote');
			$request->setMethod(\Zend\Http\Request::METHOD_GET);
			$this->_logger->debug('customer lat',array($custlatitude));
			$this->_logger->debug('customer long',array($custlongitude)); 
			$this->_logger->debug('source lat',array($lat));
			$this->_logger->debug('source long',array($longitude));  
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


}?>