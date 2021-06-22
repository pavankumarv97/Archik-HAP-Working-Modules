<?php
namespace Chopserve\Telyport\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\InventoryShipping\Model\ResourceModel\ShipmentSource\GetSourceCodeByShipmentId;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
class SalesOrderShipmentAfter implements ObserverInterface
{
	protected $shipmentNotifier;
	protected $_logger;
	protected $_httpClientFactory;
	protected $_telyportFactory;
	protected $orderRepository;
	protected $_customerRepository;
	protected $sourceRepository;
	protected $resourceConnection;
	const SHIPMENT_ID = 'shipment_id';
    const SOURCE_CODE = 'source_code';
    public function __construct(
		\Psr\Log\LoggerInterface $logger,		
		\Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
		\Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
		\Chopserve\Telyport\Model\TelyportFactory $telyportFactory,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		GetSourceCodeByShipmentId $sourceCodeByShipmentId,
		SourceRepositoryInterface $sourceRepository,
		ResourceConnection $resourceConnection
    ) {
        $this->_logger = $logger;
		$this->shipmentNotifier = $shipmentNotifier;
		$this->_httpClientFactory   = $httpClientFactory;
		$this->_telyportFactory = $telyportFactory;
		$this->orderRepository = $orderRepository;
		$this->_customerRepository = $customerRepository;
		$this->sourceCodeByShipmentId = $sourceCodeByShipmentId;
		$this->sourceRepository = $sourceRepository;
		$this->resourceConnection = $resourceConnection;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		try{
			$shipment = $observer->getEvent()->getShipment();
			$shipmentId = $shipment->getId();
			$orderId = $shipment->getOrder()->getId();
			$this->_logger->info('--$orderId--'.$orderId);			
			
			
		$orderData = $shipment->getOrder();
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
				'name' => 'Jafar',
				'mobileNumber' => '9743167955',
			);
			$receiver = array(
				'name' => $custFirstName,
				'mobileNumber' => $telephone,
			);
			if(isset($shipmentId) && $shipmentId != ''){
				if (!empty($shipment->getExtensionAttributes())
					&& $shipment->getExtensionAttributes()->getSourceCode()) {
					$sourceCode = $shipment->getExtensionAttributes()->getSourceCode();
				} else {
					$sourceCode = 'default';
				}
				//$sourceCode = $this->getSource($shipmentId);
				if(isset($sourceCode) && $sourceCode != ''){
					//$sourceInfo = $this->sourceRepository->get($sourceCode);
					$sourceInfo = $this->getSourcesByCode($sourceCode);
					$fromAddress = array(
						'address' => $sourceInfo->getStreet(),
						'pincode' => $sourceInfo->getPostcode(),
						'house_no' => '',
						'landmark' => ''
					);
				}else{
					$fromAddress = array(
						'address' => $billingAddress['street'],
						'pincode' => $billingAddress['postcode'],
						'house_no' => '',
						'landmark' => $billingAddress['street']
					);//$billingAddress['street']
				}
			}
			
			$toAddress = array(
				'address' => $shippingAddress['street'],
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
			if(!empty($responseData)){
				$telyportId = $responseData['message'][0][0]['id'];
				$this->_logger->info('-telyportId-'.$telyportId);
				$telyport_model = $this->_telyportFactory->create();
				$telyport_model->setData('order_id',$orderId);
				$telyport_model->setData('telyport_id',$telyportId);
				$telyport_model->save();	
					
				return [$telyportId];
			}
			
		 }
		}catch (\Exception $e) {
			$this->_logger->info('--shipment observer--'.$e->getMessage());
		} 
    }
	public function getSource($shipmentId)
	 {
		$connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection
            ->getTableName('inventory_shipment_source');

        /* $select = $connection->select()
            ->from($tableName, [
                self::SOURCE_CODE => self::SOURCE_CODE
            ])
            ->where(self::SHIPMENT_ID . ' = ?', $shipmentId)
            ->limit(1);
			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/custom.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			$logger->info('--Query-- ' .$select->__toString());

        $sourceCode = $connection->fetchOne($select);
        $sourceCodes = $connection->fetchAll($select); */
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/custom.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			
		$binds_select = array(
			'shipment_id'    => $shipmentId
		);				
		$existQry = "select * from ".$tableName." where shipment_id = '".$shipmentId."' limit 1"; 
		$logger->info('--Query--'.$existQry);
		$resultsExist = $connection->query($existQry);
		$isResExit = $resultsExist->fetchAll();
		$logger->info('--isResExit-- '.json_encode($isResExit));
		$sourceCheck = count($isResExit);
		if(isset($isResExit) && $sourceCheck > 0){	
			$logger->info('--sourceCode-- '.$sourceCheck[0]['source_code']);
			return $sourceCheck[0]['source_code'];			
		}
		return '';
        //return $sourceCode['source_code'];
		// return $this->sourceCodeByShipmentId->execute($shipmentId);
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
}