<?php
namespace Hatsun\DunzoIntegration\Observer;
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
	protected $_dunzoFactory;
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
		\Hatsun\DunzoIntegration\Model\DunzoFactory $dunzoFactory,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		GetSourceCodeByShipmentId $sourceCodeByShipmentId,
		SourceRepositoryInterface $sourceRepository,
		ResourceConnection $resourceConnection
    ) {
        $this->_logger = $logger;
		$this->shipmentNotifier = $shipmentNotifier;
		$this->_httpClientFactory   = $httpClientFactory;
		$this->_dunzoFactory = $dunzoFactory;
		$this->orderRepository = $orderRepository;
		$this->_customerRepository = $customerRepository;
		$this->sourceCodeByShipmentId = $sourceCodeByShipmentId;
		$this->sourceRepository = $sourceRepository;
		$this->resourceConnection = $resourceConnection;
    }


    private $clientId = '5674abe4-14f7-4c5a-bce1-1bc896aa8f05';
    private $clientSecret = 'a29d4a17-a828-4d1f-8816-4d5e31521463';
    private $Authorization = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkIjp7InJvbGUiOjEwMCwidWlkIjoiZWM4OTI4ZmYtMGJmMS00NzczLTk2MDAtMWIxNDJiNDA5NTAwIn0sIm1lcmNoYW50X3R5cGUiOm51bGwsImNsaWVudF9pZCI6IjU2NzRhYmU0LTE0ZjctNGM1YS1iY2UxLTFiYzg5NmFhOGYwNSIsImF1ZCI6Imh0dHBzOi8vaWRlbnRpdHl0b29sa2l0Lmdvb2dsZWFwaXMuY29tL2dvb2dsZS5pZGVudGl0eS5pZGVudGl0eXRvb2xraXQudjEuSWRlbnRpdHlUb29sa2l0IiwibmFtZSI6InRlc3RfNjY3MTY4NTk0NyIsInV1aWQiOiJlYzg5MjhmZi0wYmYxLTQ3NzMtOTYwMC0xYjE0MmI0MDk1MDAiLCJyb2xlIjoxMDAsImR1bnpvX2tleSI6ImNmMDU2M2UzLTAzMjYtNDVkNC1iNTZlLTlhN2QzMzZmOTljMiIsImV4cCI6MTc3MzQ2MjU1OCwidiI6MCwiaWF0IjoxNjE3OTQyNTU4LCJzZWNyZXRfa2V5IjoiZDc3MTMwNTEtYWRiMi00NTNiLWE3ODktZjY2YzY3NjJkOWQxIn0.4gLpii3cTz9RWl2UzTEpg1oVXvQ3I6hfQ6YczL0VqBQ';


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

           $this->_logger->debug('-shipment address-'.json_encode($shippingAddress));
		   
		//    $phoneNumber = $this->getCustomerPhoneNumber($orderData);
		   if(isset($phoneNumber) && $phoneNumber!= ''){
			   $telephone = $phoneNumber;
		   }else{
			   $telephone = $shippingAddress['telephone'];
		   }
		   $custFirstName = $orderData->getCustomerFirstname();
		   
        $sender_details = array(
            "name"=> "Puneet",
            "phone_number"=> "9999999999"
        );
            
            $receiver_details = array(
                "name" => "Vijendra",
                "phone_number"=> "9999999998"
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
                    $this->_logger->info('-sourceInfo-'.json_encode($sourceInfo));
                    $pickup_details = array(
                        "lat"=>12.9063,
                        "lng"=> 77.5904,
                        "address"=> array(
                            "apartment_address" => "200 Block 4",
                            "street_address_1" => "Suncity Apartments",
                            "street_address_2" => "Bellandur",
                            "landmark"=> "Iblur lake",
                            "city" => "Bangalore",
                            "state"=> "Karnataka",
                            "pincode"=> "560103",
                            "country"=> "India"
                        )
                        );
				}else{
                    $pickup_details = array(
                        "lat"=>12.9063,
                        "lng"=> 77.5904,
                        "address"=> array(
                            "apartment_address" => "200 Block 4",
                            "street_address_1" => "Suncity Apartments",
                            "street_address_2" => "Bellandur",
                            "landmark"=> "Iblur lake",
                            "city" => "Bangalore",
                            "state"=> "Karnataka",
                            "pincode"=> "560103",
                            "country"=> "India"
                        )
                        );
				}
			}
			
            $drop_details = array(
                "lat"=> 12.9198,
                "lng"=> 77.5777,
                "address"=> array(
                    "apartment_address" => "204 Block 4",
                    "street_address_1"=> "Suncity Apartments",
                    "street_address _2"=> "Bellandur",
                    "landmark"=> "Iblur lake",
                    "city"=> "Bangalore",
                    "state"=> "Karnataka",
                    "pincode"=> "560103",
                    "country"=> "India"
                )
                );

            $request_id ="b115d54b-c044-4387-a629-u86gjkbg";
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


			$this->_logger->info('-data-'.json_encode($dataInfo));			
			

        $curl = curl_init("https://apis-staging.dunzo.in/api/v1/tasks");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataInfo));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "client-id :" .$this->clientId , "Authorization :".$this->Authorization));
		$response = curl_exec($curl);
			curl_close($curl);
			$this->_logger->debug('-response without array-'.json_encode($response));

            $this->_logger->debug('-response with array-',array (json_decode($response)));


			// $responseData = json_decode($response,true);
            // $this->_logger->debug('-tracking id-',array ($responseData['task_id']));
            $responseobject[] = json_decode($response, true);
            // $this->_logger->debug('tracking id ',array ($responseobject[0]['task_id']));

            $this->_logger->debug('response object items',array($responseobject));

            $this->_logger->debug('tracking id ',array ($responseobject[0]['task_id']));
			if(!empty($responseobject)){
				$trackingId = $responseobject[0]['task_id'];
                // $trackingId =  '3d7119e6-720a-4e4b-a76d-ed1817e85b8f';
				$this->_logger->info('-trackingId-'.$trackingId);
				$dunzo_model = $this->_dunzoFactory->create();
				$dunzo_model->setData('order_id',$orderId);
				$dunzo_model->setData('tracking_id',$trackingId);
				$dunzo_model->save();	
					
				return [$trackingId];
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


	//  public function getCustomerPhoneNumber($orderData){
	// 	 return $this->_customerRepository->getById($orderData->getCustomerId())
	// 			->getCustomAttribute('9632127294')
	// 			->getValue();
	//  }
	 
	 public function getSourcesByCode($sourceCode)
    {
        return $this->sourceRepository->get($sourceCode);
    }
}