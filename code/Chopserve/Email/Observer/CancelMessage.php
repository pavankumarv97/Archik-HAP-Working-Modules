<?php
namespace Chopserve\Email\Observer; 
use Magento\Framework\Event\ObserverInterface; 
use Magento\Framework\HTTP\ZendClientFactory;
class CancelMessage implements ObserverInterface { 

	protected $_logger;
	protected $httpClientFactory;
	protected $_customer;
    public function __construct(
		\Psr\Log\LoggerInterface $logger,		
		ZendClientFactory $httpClientFactory,
		\Magento\Customer\Model\Customer $customer
    ) {
        $this->_logger = $logger;
		$this->httpClientFactory = $httpClientFactory;
		$this->_customer = $customer;
    } 
	public function execute(\Magento\Framework\Event\Observer $observer) { 
		$order = $observer->getEvent()->getOrder();
		$orderId = $order->getIncrementId();
		$customerId = $order->getCustomerId();
		if(isset($customerId) && $customerId != ''){
			$customerObj = $this->_customer->load($customerId); 
			$phoneNumber = $customerObj->getPhoneNumber(); 
			//$this->_logger->info('--customerObj--'.json_encode($customerObj->getData()));
			//$this->_logger->info('--getPhoneNumber--'.$phoneNumber);
		}else{
			$phoneNumber = $order->getShippingAddress()->getTelephone();
		}
		if(isset($phoneNumber) && $phoneNumber != ''){
			$this->sendCancelMessage($phoneNumber, $orderId);
		}
   }
   public function sendCancelMessage($phoneNumber, $orderId)
    {
        $client = $this->httpClientFactory->create();
        $client->setUri('http://onex-ultimo.in/api/pushsms');
        $client->setMethod(\Zend_http_Client::GET);
        $client->setHeaders(['Content-Type: application/json', 'Accept: application/json']);

        $params = [
            "user" => "Chopserveotp",
            'authkey' => '92G5bb5WT5dcs',
            'sender' => 'CHPSRV',
            'mobile' => $phoneNumber,
            'text' => 'Order Cancelled: Your order has been cacelled; Order number :' . $orderId,
            'output' => 'json'
        ];
        $client->setParameterGet($params);
        return $client->request();
    }
}