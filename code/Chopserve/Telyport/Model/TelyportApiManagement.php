<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Chopserve\Telyport\Model;

class TelyportApiManagement implements \Chopserve\Telyport\Api\TelyportApiManagementInterface
{
	protected $_httpClientFactory;
	protected $_logger;
	protected $_telyportFactory;
	protected $orderRepository;
	protected $_customerRepository;
	public function __construct(
		\Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
		\Psr\Log\LoggerInterface $logger,
		\Chopserve\Telyport\Model\TelyportFactory $telyportFactory,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
	) {
		$this->_httpClientFactory   = $httpClientFactory;
		$this->_logger = $logger;
		$this->_telyportFactory = $telyportFactory;
		$this->orderRepository = $orderRepository;
		$this->_customerRepository = $customerRepository;
	}
    /**
     * {@inheritdoc}
     */
    public function telyportApi($param)
    {
       $resultArr = array();
	   $result =  json_encode($param);
	   $resultSet = json_decode($result,true);	
	   $orderId = $resultSet['order_id'];
	   //$sourcePin = $resultSet['source_pincode'];
	   try{
	   $orderData = $this->orderRepository->get($orderId);
	  
	   if ($orderData) {
		   $billingAddress = $orderData->getBillingAddress()->getData();
		   $shippingAddress = $orderData->getShippingAddress()->getData();
		   
		   $phoneNumber = $this->getCustomerPhoneNumber($orderData);
		   if(isset($phoneNumber) && $phoneNumber!= ''){
			   $telephone = $phoneNumber;
		   }else{
			   $telephone = $shippingAddress['telephone'];
		   }
		   $custFirstName = $orderData->getCustomerFirstname();
		   $pack = array(
				'type' => 'medium',
				"isFragile" => false,
				"isSecure" => false,
				"itemTypes" => ["Meat"],
			);
		   $sender = array(
				'name' => $custFirstName,
				'mobileNumber' => $telephone,
			);
			$receiver = array(
				'name' => $custFirstName,
				'mobileNumber' => $telephone,
			);
			$fromAddress = array(
				'address' => $billingAddress['street'],
				'pincode' => '560098',
				'house_no' => '',
				'landmark' => $billingAddress['street']
			);//$billingAddress['street']
			$toAddress = array(
				'address' => $shippingAddress['street'],
				'pincode' => '560098',
				'house_no' => '',
				'landmark' => $shippingAddress['street']
			);//$shippingAddress['street']
			$grandTotal = $orderData->getGrandTotal();
			
		    //$shippingMethod = $orderData->getShippingAddress()->getShippingMethod();
			$payment = $orderData->getPayment();
			$paymentMethod = $payment->getMethodInstance();
			$methodCode = $paymentMethod->getCode(); // cashondelivery
			if($methodCode == 'cashondelivery'){
				$collectionsAmount = number_format((float)$grandTotal, 2);
			}else{
				$collectionsAmount =  0.00;
			}
		    $shipType = "standard";
			//$paymentMethod = $orderData->getPayment()->getMethod();
			$data['fromAddress'] = $fromAddress;
			$data['toAddress'] = $toAddress;
			$data['shipType'] = $shipType;
			$data['pack'] = $pack;
			$data['sender'] = $sender;
			$data['receiver'] = $receiver;
			$data['scheduledTimestamp'] = 0;
			$data['deliveryChargesPayableAt'] = 'Sender';
			$data['collectionsAmount'] = $collectionsAmount;
			settype($data["collectionsAmount"], "float");
			$data['collectionsAmountPayableAt'] = 'Receiver';
			$data['tpCommissionsAmountPayableAt'] = 'Sender';
			$data['paymentMode'] = 'Cash';
			$data['orderType'] = 'RUSH';
			$dataInfo['data'][0] = $data;
			$this->_logger->info('-data-'.json_encode($dataInfo));
			//exit;
			/* $headers = array(
				"Content-type: application/json",
				"Accept: application/json",
				"ApiKey: FBE8FCA6E5158F3FF3D919AB932D7",
			);
			$url = 'https://telyport.com/api/create_bulk_order';

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataInfo));
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$response = curl_exec($ch); 
			curl_close($ch);  */
			$url = 'https://telyport.com/api/create_bulk_order';
			$curl = curl_init();

			  curl_setopt_array($curl, array(
			  CURLOPT_URL => $url,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => json_encode($dataInfo),
			  CURLOPT_SSL_VERIFYHOST => false,
			  CURLOPT_HEADER => false,
			  CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
				"ApiKey: FBE8FCA6E5158F3FF3D919AB932D7"
			  ),
		));
			$response = curl_exec($curl);
			curl_close($curl);
			//echo $response;exit;
			$this->_logger->info('-response-'.json_encode($response));
			$responseData = json_decode($response,true);
			if(!empty($responseData)){
				$telyportId = $responseData['message'][0][0]['id'];
				$this->_logger->info('-telyportId-'.$telyportId);
				$telyport_model = $this->_telyportFactory->create();
				$telyport_model->setData('order_id',$orderId);
				$telyport_model->setData('telyport_id',$telyportId);
				$telyport_model->save();	
					
				return [$telyportId];
			}
			//$this->_logger->info('-data-'.json_encode($data));
			//return $data;exit;
			/* $client = $this->_httpClientFactory->create();
			$url = 'https://telyport.com/api/create_bulk_order';
			$client->setUri($url);
			$client->setConfig(['timeout' => 300]);
			$client->setHeaders(['Content-Type: application/json', 'Accept: application/json','ApiKey:FBE8FCA6E5158F3FF3D919AB932D7']);
			$client->setMethod(\Zend_Http_Client::POST);
			$client->setRawData(json_encode($dataInfo));
			try {
				$responseBody = $client->request()->getBody();	
				$this->_logger->info('--'.json_encode($responseBody));
			} catch (\Exception $e) {
				echo $e->getMessage();
			}  */
		 }
		 } catch (\Exception $e) {
				//echo $e->getMessage();
				$this->_logger->info('--'.json_encode($e->getMessage()));
		}
    }
	
	public function getCustomerPhoneNumber($order){
        return $this->_customerRepository->getById($order->getCustomerId())
				->getCustomAttribute('phone_number')
				->getValue();
    }
}

