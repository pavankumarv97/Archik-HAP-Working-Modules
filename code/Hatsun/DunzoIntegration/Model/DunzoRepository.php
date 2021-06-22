<?php


namespace Hatsun\DunzoIntegration\Model;

use Hatsun\DunzoIntegration\Api\DunzoRepositoryInterface;
use Magento\Framework\HTTP\ZendClientFactory;
use Hatsun\DunzoIntegration\Model\ResourceModel\Dunzo\CollectionFactory;



class DunzoRepository implements DunzoRepositoryInterface
{

    private $httpClientFactory;
    private $logger;
    private $collectionFactory;

    public function __construct(ZendClientFactory $httpClientFactory,
    \Psr\Log\LoggerInterface $logger,
    CollectionFactory $collectionFactory
    )
    {
        $this->httpClientFactory = $httpClientFactory;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
    }


    // prod Keys
    //clientID =  ba4b7e90-1969-4fe5-b84c-afd0b99ba6ab
    //clientSecret = 35435ece-855e-4a32-a858-53834414bcee

    // test keys

    //clientId c2936f85-bc47-4504-a7eb-3c5105a2c423    
    //clientSecret 8749fcae-8ee8-4fc3-a401-a60acef5778b

    // development
    // private $clientId = 'c2936f85-bc47-4504-a7eb-3c5105a2c423';
    // private $clientSecret = '8749fcae-8ee8-4fc3-a401-a60acef5778b';
    // private $Authorization = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkIjp7InJvbGUiOjEwMCwidWlkIjoiN2EyNTFiOTAtMzY2Ni00YTE0LWE1NzktNWIzMTY0MzUwMTAxIn0sIm1lcmNoYW50X3R5cGUiOm51bGwsImNsaWVudF9pZCI6ImJhNGI3ZTkwLTE5NjktNGZlNS1iODRjLWFmZDBiOTliYTZhYiIsImF1ZCI6Imh0dHBzOi8vaWRlbnRpdHl0b29sa2l0Lmdvb2dsZWFwaXMuY29tL2dvb2dsZS5pZGVudGl0eS5pZGVudGl0eXRvb2xraXQudjEuSWRlbnRpdHlUb29sa2l0IiwibmFtZSI6IkhhdHN1biBBZ3JvIFByb2R1Y3QgTHRkIiwidXVpZCI6IjdhMjUxYjkwLTM2NjYtNGExNC1hNTc5LTViMzE2NDM1MDEwMSIsInJvbGUiOjEwMCwiZHVuem9fa2V5IjoiYzJhMTFkN2UtZjdlNi00MzIyLWExZjQtYWJiZjUxYzM5YTIyIiwiZXhwIjoxNzc5NDI3NDE3LCJ2IjowLCJpYXQiOjE2MjM5MDc0MTcsInNlY3JldF9rZXkiOiI0MDVjYWI4MC1lZDk5LTQ0N2ItYTMwMy01MDJlNDQwODJkYTgifQ.y5YVaMY4wFxUDj1eBs1F3N1KP7MKPUw53e448RuJFaM';
    // private $dunzoUrl = 'https://apis-staging.dunzo.in/api/v1/';

    // production
    private $clientId = "ba4b7e90-1969-4fe5-b84c-afd0b99ba6ab";
    private $clientSecret = "35435ece-855e-4a32-a858-53834414bcee";
    private $Authorization = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkIjp7InJvbGUiOjEwMCwidWlkIjoiN2EyNTFiOTAtMzY2Ni00YTE0LWE1NzktNWIzMTY0MzUwMTAxIn0sIm1lcmNoYW50X3R5cGUiOm51bGwsImNsaWVudF9pZCI6ImJhNGI3ZTkwLTE5NjktNGZlNS1iODRjLWFmZDBiOTliYTZhYiIsImF1ZCI6Imh0dHBzOi8vaWRlbnRpdHl0b29sa2l0Lmdvb2dsZWFwaXMuY29tL2dvb2dsZS5pZGVudGl0eS5pZGVudGl0eXRvb2xraXQudjEuSWRlbnRpdHlUb29sa2l0IiwibmFtZSI6IkhhdHN1biBBZ3JvIFByb2R1Y3QgTHRkIiwidXVpZCI6IjdhMjUxYjkwLTM2NjYtNGExNC1hNTc5LTViMzE2NDM1MDEwMSIsInJvbGUiOjEwMCwiZHVuem9fa2V5IjoiYzJhMTFkN2UtZjdlNi00MzIyLWExZjQtYWJiZjUxYzM5YTIyIiwiZXhwIjoxNzc5NDc5MDI3LCJ2IjowLCJpYXQiOjE2MjM5NTkwMjcsInNlY3JldF9rZXkiOiI0MDVjYWI4MC1lZDk5LTQ0N2ItYTMwMy01MDJlNDQwODJkYTgifQ.K3_Uc9G8jTj3mnE3bDy1JvC4ZRBRfKfUwk-PVMsZkyM';

    private $dunzoUrl = 'https://api.dunzo.in/api/v1/';
 


    public function getToken()
    {
        
        $httpHeaders = new \Zend\Http\Headers();
        $httpHeaders->addHeaders([
          'Accept' => 'application/json',
          'Content-Type' => 'application/json',
          'client-id' => $this->clientId,
          'client-secret' => $this->clientSecret
        ]);
        $request = new \Zend\Http\Request();
        $request->setHeaders($httpHeaders);
        $request->setUri($this->dunzoUrl.'token');
        $request->setMethod(\Zend\Http\Request::METHOD_GET);
        $client = new \Zend\Http\Client();
        $response = $client->send($request);
        $responseobject[] = json_decode($response->getBody(), true);
        $this->logger->debug('token',array ($response));
        return $responseobject;
    }



    public function getQuote($object)
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
        $request->setUri($this->dunzoUrl.'quote');
        $request->setMethod(\Zend\Http\Request::METHOD_GET);
        $params = new \Zend\Stdlib\Parameters([
            'pickup_lat'=>$object['pickup_lat'],
            'pickup_lng'=>$object['pickup_lng'],
            'drop_lat'=>$object['drop_lat'],
            'drop_lng'=>$object['drop_lng'],
            'category_id'=>$object['category_id']
        ]);
       
        $request->setQuery($params);
        $client = new \Zend\Http\Client();
        $response = $client->send($request);
        $responseobject[] = json_decode($response->getBody(), true);
        return $responseobject;

    }


    public function createTasks($object){
		$ch = curl_init($this->dunzoUrl."tasks");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($object));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "client-id: ".$this->clientId , "Authorization: ".$this->Authorization));
		$response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $response;
        die();
    }


    public function getStatus($task_id)
    {
        if(isset($task_id)&&!is_null($task_id)){
           $ch = curl_init($this->dunzoUrl."tasks/".$task_id."/status");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "client-id: " .$this->clientId , "Authorization: ".$this->Authorization));
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $responsenew[] = json_decode($response, true);
            return $responsenew;
            die(); 
        }else{
            return "Please Check the Id Again";
        }
        

    // return $response;

    }

    public function cancel($task_id , $cancellation)
    {
        $ch = curl_init($this->dunzoUrl."tasks/".$task_id."/_cancel");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($cancellation));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "client-id: " .$this->clientId , "Authorization: ".$this->Authorization));
		$response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $response;
    }


    public function getTrackingId($orderId)
    {
        if(isset($orderId)){
            $collection = $this->collectionFactory->create()->addFieldToSelect('*')->addFieldToFilter("order_id", $orderId);    
            $dunzoData = $collection->getData();   
            if(isset($dunzoData)){
                return $collection->getData();
            }else{
                return "Sorry No Data Found!";
            }
        }
        
    }


    /**
     * @param mixed $params
     * @return mixed
     */
    public function grandtotal($params){
        return 'hi';
    }

}