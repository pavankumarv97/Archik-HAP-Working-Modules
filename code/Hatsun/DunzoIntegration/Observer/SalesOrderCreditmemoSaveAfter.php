<?php
/**
 * Copyright © 2018 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Hatsun\DunzoIntegration\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Hatsun\CustomRazorpay\Model\ResourceModel\CustomeRazorpayPayment\CollectionFactory as RazorpayCollectionFactory;


/**
 * Class SalesOrderCreditmemoSaveAfter
 *
 */
class SalesOrderCreditmemoSaveAfter implements ObserverInterface
{

    protected $_logger;
    private  $username = "rzp_test_RKxG1LoLkdTy0s";
    private $password = "53siy3KRQD3oRo5ni3uwouQJ";
    protected $razorpayCollectionFactory;
    
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
	RazorpayCollectionFactory $razorpayCollectionFactory
    ) {
        $this->_logger = $logger;
	$this->razorpayCollectionFactory = $razorpayCollectionFactory;
    }

    /**
     * @param Observer $observer
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        try{
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();



        /** @var \Magento\Sales\Model\Order $order */
        $order = $creditmemo->getOrder();
	$quoteId = $order->getQuoteId();
	$this->_logger->debug('quote id details in credit memo',array($quoteId));

	$orderDetails = $order->getData();
	$base_grand_total = $orderDetails['base_subtotal'];
	$base_grand_total = $grand_total * 100;

	$this->_logger->debug('amount from base sub total ',array($base_grand_total));
      
		$customRazorpayData = $this->razorpayCollectionFactory->create();
		$customRazorpayData->addFieldToFilter('quoteId',$quoteId);
		$custRazorpayDetail = $customRazorpayData->getData();

	    $this->_logger->debug('payment id from custom razorpay collection ',array($custRazorpayDetail));
	    $this->_logger->debug('payment id from custom razorpay collection 1',array($custRazorpayDetail[0]['rzp_payment_id']));



        $objectData = array(
            "payment_id"=> $custRazorpayDetail[0]['rzp_payment_id'],
	    "amount" =>$base_grand_total,
            "reverse_all" => 1
        );

        $this->_logger->debug('object in credit memo execute function ',array($objectData));
        $response = $this->refundAmount($objectData);
        $this->_logger->debug('response in credit memo execute function ',array($response));

    }catch (\Exception $e) {
        $this->_logger->info('--ordersave observer--'.$e->getMessage());
    }
    return $this;
    }



    public function refundAmount($object)
    {

      $payment_id = $object['payment_id'];
      $dataInfo['amount'] =  $object['amount'];
      $dataInfo['reverse_all'] = 1;
      
      $this->_logger->debug('-data information-',array($dataInfo));
      $curl = curl_init("https://api.razorpay.com/v1/payments/".$payment_id."/refund");    
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataInfo));
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
      curl_setopt($curl, CURLOPT_USERPWD, "$this->username:$this->password");
      $response = curl_exec($curl);
      curl_close($curl);
      $this->_logger->debug('-response without array in creadit memo-'.json_encode($response));

      $this->_logger->debug('-response with array creadit memo-',array (json_decode($response)));

      $responseobject[] = json_decode($response, true);
      return $responseobject;

    }
}