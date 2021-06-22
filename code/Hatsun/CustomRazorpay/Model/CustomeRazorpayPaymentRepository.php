<?php


namespace Hatsun\CustomRazorpay\Model;

use Hatsun\CustomRazorpay\Api\CustomeRazorpayPaymentRepositoryInterface;
use Magento\Framework\HTTP\ZendClientFactory;
use Hatsun\CustomRazorpay\Model\ResourceModel\CustomeRazorpayPayment\CollectionFactory;
use Hatsun\CustomRazorpay\Model\ResourceModel\CustomeRazorpayPayment as ResourceCustomeRazorpayPayment;
use Hatsun\CustomRazorpay\Model\CustomeRazorpayPaymentFactory;





class CustomeRazorpayPaymentRepository implements CustomeRazorpayPaymentRepositoryInterface
{
   
    private $httpClientFactory;
    private $customeRazorpayPaymentFactory;
    private $logger;
    private $collectionFactory;
    protected $resource;
    protected $messageManager;

    public function __construct(
        ZendClientFactory $httpClientFactory,
        ResourceCustomeRazorpayPayment $resource,
        \Psr\Log\LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        CustomeRazorpayPaymentFactory $customeRazorpayPaymentFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->httpClientFactory = $httpClientFactory;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->resource = $resource;
        $this->customeRazorpayPaymentFactory = $customeRazorpayPaymentFactory;
        $this->messageManager = $messageManager;
       
    }

    private $razorpayUrl = "https://api.razorpay.com/v1/";
    private $razorpay_key_id = "rzp_live_Vz8uIp1N7EfRBF";
    private $razorpay_key_secret = "gloEUqmhsls7QuSpshv2kf1W";


    
        public function saveObject($object){
    
           $updateFactory = $this->customeRazorpayPaymentFactory->create()->load($object['quoteId'],"quoteId");
           $this->logger->debug('custome razorpay',array($updateFactory->getData()));
            if($updateFactory){
                foreach( $object as $key => $value){
                    $updateFactory->setData($key ,$value); 
                }
                $obj =  $this->resource->save($updateFactory);
                $this->logger->debug('updated custome razorpay',array($updateFactory->getData()));
                // return $obj;
            }
            else{
                $factory =  $this->customeRazorpayPaymentFactory->create();
                $factory->setRzpOrderId($object['rzp_order_id']);
                $factory->setRzpPaymentId($object['rzp_payment_id']);
                $factory->setRzpSignature($object['rzp_signature']);
                $factory->setCustomerId($object['customerId']);
                $factory->setQuoteId($object['quoteId']);
                $factory->setStoreId($object['storeId']);
                $factory->save();
                $result[0]['data'] = $factory->getData();
                $result[0]['msg'] = "uploaded successfully";
                return $result;
            }
          
        }


       
//     // for route payemnt
  // "params": {
  //       "request_type": "fetchtransfer",
  //       "rzp_transfer_id": "trf_HPL0KwKu4SjX2w",
  //       "rzp_payment_id": "pay_HPGbaXz5wJUuJx",
  //        "franchise_amount": 5,
  //        "source_acc_id" :  "acc_H7kr6yMbynaKyU",
  //        "source_acc_name" : "J K Agencies Royapettah",
  //        "dunzo_amount" : 5
 
  //   }
 
 


        /**
         * @param mixed $params
         *
         * @return mixed
         */
        public function razorpayApis($params){
            $request_type = $params['request_type'];
            if($request_type){
                switch ($request_type) {
                    // Fetch Order Api Calls
                    case 'fetchorder':
                        $requestUrl = $this->razorpayUrl."orders";
                        $requestMethod = "GET";
                        break;
                    case 'fetchorderbyorderid':
                        $requestUrl = $this->razorpayUrl."orders/".$params['order_id'];
                        $requestMethod = "GET";
                        break;
                    case 'fetchpaymentsbyorderid':
                        $requestUrl = $this->razorpayUrl."orders/".$params['order_id']."/payments";
                        $requestMethod = "GET";
                        break; 
                    // Refund Api Calls
                    case 'getfullrefund':
                        $requestUrl = $this->razorpayUrl."payments/".$params['rzp_payment_id']."/refund";
                        $requestMethod = "POST";
                        $requestData = [
                            "reverse_all" => 1
                        ];
                        break;
                    case 'fetchrefunds':
                        $requestUrl = $this->razorpayUrl."refunds";
                        $requestMethod = "GET";
                    case 'fetchrefundbyid':
                        $requestUrl = $this->razorpayUrl."refunds/".$params['rzp_refund_id'];
                        $requestMethod = "GET";
                    case 'fetchallrefundsforpayment':
                        $requestUrl = $this->razorpayUrl."payments/".$params['rzp_payment_id']."/refunds";
                        $requestMethod = "GET";

                    // Route Api Calls

                    case 'tranferviapayment':
                        $requestUrl = $this->razorpayUrl."payments/".$params['rzp_payment_id']."/transfers";                        
                        $requestMethod = "POST";
                        date_default_timezone_set('Asia/Kolkata');
                        $payment_id = $params['rzp_payment_id'];
                        $franchise_amount = $params['franchise_amount'] * 100 ;
                        $dunzo_amount = $params['dunzo_amount'] * 100;
                        $object = [
                            "franchise_amount"=> $franchise_amount,
                            "payment_id"=> $params['rzp_payment_id'],
                            "source_acc_id"=> $params['source_acc_id'],
                            "source_acc_name"=> $params['source_acc_name'],
                            "dunzo_amount"=> $dunzo_amount
                        ];
                        $nexttime = date('d-m-Y h:i:s', strtotime('+1 days'));
                        $afterthreehrs = strtotime(strval($nexttime));
                        $currentTime = date('d-m-Y h:i:s',time());       
                        $currentTime1 = time();
                        $requestData =   array(  
                            "transfers" => [ array(
                                "account"=> $object['source_acc_id'],
                                "amount"=> $object['franchise_amount'],
                                "currency"=> "INR",
                                "notes"=> array(
                                  "name"=> $object['source_acc_name'],
                                  "roll_no"=> "IEC2011025"
                                ),
                                "linked_account_notes"=> ["roll_no"],
                                "on_hold"=> true,
                                "on_hold_until"=> (int)$afterthreehrs
                              ),array (
                                "account"=> "acc_HNzU4pkT6mkAXv",
                                "amount"=> $object['dunzo_amount'],
                                "currency"=> "INR",
                                "notes"=> array(                  
                                  "name"=> "Dunzo Amount",
                                  "roll_no"=> "IEC2011026"
                                ),
                                "linked_account_notes"=> ["roll_no"],
                                "on_hold"=> true,
                                "on_hold_until"=> (int)$afterthreehrs
                              )
                            ]
                        );          
                        break;
                    case 'directtransfer':
                        $requestUrl = $this->razorpayUrl."payments/".$params['rzp_payment_id']."/transfers";
                        $requestMethod = "POST";
                        $requestData = $params['directtransfer'];
                        break;
                    case 'fetchtransferviapayment':
                        $requestUrl = $this->razorpayUrl."payments/".$params['rzp_payment_id']."/transfers";
                        $requestMethod = "GET";
                        break;
                    case 'fetchtransfer':
                        $requestUrl = $this->razorpayUrl."transfers/".$params['rzp_transfer_id'];
                        $requestMethod = "GET";
                        break;
                    default:
                        # code...
                        break;
                }
                if($requestMethod == "POST"){
                    try{
                        $curl = curl_init($requestUrl);
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestData));
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                        curl_setopt($curl, CURLOPT_USERPWD, $this->razorpay_key_id.":".$this->razorpay_key_secret);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
                        $response = curl_exec($curl);
                        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                        curl_close($curl);
                        if($httpcode == 200){
                            return $response;
                        }else{
                            $this->messageManager->addErrorMessage("Invalid Response",$response);
                        }                        
                    }catch(\Magento\Framework\Exception\LocalizedException $e){
                        $this->messageManager->addError($e,__('Error from payment', $e->getMessage()));
                    }                    
                }else if($requestMethod == "GET"){
                    try{
                        $ch = curl_init($requestUrl);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                        curl_setopt($ch, CURLOPT_USERPWD, $this->razorpay_key_id.":".$this->razorpay_key_secret);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
                        $response = curl_exec($ch);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);
                        if($httpcode == 200){
                            return $response;

                            die(); 
                        }else{
                            $this->messageManager->addErrorMessage("Invalid Response",$response);
                        }                              
                    }catch(\Magento\Framework\Exception\LocalizedException $e){
                         $this->messageManager->addError($e,__('Error from payment', $e->getMessage()));
                    }
                    
                }

                    
            }
           

        }

         /**
         * @param mixed $params
         *
         * @return mixed
         */
        public function refunds($params){
            echo "refunds";

        }

         /**
         * @param mixed $params
         *
         * @return mixed
         */
        public function routes($params){
            echo "routes";

        }

    
      
    }

