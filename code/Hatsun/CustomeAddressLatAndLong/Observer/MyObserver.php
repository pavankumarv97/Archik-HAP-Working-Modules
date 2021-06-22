<?php
namespace Hatsun\CustomeAddressLatAndLong\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class MyObserver implements ObserverInterface
{


    protected $_logger;

    public function __construct(
		\Psr\Log\LoggerInterface $logger
    ) {
        $this->_logger = $logger;
		
    }
	


    public function execute(Observer $observer)
    {
        /** @var $orderInstance Order */
        $order = $observer->getEvent()->getOrder();
        echo $orderId = $order->getId();

      

        $username = 'rzp_test_RKxG1LoLkdTy0s';

        $password = '53siy3KRQD3oRo5ni3uwouQJ';

       $body =  array(
                "transfers" => array(
                "account"=> "acc_H0eSQ1fk45fOx8",
                "amount"=> 100,
                "currency"=> "INR",
                "notes"=> array(
                  "name"=> "RV Enterprises - Annanagar",
                  "roll_no"=> "IEC2011025"
                ),
                "linked_account_notes"=> ["roll_no"],
                "on_hold"=> true,
                "on_hold_until"=> 1671222870
              ),array (
                "account"=> "acc_H0eUsFSsqBvA9f",
                "amount"=> 100,
                "currency"=> "INR",
                "notes"=> array(
                  
                  "name"=> "Chendur Enterprises - Thiruvanmiyur",
                  "roll_no"=> "IEC2011026"
                ),
                "linked_account_notes"=> ["roll_no"],
                "on_hold"=> false
              )
            );

        $curl = curl_init("https://api.razorpay.com/v1/payments/pay_H1sknMEXNCJMXH/transfers");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$response = curl_exec($curl);
			curl_close($curl);

            $responseobject[] = json_decode($response, true);

            $this->_logger->debug('response object ',array($responseobject));

    }
}