<?php

namespace Hatsun\DunzoIntegration\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SalesQuoteCollectTotalsBefore implements ObserverInterface
{


	protected $_logger;

    public function __construct(
		\Psr\Log\LoggerInterface $logger		
    ) {
        $this->_logger = $logger;
    }


    private $clientId = '5674abe4-14f7-4c5a-bce1-1bc896aa8f05';
    private $clientSecret = 'a29d4a17-a828-4d1f-8816-4d5e31521463';
    private $Authorization = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkIjp7InJvbGUiOjEwMCwidWlkIjoiZWM4OTI4ZmYtMGJmMS00NzczLTk2MDAtMWIxNDJiNDA5NTAwIn0sIm1lcmNoYW50X3R5cGUiOm51bGwsImNsaWVudF9pZCI6IjU2NzRhYmU0LTE0ZjctNGM1YS1iY2UxLTFiYzg5NmFhOGYwNSIsImF1ZCI6Imh0dHBzOi8vaWRlbnRpdHl0b29sa2l0Lmdvb2dsZWFwaXMuY29tL2dvb2dsZS5pZGVudGl0eS5pZGVudGl0eXRvb2xraXQudjEuSWRlbnRpdHlUb29sa2l0IiwibmFtZSI6InRlc3RfNjY3MTY4NTk0NyIsInV1aWQiOiJlYzg5MjhmZi0wYmYxLTQ3NzMtOTYwMC0xYjE0MmI0MDk1MDAiLCJyb2xlIjoxMDAsImR1bnpvX2tleSI6ImNmMDU2M2UzLTAzMjYtNDVkNC1iNTZlLTlhN2QzMzZmOTljMiIsImV4cCI6MTc3MzQ2MjU1OCwidiI6MCwiaWF0IjoxNjE3OTQyNTU4LCJzZWNyZXRfa2V5IjoiZDc3MTMwNTEtYWRiMi00NTNiLWE3ODktZjY2YzY3NjJkOWQxIn0.4gLpii3cTz9RWl2UzTEpg1oVXvQ3I6hfQ6YczL0VqBQ';


    private $count =1;
    public function execute(EventObserver $observer)
    {

       if($this->count==1){

        $quote = $observer->getEvent()->getQuote();
        $grand_total = $observer->getEvent()->getQuote()->getGrandTotal();

        $shippingAddress = $observer->getEvent()->getQuote()->getShippingAddress()->getData();

        // $this->_logger->debug('-shipping Address Details-',array (json_encode($shippingAddress)));

        

        // $this->_logger->debug('-total grand amount-',array (json_decode($grand_total)));

       


        // $httpHeaders = new \Zend\Http\Headers();
        // $httpHeaders->addHeaders([
        //   'Accept' => 'application/json',
        //   'Content-Type' => 'application/json',
        //   'client-id' => $this->clientId,
        //   'Authorization'=>$this->Authorization
        // ]);


        // $request = new \Zend\Http\Request();
        // $request->setHeaders($httpHeaders);
        // $request->setUri('https://apis-staging.dunzo.in/api/v1/quote');
        // $request->setMethod(\Zend\Http\Request::METHOD_GET);


        // $params = new \Zend\Stdlib\Parameters([
        //     'pickup_lat'=>12.9063,
        //     'pickup_lng'=>77.5904,
        //     'drop_lat'=>12.9198,
        //     'drop_lng'=>77.5777,
        //     'category_id'=>"pickup_drop"
        // ]);

        // $request->setQuery($params);
        // $client = new \Zend\Http\Client();
        // $response = $client->send($request);
        // // $this->logger->debug('quote',array ($response));

        // $responseobject[] = json_decode($response->getBody(), true);
        // $this->_logger->debug('quote create details',array ($responseobject));
    
        // $deliveryCharge = $responseobject[0]['estimated_price'];

        // $this->_logger->debug('delivery charge',array ($deliveryCharge));


        // $deliveryFees = 100;

        $deliveryFees = $this->getDeliveryAmount();
        $new_grand_total = $grand_total + $deliveryFees;

        // $this->_logger->debug('-new grand amount-',array (json_decode($new_grand_total)));

        // $new_grand_total = $grand_total + $deliveryCharge; // Adding dunzo delivery charges

        $quote->setGrandTotal($new_grand_total);

        $quote->collectTotals()->save();


        $this->count =$this->count + 1;



        }
    }


    public function getDeliveryAmount()
    {

        $httpHeaders = new \Zend\Http\Headers();
        $httpHeaders->addHeaders([
          'Accept' => 'application/json',
          'Content-Type' => 'application/json',
          'client-id' => $this->clientId,
          'Authorization'=>$this->Authorization
        ]);


        $request = new \Zend\Http\Request();
        $request->setHeaders($httpHeaders);
        $request->setUri('https://apis-staging.dunzo.in/api/v1/quote');
        $request->setMethod(\Zend\Http\Request::METHOD_GET);


        $params = new \Zend\Stdlib\Parameters([
            'pickup_lat'=>12.9063,
            'pickup_lng'=>77.5904,
            'drop_lat'=>12.9198,
            'drop_lng'=>77.5777,
            'category_id'=>"pickup_drop"
        ]);

        $request->setQuery($params);
        $client = new \Zend\Http\Client();
        $response = $client->send($request);
        // $this->logger->debug('quote',array ($response));

        $responseobject[] = json_decode($response->getBody(), true);
        // $this->_logger->debug('quote create details',array ($responseobject));
    
        $deliveryCharge = $responseobject[0]['estimated_price'];

        // $this->_logger->debug('delivery charge',array ($deliveryCharge));

        return $deliveryCharge;

    }
}