<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Chopserve\Telyport\Observer\Sales;

class OrderInvoicePay implements \Magento\Framework\Event\ObserverInterface
{
	protected $_httpClientFactory;
	protected $_logger;
	protected $_telyportFactory;
	public function __construct(
		\Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
		\Psr\Log\LoggerInterface $logger,
		\Chopserve\Telyport\Model\TelyportFactory $telyportFactory
	) {
		$this->_httpClientFactory   = $httpClientFactory;
		$this->_logger = $logger;
		$this->_telyportFactory = $telyportFactory;
	}
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $invoice = $observer->getEvent()->getInvoice();
		$order = $invoice->getOrder();
		$incrementId = $order->getIncrementId();
		$orderId = $order->getId();
		$this->_logger->info('-incre-'.$incrementId);
		$this->_logger->info('-orderId-'.$orderId);
		$telyport_model = $this->_telyportFactory->create();
		$telyport_model->setData('order_id',$incrementId);
		$telyport_model->setData('telyport_id',2);
		$telyport_model->save();
		return $this;
		 if ($order) {
			$data['fromAddress'] = 1;
			$data['toAddress'] = 1;
			$data['shipType'] = 1;
			$data['pack'] = 1;
			$data['sender'] = 1;
			$data['receiver'] = 1;
			$data['scheduledTimestamp'] = 1;
			$data['deliveryChargesPayableAt'] = 1;
			$data['collectionsAmount'] = 1;
			$data['collectionsAmountPayableAt'] = 1;
			$data['tpCommissionsAmountPayableAt'] = 1;
			$data['paymentMode'] = 1;
			$data['orderType'] = 1;
			$client = $this->_httpClientFactory->create();
			$url = 'https://telyport.com/api/create_bulk_order';
			$client->setUri($url);
			$client->getUri()->setPort($port);
			$client->setConfig(['timeout' => 300]);
			$client->setHeaders(['Content-Type: application/json', 'Accept: application/json','ApiKey:FBE8FCA6E5158F3FF3D919AB932D7']);
			$client->setMethod(\Zend_Http_Client::POST);
			$client->setRawData(json_encode($data));
			try {
				$responseBody = $client->request()->getBody();	
				$this->_logger->info('--'.json_encode($responseBody));
			} catch (\Exception $e) {
				echo $e->getMessage();
			}
		 }
    }
}

