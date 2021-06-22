<?php


namespace Hatsun\CustomeAddressLatAndLong\Model;

use Hatsun\CustomeAddressLatAndLong\Api\RouteRepositoryInterface;
use Magento\Framework\HTTP\ZendClientFactory;
use Hatsun\CustomeAddressLatAndLong\Model\ResourceModel\LatandLong\CollectionFactory;
use Hatsun\CustomeAddressLatAndLong\Model\ResourceModel\LatandLong as ResourceLatandLong;
use Hatsun\CustomeAddressLatAndLong\Model\LatandLongFactory;





class RouteRepository implements RouteRepositoryInterface
{


    private $logger;


    private  $username = "rzp_test_RKxG1LoLkdTy0s";
    private $password = "53siy3KRQD3oRo5ni3uwouQJ";


    public function __construct(
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->logger = $logger;
    }


    

    public function routeAmount($object)
    {


      $payment_id = $object['payment_id'];
      

      // $url = "https://api.razorpay.com/v1/payments/".$payment_id."/transfers";
      // $this->logger->debug('payment id in route repository',array ($url));


     $dataInfo =   array(
  
            "transfers" => [ array(
                "account"=> "acc_H0eSQ1fk45fOx8",
                "amount"=> $object['franchise_amount'],
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
                "amount"=> $object['dunzo_amount'],
                "currency"=> "INR",
                "notes"=> array(
                  
                  "name"=> "Chendur Enterprises - Thiruvanmiyur",
                  "roll_no"=> "IEC2011026"
                ),
                "linked_account_notes"=> ["roll_no"],
                "on_hold"=> false
              )
            ]
            );         


            // $responseData = json_decode($response,true);
            $this->logger->debug('data information in route repository',array ($dataInfo));

            $this->logger->debug('object coming from request body',array ($object));


        $curl = curl_init("https://api.razorpay.com/v1/payments/".$payment_id."/transfers");

        
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataInfo));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($curl, CURLOPT_USERPWD, "$this->username:$this->password");
	    	$response = curl_exec($curl);
	  		curl_close($curl);
		  	$this->logger->debug('-response without array-'.json_encode($response));

        $this->logger->debug('-response with array-',array (json_decode($response)));

        $responseobject[] = json_decode($response, true);
       
       
        return $responseobject;
      
    }



    public function reversalAmount($object)
    {

      $transfer_id = $object['transfer_id'];


      $dataInfo['amount'] =  $object['amount'];

      $curl = curl_init("https://api.razorpay.com/v1/transfers/".$transfer_id."/reversals");

        
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataInfo));
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
      curl_setopt($curl, CURLOPT_USERPWD, "$this->username:$this->password");
      $response = curl_exec($curl);
      curl_close($curl);
      $this->logger->debug('-response without array-'.json_encode($response));

      $this->logger->debug('-response with array-',array (json_decode($response)));

      $responseobject[] = json_decode($response, true);
      return $responseobject;


    }



    public function refundAmount($object)
    {


      $payment_id = $object['payment_id'];
      $dataInfo['amount'] =  $object['amount'];
      $dataInfo['reverse_all'] = 1;
      
      $this->logger->debug('-data information-',array($dataInfo));
      // $curl = curl_init("https://api.razorpay.com/v1/transfers/".$transfer_id."/reversals");
      $curl = curl_init("https://api.razorpay.com/v1/payments/".$payment_id."/refund");    
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataInfo));
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
      curl_setopt($curl, CURLOPT_USERPWD, "$this->username:$this->password");
      $response = curl_exec($curl);
      curl_close($curl);
      $this->logger->debug('-response without array-'.json_encode($response));

      $this->logger->debug('-response with array-',array (json_decode($response)));

      $responseobject[] = json_decode($response, true);
      return $responseobject;

    }

}