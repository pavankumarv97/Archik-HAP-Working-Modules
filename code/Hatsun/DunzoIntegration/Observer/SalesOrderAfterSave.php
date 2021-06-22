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
use Hatsun\DunzoIntegration\Model\DunzoRepository as DunzoRepository;
use Hatsun\CustomRazorpay\Model\CustomeRazorpayPaymentRepository as CustomRazorpayRepository;
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
	protected $dunzoRepository;
	protected $customRazorpayRepository;
	protected $messageManager;

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
		 SocialLoginRepository $socialLoginRepository,
		 DunzoRepository $dunzoRepository,
		 CustomRazorpayRepository $customRazorpayRepository,
		 \Magento\Framework\Message\ManagerInterface $messageManager

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
		$this->dunzoRepository = $dunzoRepository;
		$this->customRazorpayRepository = $customRazorpayRepository;
		$this->messageManager = $messageManager;
    }
	
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
				if ($orderData instanceof \Magento\Framework\Model\AbstractModel) {			
					$this->_logger->debug('order status',array($orderData->getData()));
					$quoteId =  $orderData->getQuoteId();
	            	$orderDetails = $orderData->getData();
	            	$customerId = $orderData->getCustomerId();
		        	$base_grand_total = $orderDetails['base_subtotal'];		        	
		        	$increment_id = $orderDetails['increment_id'];
					$billingAddress = $orderData->getBillingAddress()->getData();
					$shippingAddress = $orderData->getShippingAddress()->getData();

					$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
					$customerData = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
					if($customerData->getId()){
						$customer_phone = $customerData->getPhoneNumber();
					}
					

		        	// for storepickup_storepickup
		        	// for customshipping
		        	// for flatrate


		        	$latAndLongDetails = $this->getLatAndLong($quoteId);	
		        	if(isset($latAndLongDetails) && count($latAndLongDetails) > 0){
		        		$storeCode = $latAndLongDetails[0]['storeId'];
						$shipping_option = $latAndLongDetails[0]['shipping_option'];

						// source Collection Data
						$collection = $this->sourceCollectionFactory->create();
						$collection->addFieldToFilter('source_code',$storeCode);
						$sourceCollection = $collection->getData();   

						$customRazorpayData = $this->razorpayCollectionFactory->create();
				        $customRazorpayData->addFieldToFilter('quoteId',$quoteId);
				        $custRazorpayDetail = $customRazorpayData->getData();			
					
						// for delivvery orders
						if($shipping_option == "delivery"){
							$customerLat = $latAndLongDetails[0]['latitude'];
							$customerLong = $latAndLongDetails[0]['longitude'];

							if(count($sourceCollection) > 0){
								$pickup_lat = (float)$sourceCollection[0]['latitude'];
								$pickup_lng = (float)$sourceCollection[0]['longitude'];
								try{
								  $object = [
									  'pickup_lat' => $pickup_lat,
									  'pickup_lng' => $pickup_lng,
									  'drop_lat' => $customerLat,
									  'drop_lng' => $customerLong,
									  'category_id' => "pickup_drop"
								  ];
								  $dunzo_quote = $this->dunzoRepository->getQuote($object);
								  	// dunzo estimation response
								 //  {
									// "category_id": "pickup_drop",
									// "distance": 6.17,
									// "estimated_price": 60.50,
									// "eta": {
									// "pickup": 12,
									// "dropoff": 45}
								 //  }
								  if(isset($dunzo_quote[0]['estimated_price']) && !is_null($dunzo_quote[0]['estimated_price'])){
									 $deliveryFee = $dunzo_quote[0]['estimated_price'];
									
							        if( isset($custRazorpayDetail) && count($custRazorpayDetail) > 0 ){
							            $payment_id_new =  $custRazorpayDetail[0]['rzp_payment_id'];
							            $object = array (
							            	"request_type" => "tranferviapayment",
												"rzp_payment_id"=> $payment_id_new,												
												// "franchise_amount" => $base_grand_total,
												"franchise_amount" => 10,
												"source_acc_id" =>  $sourceCollection[0]['account_id'],
												"source_acc_name" =>  $sourceCollection[0]['name'],
												// "dunzo_amount" => $deliveryFee
												"dunzo_amount" => 10
											);	
											$routeResponse = $this->customRazorpayRepository->razorpayApis($object);
											$this->_logger->debug('salesorderaftersave routeResponse',array($routeResponse));
											$razorpayRoute[] = json_decode($routeResponse, true);
											if(isset($razorpayRoute)>0 ){
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
														"pincode"=> $sourceCollection[0]['postcode'],
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
														"state"=>$shippingAddress['region'],
														"pincode"=> $shippingAddress['postcode'],
														"country"=> $shippingAddress['country_id']
													)
												);
												$request_id = md5(uniqid($shippingAddress['telephone'], true));
								                $package_content =   ["Documents | Books", "Clothes | Accessories", "Electronic Items"];
								                $package_approx_value = $orderDetails['base_subtotal'];
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
												$dunzoTask = $this->dunzoRepository->createTasks($dataInfo);
												$dunzoTaskData[] = json_decode($dunzoTask, true);
												$this->_logger->debug('dunzo response',array($dunzoTaskData));
												$this->_logger->debug('tracking id ',array ($dunzoTaskData[0]['task_id']));										
												if(!empty($dunzoTaskData) && $dunzoTaskData[0]['task_id']){
													$trackingId = $dunzoTaskData[0]['task_id'];
													$dunzo_model = $this->_dunzoFactory->create();
													$dunzo_model->setOrderId($orderId);
													$dunzo_model->setCustomerId($orderData->getCustomerId());
													$dunzo_model->setDunzoStatus($dunzoTaskData[0]['state']);
													$dunzo_model->setDunzoAmount($dunzoTaskData[0]['estimated_price']);
													$dunzo_model->setTrackingId($trackingId);
													$dunzo_model->save();							
											    }else{
													$this->_logger->info('--ordersave else--');
												}
											}else{
												$this->_logger->debug('route response error',array($routeResponse));
											}
											$this->_logger->debug('route response',array($routeResponse));
											$result['state'] = 1;
											$result['message'] = 'success';	
							         }
								   }else{
										$result['message'] = $dunzo_quote[0]['message'];   
										return $this; 
								   }

								}catch(\Magento\Framework\Exception\LocalizedException $e){
									$this->messageManager->addError($e,__('Error in Order', $e->getMessage()));
								}			
							}						
						}else{
							// pickup order
							// SMS notification and 
							$this->_logger->info('--ordersave pickup--');
						}
						// send notification
						// send sms
						try{
							$emailMessage = $this->socialLoginRepository->sendMailToUsers($sourceCollection[0]['email'],$increment_id,'storeownermail');
							// $smsMessage = $this->socialLoginRepository->sendsms($sourceCollection[0]['phone'],'Greetings from Hatsun, Your order with order number '.$increment_id.' is processed successfully');
							if($shipping_option == "delivery"){
								$Amount = $orderDetails['base_subtotal'] * 2;
							}else{
								$Amount = $orderDetails['base_subtotal'];
							}				
							if(isset($custRazorpayDetail['rzp_order_id'])){
								$sendSMStoCustomer = $this->socialLoginRepository->sendsms($customer_phone,'Greetings from Hatsun, the Payment of Rupees '.$Amount.' with Transaction ID '.$custRazorpayDetail['rzp_order_id'].' has been received and order has been placed successfully');
							}else{
								$sendSMStoCustomer = $this->socialLoginRepository->sendsms($customer_phone,'Greetings from Hatsun, the Payment of Rupees '.$Amount.' with Transaction ID '.$increment_id.' has been received and order has been placed successfully');
							}				
							
						}catch(Exception $e){
							$result['state'] = 0;
						}
					}
					return $this;
				}
			}
		}catch(\Magento\Framework\Exception\LocalizedException $e){
          $this->messageManager->addError($e,__('Something Went Wrong', $e->getMessage()));
      }
	}	
	public function getLatAndLong($quoteId){
		$collection = $this->collectionFactory->create()->addFieldToSelect('*')->addFieldToFilter("quoteId", $quoteId);   
		return $collection->getData();
	} 		
}


?>