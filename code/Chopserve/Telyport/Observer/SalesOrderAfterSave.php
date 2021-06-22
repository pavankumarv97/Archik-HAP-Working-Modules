<?php
namespace Chopserve\Telyport\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\InventoryShipping\Model\ResourceModel\ShipmentSource\GetSourceCodeByShipmentId;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Chopserve\SourceMapping\Model\ResourceModel\Mapping\CollectionFactory as MappingCollectionFactory;
class SalesOrderAfterSave implements ObserverInterface
{
	protected $_logger;
	protected $_httpClientFactory;
	protected $_telyportFactory;
	protected $orderRepository;
	protected $_customerRepository;
	protected $sourceRepository;
	protected $_sourceMapping;
	protected $mappingCollectionFactory;
	const AWAITING_PICKUP = 'awaiting_pickup';
	const APIKEY  = 'FBE8FCA6E5158F3FF3D919AB932D7';
	const SENDER  = 'Jafar';
	const SENDER_MOBILE  = '9743167955';
    public function __construct(
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
		\Chopserve\Telyport\Model\TelyportFactory $telyportFactory,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		GetSourceCodeByShipmentId $sourceCodeByShipmentId,
		SourceRepositoryInterface $sourceRepository,
		\Chopserve\SourceMapping\Model\MappingRepository $sourceMapping,
		MappingCollectionFactory $mappingCollectionFactory
    ) {
        $this->_logger = $logger;
		$this->_httpClientFactory   = $httpClientFactory;
		$this->_telyportFactory = $telyportFactory;
		$this->orderRepository = $orderRepository;
		$this->_customerRepository = $customerRepository;
		$this->sourceCodeByShipmentId = $sourceCodeByShipmentId;
		$this->sourceRepository = $sourceRepository;
		$this->_sourceMapping = $sourceMapping;
		$this->mappingCollectionFactory = $mappingCollectionFactory;
    }
	
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		try{
		$orderData = $observer->getEvent()->getOrder();
		$orderId = $observer->getEvent()->getOrder()->getId();
		if ($orderData instanceof \Magento\Framework\Model\AbstractModel) {
			
		    if($orderData->getStatus() == 'awaiting_pickup') {
					
				  $billingAddress = $orderData->getBillingAddress()->getData();
				   $shippingAddress = $orderData->getShippingAddress()->getData();
				   $destPincode = $shippingAddress['postcode'];
				   //$this->_logger->info('--destPincode-'.$destPincode);
				   $sourcePincode = $this->getSourcePinCode($destPincode);
				  // $this->_logger->info('--sourcePincode-'.json_encode($sourcePincode));
				   if(isset($sourcePincode) && $sourcePincode != ''){
					   $sourceCode = $sourcePincode;
				   }else{
					   $sourceCode = '560034';
				   }
				   $this->_logger->info('--sourcePincode-'.$sourcePincode);
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
						'name' => self::SENDER,
						'mobileNumber' => self::SENDER_MOBILE,
					);
					$receiver = array(
						'name' => $custFirstName,
						'mobileNumber' => $telephone,
					);
					$this->_logger->info('--destPincode-'.$destPincode);
					if(isset($sourceCode) && $sourceCode != ''){
						$sourceInfo = $this->getSourcesByCode($sourceCode);
						$this->_logger->info('-response-'.json_encode($sourceInfo->getData()));
						$fromAddress = array(
							'address' => $sourceInfo->getStreet(),
							'lat' => $sourceInfo->getLatitude(),
							'lng' => $sourceInfo->getlongitude(),
							'pincode' => $sourceCode,
							'house_no' => '',
							'landmark' => ''
						);
					}else{
						$fromAddress = array(
							'address' => $billingAddress['street'],
							'pincode' => $billingAddress['postcode'],
							'house_no' => '',
							'landmark' => $billingAddress['street']
						);
					}
					if(isset($shippingAddress['latitude']) && $shippingAddress['latitude'] !=''){
						$lattitude = $shippingAddress['latitude'];
					}else{
						$lattitude = '';
					}
					if(isset($shippingAddress['longitude']) && $shippingAddress['longitude'] !=''){
						$longitude = $shippingAddress['longitude'];
					}else{
						$longitude = '';
					}
					$toAddress = array(
						'address' => $shippingAddress['street'],
						'lat' => $lattitude,
						'lng' => $longitude,
						'pincode' => $shippingAddress['postcode'],
						'house_no' => '',
						'landmark' => $shippingAddress['street']
					);//$shippingAddress['street']
					$grandTotal = $orderData->getGrandTotal();			
					$shipType = "standard";
					$paymentMethod = $orderData->getPayment()->getMethod();
					$data['fromAddress'] = $fromAddress;
					$data['toAddress'] = $toAddress;
					$data['shipType'] = $shipType;
					$data['pack'] = $pack;
					$data['sender'] = $sender;
					$data['receiver'] = $receiver;
					$data['scheduledTimestamp'] = 0;
					$data['deliveryChargesPayableAt'] = 'Sender';
					$data['collectionsAmount'] = number_format((float)$grandTotal, 2);
					settype($data["collectionsAmount"], "float");
					$data['collectionsAmountPayableAt'] = 'Sender';
					$data['tpCommissionsAmountPayableAt'] = 'Sender';
					$data['paymentMode'] = 'Cash';
					$data['orderType'] = 'SEND';
					$dataInfo['data'][0] = $data;
					$this->_logger->info('-data-'.json_encode($dataInfo));
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
					$this->_logger->info('-response-'.json_encode($response));
					$responseData = json_decode($response,true);
					if(!empty($responseData) && isset($responseData['message'][0][0]['id'])){
						$telyportId = $responseData['message'][0][0]['id'];
						$this->_logger->info('-telyportId-'.$telyportId);
						$telyport_model = $this->_telyportFactory->create();
						$telyport_model->setData('order_id',$orderId);
						$telyport_model->setData('telyport_id',$telyportId);
						$telyport_model->save();	
					}
				
		    }else{
				$this->_logger->info('--ordersave else--');
			}
		}
		}catch (\Exception $e) {
			$this->_logger->info('--ordersave observer--'.$e->getMessage());
		}
		return $this;
	}
	public function getCustomerPhoneNumber($orderData){
		 return $this->_customerRepository->getById($orderData->getCustomerId())
				->getCustomAttribute('phone_number')
				->getValue();
	 }
	public function getSourcesByCode($sourceCode)
    {
        return $this->sourceRepository->get($sourceCode);
    }
	public function getSourcePinCode($pincode)
    {
        return $this->getSourcePostcode($pincode);
    }

    public function getSourcePostcode($postcode)
    {
        $collection = $this->mappingCollectionFactory->create()->getItems();
        foreach ($collection as $item) {
			
            $desPostcodes = explode(",", $item['pincodes']);
            foreach ($desPostcodes as $desPostcode) {
                if ($desPostcode == $postcode) {
                    return $item['source_pincode'];
                }
            }
        }
        return '560034';
    }
}

?>