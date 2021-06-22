<?php
//
//namespace Chopserve\CustomShipping\Observer;
//
//use Chopserve\CustomeStatus\Logger\Logger;
//use Magento\Framework\Event\Observer as EventObserver;
//use Magento\Framework\Event\ObserverInterface;
//use Magento\Framework\HTTP\ZendClientFactory;
//
//class SalesOrderInvoicePay implements ObserverInterface
//{
//    /**
//     * @param EventObserver $observer
//     * @return $this
//     */
//
//    /**
//     * Logging instance
//     * @var Logger
//     */
//    protected $_logger;
//
//    /**
//     * @var ZendClientFactory
//     */
//    private $httpClientFactory;
//
//    /**
//     * Data constructor.
//     *
//     * @param \Magento\Framework\View\Element\Template\Context $context
//     * @param ZendClientFactory $httpClientFactory
//     * @param \Psr\Log\LoggerInterface $logger
//     */
//    public function __construct(
//        \Magento\Framework\View\Element\Template\Context $context,
//        ZendClientFactory $httpClientFactory,
////        \Chopserve\CustomeStatus\Logger\Logger $logger,
//        \Psr\Log\LoggerInterface $logger
//    ) {
//        $this->httpClientFactory = $httpClientFactory;
//        $this->_logger = $logger;
//    }
//
//    public function execute(EventObserver $observer)
//    {
//        $invoice = $observer->getEvent()->getInvoice();
//        $order = $invoice->getOrder();
//
//        /* reset total_paid & base_total_paid of order */
////        $order->setTotalPaid($order->getTotalPaid() - $invoice->getGrandTotal());
////        $order->setBaseTotalPaid($order->getBaseTotalPaid() - $invoice->getBaseGrandTotal());
//
//        if ($order) {
//
//            $billingAddress = $order->getBillingAddress();
//
//            $client = $this->httpClientFactory->create();
//            $client->setUri('https://jsonplaceholder.typicode.com/todos/1');
////            $client->setUri('https://telyport.com/api/create_bulk_order');
//            $client->setMethod(\Zend_http_Client::GET);
////            $client->setMethod(\Zend_http_Client::POST);
//            $client->setHeaders(['Content-Type: application/json', 'Accept: application/json']);
//
//            $response = $client->request();
//            $response = json_decode($response->getBody(), true);
//
////            $ar = implode(',', $billingAddress);
//
////            $this->_logger->debug($ar);
//
//            return $order;
//        }
//    }
//}
