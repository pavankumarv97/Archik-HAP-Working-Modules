<?php

namespace Chopserve\SocialLogin\Model;

use Chopserve\SocialLogin\Api\SocialLoginRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Chopserve\SocialLogin\Model\ResourceModel\Apiauth\CollectionFactory ;
use Hatsun\CustomRazorpay\Model\ResourceModel\CustomeRazorpayPayment\CollectionFactory as RazorpayCollectionFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
// use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Customer;
use Magento\Sales\Model\Order;
use Magento\Customer\Model\ResourceModel\Customer\Relation as Resource;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote\Address\ItemFactory as QuoteModelFactory;
use Hatsun\DunzoIntegration\Model\DunzoRepository as DunzoRepository;
use Hatsun\CustomRazorpay\Model\CustomeRazorpayPaymentRepository as CustomRazorpayRepository;

class SocialLoginRepository implements SocialLoginRepositoryInterface
{
    private $_objectManager = null;
    private $messageManager;
    private $request;
    private $apiauthFactory;
    private $_socialloginFactory;
    protected $razorpayCollectionFactory;
    protected $transportBuilder;
    protected $storeManager;
    protected $inlineTranslation;
    protected $_scopeConfig;
    protected $customer;
    protected $_logger; 
    protected $_dunzoFactory;
    protected $cus;
    protected $resource;
    protected $quoteFactory;
    protected $quoteModelFactory;
    protected $dunzoRepository;
    protected $customRazorpayRepository;
    /**
     * Initialize dependencies.
     *
    * @param \Magento\Framework\Webapi\Rest\Response $response
    * @param \Magento\Framework\App\Config\ScopeConfigInterface scopeConfig
    * @param Magento\Framework\App\Helper\Context $context
    * @param Magento\Store\Model\StoreManagerInterface $storeManager
    * @param Magento\Catalog\Model\Product $product
    * @param Magento\Framework\Data\Form\FormKey $formKey $formkey,
    * @param Magento\Quote\Model\Quote $quote,
    * @param Magento\Customer\Model\CustomerFactory $customerFactory,
    * @param Magento\Sales\Model\Service\OrderService $orderService,
    */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        CollectionFactory $apiCollectionFactory,
        \Magento\Framework\Webapi\Rest\Response $response,
        \Magento\Framework\Webapi\Rest\Request $request,
        \Chopserve\SocialLogin\Model\ResourceModel\ItemFactory $SocialLoginFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Data\Form\FormKey $formkey,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService , 
        \Magento\Integration\Model\Oauth\TokenFactory $tokenModelFactory,
        RazorpayCollectionFactory $razorpayCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StateInterface $state,
        Customer $customer,
        Order $order,
        \Psr\Log\LoggerInterface $logger,
        \Hatsun\DunzoIntegration\Model\DunzoFactory $dunzoFactory,
        \Magento\Customer\Model\Data\Customer $cus,
        Resource $resource,
        QuoteFactory $quoteFactory,
        QuoteModelFactory $quoteModelFactory,
        DunzoRepository $dunzoRepository,
        CustomRazorpayRepository $customRazorpayRepository
    ) {
        $this->_objectManager = $objectManager;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->response = $response;
        $this->apiauthFactory = $apiCollectionFactory;
        $this->_socialloginFactory = $SocialLoginFactory;
        $this->_storeManager = $storeManager;
        $this->_product = $product;
        $this->_formkey = $formkey;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
        $this->_tokenModelFactory = $tokenModelFactory;
        $this->razorpayCollectionFactory = $razorpayCollectionFactory;
        $this->transportBuilder = $transportBuilder;
       $this->storeManager = $storeManager;
       $this->inlineTranslation = $state;
        $this->_scopeConfig = $scopeConfig;
        $this->customer = $customer;
       $this->order = $order;
       $this->_logger = $logger;
       $this->_dunzoFactory = $dunzoFactory;
       $this->cus = $cus;
       $this->resource = $resource;
       $this->quoteFactory = $quoteFactory;
       $this->quoteModelFactory = $quoteModelFactory;
       $this->dunzoRepository = $dunzoRepository;
        $this->customRazorpayRepository = $customRazorpayRepository;
    }

    private  $username = "rzp_test_RKxG1LoLkdTy0s";
    private $password = "53siy3KRQD3oRo5ni3uwouQJ";


    /**
     * @param string $platform
     * @return string
     */
    public function getList($platform)
    {
        $collection = $this->apiauthFactory->create();
        $collection->addFieldToSelect('*');
        $a = 1;
        try {
            if($platform == "all"){
                $resultArr['status'] = 1;
                $resultArr['message'] = "Found";
                $contactModel = $this->_objectManager->create('Chopserve\SocialLogin\Model\Apiauth');
                $collection = $contactModel->getCollection();
                $i = 0;
                foreach($collection as $contact) {
                    $resultArr[$i]['auth_id'] = $contact->getAuthId();
                    $resultArr[$i++]['platform'] = $contact->getPlatform();
//                    var_dump($contact->getData());
                }
            }else {
                $resultArr['status'] = 0;
                $resultArr['message'] = "Error";
            }
        }catch(\Magento\Framework\Exception\LocalizedException $e){
            $this->messageManager->addError(
                $e,
                __('invalid', $e->getMessage())
            );
            $resultArr['status'] = 0;
            $resultArr['message'] = "Failed!!";
        }
        echo json_encode($resultArr);
        die();
    }


    /**
     * @param mixed $params
     */
    public function userinfo($params){
        try{
            $userModel = $this->_objectManager->create('Chopserve\SocialLogin\Model\Item');
            $userModel->load($params['email'],'email');
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customerSession = $objectManager->create('Magento\Customer\Model\Session');
            $customerToken = $this->_tokenModelFactory->create();
            $customer = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create(); 
            $customer->setWebsiteId(1);
            $customer->loadByEmail($params['email']);
            if($userModel->getData() && $customer->getData()){
                $userModel->setFirstName($params['firstname']);
                $userModel->setLastName($params['lastname']);
                $userModel->setPlatformAuthId($params['auth_id']);
                $userModel->setEmail($params['email']);
                $userModel->setEmailVerified($params['email_verified']);
                $userModel->setPhoneNumber($params['phone_number']);
                $userModel->setImgUrl($params['img_url']);                
                $userId = $customer->getId();
                $customer->setWebsiteId(1); 
                $customer->setEmail($params['email']);
                $customer->setFirstname($params['firstname']);
                $customer->setPhoneNumber($params['phone_number']);
                $customer->setLastname($params['lastname']);
                $customer->setPassword("testuser");
                $userModel->save();
                $customer->save();
                $token = $this->getCustomerToken($params['email'],"testuser");
                $customerSession->setCustomerId($customer->getId());
                $resultArr['status'] = 1;
                $resultArr['customer_id'] = $customer->getId();
                $resultArr['cname'] = $customer->getFirstname()." ".$customer->getLastname(); 
                $resultArr['cust_token'] = $token;
                $resultArr['mobile'] = $customer->getPhoneNumber();
                $resultArr['session_id'] =  $customerSession->getCustomerId();
                $resultArr['msg']='Logged In Successfully!!';
            }else{
                $userModel->setFirstName($params['firstname']);
                $userModel->setLastName($params['lastname']);
                $userModel->setPlatformAuthId($params['auth_id']);
                $userModel->setEmail($params['email']);
                $userModel->setEmailVerified($params['email_verified']);
                $userModel->setPhoneNumber($params['phone_number']);
                $userModel->setImgUrl($params['img_url']);
                $customer = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();   
                $customer->setWebsiteId(1); 
                $customer->setEmail($params['email']);
                $customer->setFirstname($params['firstname']);
                $customer->setPhoneNumber($params['phone_number']);
                $customer->setLastname($params['lastname']);
                $customer->setPassword("testuser");
                $userModel->save();
                $customer->save();
                $token = $this->getCustomerToken($params['email'],"testuser");
                $customerSession->setCustomerId($customer->getId());
                $resultArr['status'] = 1;
                $resultArr['customer_id'] = $customer->getId();
                $resultArr['cname'] = $customer->getFirstname()." ".$customer->getLastname(); 
                $resultArr['cust_token'] = $token;
                $resultArr['session_id'] =  $customerSession->getCustomerId();
                $resultArr['msg']='Logged In Successfully!!';
            }
        }catch(Exception $e){
            throw new FrameworkException($e->getMessage());
            $resultArr['status'] = 0;
            $resultArr['message'] = "Unable to Login!";
        }
        echo json_encode($resultArr);
        die();
    }

    

    /**
     * @param mixed $params
     * @return mixed
     */
    public function guestcheckout($params){
        $store=$this->_storeManager->getStore();
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $customer=$this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($params['email']);// load customet by email address
        if(!$customer->getEntityId()){
            //If not avilable then create this customer 
            $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($params['shipping_address']['firstname'])
                    ->setLastname($params['shipping_address']['lastname'])
                    ->setEmail($params['email'])
                    ->setPhoneNumber($params['mobile'])
                    ->setPassword($params['email'])
                    ->setIsVerified(false);//verified customer while creating                
            $customer->save();
            $customerId = $customer->getId();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $addresss = $objectManager->get('\Magento\Customer\Model\AddressFactory');
            $address = $addresss->create();
            $address->setCustomerId($customerId)
                ->setFirstname($params['shipping_address']['firstname'])
                ->setLastname($params['shipping_address']['lastname'])
                ->setCountryId($params['shipping_address']['country_id'])
                ->setPostcode($params['shipping_address']['postcode'])
                ->setCity($params['shipping_address']['city'])
                ->setTelephone($params['shipping_address']['telephone'])
                ->setStreet($params['shipping_address']['street'])
		// ->setRegionCode($params['shipping_address'][region]['region_code'])
		->setRegion($params['shipping_address'][region]['region'])
                ->setRegionId($params['shipping_address'][region]['region_id']);
                foreach($params['shipping_address']['custom_attributes'] as $att){
                    if($att['attribute_code'] == 'latitude'){
                        $address->setLatitude($att['value']);
                    }
                    if($att['attribute_code'] == 'longitude'){
                        $address->setLongitude($att['value']);
                    }
                    if($att['attribute_code'] == 'address_label'){
                        $address->setAddressLabel($att['value']);
                    }
                }
		$address->setAddressLabel($params['shipping_address']['value']);
            // ->setLatitude($params['shipping_address']['latitude'])
            // ->setLongitude($params['shipping_address']['longitude'])
            // ->setShippingMethod('freeshipping_freeshipping')
            // >setIsDefaultShipping('1')
            // ->setIsDefaultBilling('1');
            $address->save();//saving the customer address with latitude and longitude   
        }        
        $quote=$this->quote->create(); //Create object of quote
        $quote->setStore($store); //set store for which you create quote
        // if you have allready buyer id then you can load customer directly 
        $customer= $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();
        $quote->assignCustomer($customer); //Assign quote to customer
        //add items in quote
        foreach($params['items'] as $item){	
            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productData = $_objectManager->get('Magento\Quote\Model\Quote\Item')->load($item['item_id']);
            $pid = $productData->getProductId();
            $product = $this->_product->load($pid);
            if($product){
                $product->setSku($item['sku']);
                $product->setName($item['name']);
                $product->setPrice($item['price']);
                $product->setProductType($item['product_type']);
                $product->setQuoteId($item['quote_id']);
                $quote->addProduct(
                    $product,
                    intval($item['qty'])
                );
            }else{
                $result=['error'=>1,'msg'=>'wrong product'];die();
            }
        }
        // $customer=$this->customerFactory->create();
        // $customer->setWebsiteId($websiteId);
        // $customer->loadByEmail($params['email']);
        // $this->logger->debug('customer id',array ($customer->getId()));
        // $addresses = $customer->getAddresses();
        // $quote->getBillingAddress()->addData($addresses);
        // $quote->getShippingAddress()->addData($addresses);
        //Set Address to quote
        $quote->getBillingAddress()->addData($params['shipping_address']);
        $quote->getShippingAddress()->addData($params['shipping_address']);
        // Collect Rates and Set Shipping & Payment Method
        $shippingAddress=$quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
                        ->collectShippingRates()
                        ->setShippingMethod('customshipping');
                 //shipping method

        $shippingAddress->save();
        print_r($shippingAddress);
        $quote->setPaymentMethod('razorpay'); //payment method
        $quote->setInventoryProcessed(false); //not effetc inventory
        $quote->save(); //Now Save quote and your quote is ready

        // // Set Sales Order Payment
        // $quote->getPayment()->importData(['method' => 'razorpay']);
        // // Collect Totals & Save Quote
        // $quote->collectTotals()->save();
        // // Create Order From Quote
        // $order = $this->quoteManagement->submit($quote);
        // $order->setEmailSent(0);
        // $increment_id = $order->getRealOrderId();
        // if($order->getEntityId()){
        //     $result['order_id']= $order->getRealOrderId();
        // }else{
        //     $result=['error'=>1,'msg'=>'Your custom message'];
        // }
        // return $result;
    }

    /**
     * @param mixed $params
     * @return mixed
     */
    public function checkmail($params){
        $store=$this->_storeManager->getStore();
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $customer=$this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($params['email']);
        if(count($customer->getData())>0){
            if($customer->getPhoneNumber()){
                $resultArr['status'] = 1;
                $resultArr['msg'] = "Phone Number Exists";
                $resultArr['mobile'] = $customer->getPhoneNumber();
            }else{
                $resultArr['status'] = 0;
                $resultArr['msg'] = "Phone Number Does Not Exists";
                $resultArr['mobile'] = $customer->getPhoneNumber();
            }           
        }else{
            $resultArr['status'] = 0;
            $resultArr['msg'] = "Invalid Customer";
        }
        return [$resultArr];
    }
    
    /**
     * @param string $username
     * @param string $password
     * @return string
     */
    public function getCustomerToken($username, $password){
        $userData = ["username" => $username, "password" => $password];
        $ch = curl_init("https://shopadmin.hapdaily.com/hatsun.prod/rest/V1/integration/customer/token");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

        $token = curl_exec($ch);
        return $token;
    }








    /**
     * @param string $source_code
     * @return mixed
     */
    public function getStoresInfo($source_code){

         $httpHeaders = new \Zend\Http\Headers();
        $httpHeaders->addHeaders([
          'Accept' => 'application/json',
          'Content-Type' => 'application/json',
          'Authorization'=>"Bearer nmii5me8o62cje2ku7q0q1ojb6ixh04u"
        ]);


        $request = new \Zend\Http\Request();
        $request->setHeaders($httpHeaders);
        $request->setUri('https://shopadmin.hapdaily.com/hatsun.prod/rest/V1/inventory/sources');
        $request->setMethod(\Zend\Http\Request::METHOD_GET);


        $params = new \Zend\Stdlib\Parameters([
            'source_code'=>$source_code
        ]);

        $request->setQuery($params);
        $client = new \Zend\Http\Client();
        $response = $client->send($request);
        $responseobject[] = json_decode($response->getBody(), true);
        // $this->_logger->debug('response',array($responseobject));
        // $deliveryCharge = $responseobject[0]['estimated_price'];
        return $responseobject;

    }



    /**
     * @param string $source_code
     * @return mixed
     */
    public function getSourceItems($source_code){
        $httpHeaders = new \Zend\Http\Headers();
        $httpHeaders->addHeaders([
          'Accept' => 'application/json',
          'Content-Type' => 'application/json',
          'Authorization'=>"Bearer nmii5me8o62cje2ku7q0q1ojb6ixh04u"
        ]);


        $request = new \Zend\Http\Request();
        $request->setHeaders($httpHeaders);
        $request->setUri('https://shopadmin.hapdaily.com/hatsun.prod/rest/V1/inventory/source-items');
        $request->setMethod(\Zend\Http\Request::METHOD_GET);


        $params = new \Zend\Stdlib\Parameters([
            'searchCriteria[filter_groups][0][filters][0][field]' => 'source_code',
            'searchCriteria[filter_groups][0][filters][0][value]' => $source_code,
            'searchCriteria[filter_groups][0][filters][0][condition_type]' => 'eq'
        ]);

        $request->setQuery($params);
        $client = new \Zend\Http\Client();
        $response = $client->send($request);
        $responseobject[] = json_decode($response->getBody(), true);
        // $this->_logger->debug('response',array($responseobject));
        // $deliveryCharge = $responseobject[0]['estimated_price'];
        return $responseobject;

    }

    /**
     * @param string $source_code
     * @param string $category_id
     * @return mixed
     */   
    public function getStoreProducts($source_code,$category_id){
        $productsList = $this->getSourceItems($source_code);
        // print_r($productsList);
        $prodskulist = [];
        foreach ($productsList[0]['items'] as $plist) {
            array_push($prodskulist, $plist['sku']);
            $skulist = implode(',',$prodskulist);
        }
        $httpHeaders = new \Zend\Http\Headers();
        $httpHeaders->addHeaders([
          'Accept' => 'application/json',
          'Content-Type' => 'application/json',
          'Authorization'=>"Bearer nmii5me8o62cje2ku7q0q1ojb6ixh04u"
        ]);
        $request = new \Zend\Http\Request();
        $request->setHeaders($httpHeaders);
        $request->setUri('https://shopadmin.hapdaily.com/hatsun.prod/rest/V1/products');
        $request->setMethod(\Zend\Http\Request::METHOD_GET);

        if(isset($skulist)){
             $params = new \Zend\Stdlib\Parameters([
                'searchCriteria[filterGroups][0][filters][0][field]' => 'category_id',
                'searchCriteria[filterGroups][0][filters][0][value]' => $category_id,
                'searchCriteria[filterGroups][0][filters][0][conditionType]' => 'eq',
                'searchCriteria[filterGroups][0][filters][1][field]' => 'sku',
                'searchCriteria[filterGroups][0][filters][1][value]' =>  $skulist,
                'searchCriteria[filterGroups][0][filters][1][conditionType]' => 'in'
            ]);
        }else{
             $params = new \Zend\Stdlib\Parameters([
                'searchCriteria[filterGroups][0][filters][0][field]' => 'category_id',
                'searchCriteria[filterGroups][0][filters][0][value]' => $category_id,
                'searchCriteria[filterGroups][0][filters][0][conditionType]' => 'eq',
                 'searchCriteria[filterGroups][0][filters][1][field]' => 'sku',
                'searchCriteria[filterGroups][0][filters][1][value]' =>  '',
                'searchCriteria[filterGroups][0][filters][1][conditionType]' => 'in'
            ]);
        }
        $request->setQuery($params);
        $client = new \Zend\Http\Client();
        $response = $client->send($request);
        $responseobject[] = json_decode($response->getBody(), true);
        echo json_encode($responseobject[0]);
        die();
    }


    /**
     * @param mixed $params
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function createGuestCustomer($params){
       
        try{
            $store = $this->_storeManager->getStore();
            $storeId = $store->getStoreId();
            // $websiteId = $this->storeManager->getStore()->getWebsiteId();
            $websiteId = 1;
            // $customer = $this->customerFactory->create();
            // $customer->setWebsiteId($websiteId);
            // // $this->_logger->debug('-checking website id-',array (json_encode($websiteId)));
            // $customer->loadByEmail($params['email']);// load customer by email to check if customer is availalbe or not
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customer = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create(); 
            $customer->setWebsiteId(1);
            $customer->loadByEmail($params['email']);
            if(!$customer->getId()){
                $customer->setWebsiteId($websiteId)
                        ->setStore($store)
                        ->setFirstname($params['firstname'])
                        ->setLastname($params['lastname'])
                        ->setPhoneNumber($params['phone_number'])
                        ->setEmail($params['email'])
                        ->setPassword("testuser")
                        ->setIsGuest(1)
                        ->setIsVerified(1);               
                    $customer->save();                   
                $resultArr['status'] = 1;
                $resultArr['message'] = "Your Account created successfully!";
            }else{
                $customer->setId($customer->getId());
                $customer->setWebsiteId($websiteId)
                        ->setStore($store)
                        ->setFirstname($params['firstname'])
                        ->setLastname($params['lastname'])
                        ->setPhoneNumber($params['phone_number'])
                        ->setEmail($params['email'])
                        ->setPassword("testuser")
                        ->setIsGuest(1)
                        ->setIsVerified(1);               
                    $customer->save();      
                // $resultArr['customer'] = $customer->getData();
                $resultArr['status'] = 1;
                $resultArr['message'] = "Customer with same email id already exists!";
                // throw new NoSuchEntityException(__('Customer with same email id already exists!'));
            }            
        }catch(Exception $e){
             throw new FrameworkException($e->getMessage());
            $resultArr['status'] = 0;
            $resultArr['message'] = "Unable to Register!";
            throw new NoSuchEntityException(__('Unable to Register!'));
        }
        echo json_encode($resultArr);
        die();        
    }

    /**
     * @param string $source_code
     * @param string $order_id
     * @return mixed
     */    
    public function setSource($source_code,$order_id){
       
        if(isset($source_code) && isset($order_id)){
            $dunzo_data = $this->_dunzoFactory->create();
            $order_id_data = $dunzo_data->getCollection()->addFieldToSelect('*')->addFieldToFilter("order_id", $order_id);
            $dunzo_info_data = $order_id_data->getData();

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orderInterface = $objectManager->create('Magento\Sales\Api\Data\OrderInterface'); 
            $order = $orderInterface->load($order_id);         
            

            if(isset($dunzo_info_data) && count($dunzo_info_data) > 0){
                 $tracking_id =  $dunzo_info_data[0]['tracking_id'];
                 if($order->getId()){
                    $order->setSourceCode($source_code);
                    // $order->setSourceCode('600091');
                    $order->save();
                    $connection = $this->_objectManager->create('\Magento\Framework\App\ResourceConnection');
                    $conn = $connection->getConnection();
                    // $source_code = "600091";
                    $query = "update HATSUN_SHOP.sales_order_grid set source_code = '".$source_code."' where entity_id =".$order_id;
                    $data = $conn->query($query);
                    $resultArr['tracking_id'] = $tracking_id;
                    $resultArr['msg'] = 'Your Data updated Succesfully!';
                    $resultArr['status'] = 1;
                }else{
                        $resultArr['msg'] = 'Order does not exists!';
                        $resultArr['status'] = 0;
                }
            }else{
                if($order->getId()){
                    $order->setSourceCode($source_code);
                    // $order->setSourceCode('600091');
                    $order->save();
                    $connection = $this->_objectManager->create('\Magento\Framework\App\ResourceConnection');
                    $conn = $connection->getConnection();
                    // $source_code = "600091";
                    $query = "update HATSUN_SHOP.sales_order_grid set source_code = '".$source_code."' where entity_id =".$order_id;
                    $data = $conn->query($query);
                    // $resultArr['tracking_id'] = $tracking_id;
                    $resultArr['msg'] = 'Your Data updated Succesfully!';
                    $resultArr['status'] = 1;
                }else{
                        $resultArr['msg'] = 'Order does not exists!';
                        $resultArr['status'] = 0;
                }
            }

        }else{
            $resultArr['msg'] = 'Invalid parameters!';
            $resultArr['status'] = 0;
        }
        echo json_encode($resultArr);
        die();
    }

        /**
     * @param string $customer_id
     * @param string $fcm_key
     * @return mixed
     */    
    public function savefcm($customer_id,$fcm_key){

        $sourceCollection = [
            array(
            "source_code" => "600014",
            "name" => "J.K Agency",
            "enabled" => "1",
            "description" => null,
            "latitude" => 12.964799,
            "longitude" => 80.195023,
            "country_id" => "IN",
            "region_id" => "563",
            "region" => "Tamil Nadu",
            "city" => "ROYAPETTAH",
            "street" => "NO 50,GOWDIYA MUTT ROAD,NEAR PONNUSAMY HOTEL, ROYAPETTAH",
            "postcode" => "600014",
            "contact_name" => null,
            "email" => "srisheshprabhu@gmail.com",
            "phone" => "9884435893",
            "fax" => null,
            "use_default_carrier_config" => "1",
            "account_id" => "acc_H7kr6yMbynaKyU"
        )
        ];

    $shippingAddress = [
        "firstname" => "pavan",
        "telephone" => "8217019247",
        "street" => "KA sdhjsd",
        "region" => "chennai",
        "city" => "chennai",
        "postcode" => "600015",
        "country_id" => "IN"
    ];

    // var_dump($sourceCollection[0]['latitude']);
    $sender_details = array(
        "name"=>  $sourceCollection[0]['name'],
        "phone_number"=> $sourceCollection[0]['phone']
    );                  
    $receiver_details = array(
        "name" => $shippingAddress['firstname'],
        "phone_number"=> $shippingAddress['telephone']
    );
    $pickup_details = array(
        "lat"=> (float)$sourceCollection[0]['latitude'],
        "lng"=> (float)$sourceCollection[0]['longitude'],
        "address"=> array(
            "apartment_address" => $sourceCollection[0]['contact_name'],
            "street_address_1" => $sourceCollection[0]['street'],
            "landmark"=>  $sourceCollection[0]['street'],
            "city" =>  $sourceCollection[0]['city'],
            "state"=> $sourceCollection[0]['region'],
            "pincode"=> $sourceCollection[0]['postcode'],
            "country"=> $sourceCollection[0]['country_id']
        )
    );
    $drop_details = array(
        "lat"=> 12.9628964,
        "lng"=> 80.2096632,
        "address"=> array(
            "apartment_address" => $shippingAddress['firstname'],
            "street_address_1"=> $shippingAddress['street'],
            "landmark"=> $shippingAddress['street'],
            "city"=> $shippingAddress['city'],
            "state"=>$shippingAddress['region'],
            "pincode"=> $shippingAddress['postcode'],
            "country"=> $shippingAddress['country_id']
        )
    );
    $request_id = md5(uniqid($shippingAddress['telephone'], true));
$package_content =   ["Documents | Books", "Clothes | Accessories", "Electronic Items"];
$package_approx_value = 200;
$special_instructions = "Fragile items. Handle with great care!!";
    $grandTotal = 200;          
    $shipType = "standard";
    // $paymentMethod = $orderData->getPayment()->getMethod();
    $data['request_id'] = $request_id;
    $data['pickup_details'] = $pickup_details;
    $data['drop_details'] = $drop_details;
    $data['sender_details'] = $sender_details;
    $data['receiver_details'] = $receiver_details;
    $data['package_content'] = $package_content;
    $data['package_approx_value'] = $package_approx_value;
    $data['special_instructions'] = $special_instructions;                  
    $dataInfo = $data;
    $dunzoTask = $this->dunzoRepository->createTasks($dataInfo);

    print_r($dunzoTask);









        // $object = array (
        // "request_type" => "tranferviapayment",
        //     "rzp_payment_id"=> "pay_HPHyqNqNE2lO0t",                                             
        //     "franchise_amount" => 20,
        //     "source_acc_id" =>  "acc_H7kr6yMbynaKyU",
        //     "source_acc_name" =>  "J K Agencies Royapettah",
        //     "dunzo_amount" => 30
        // );
        // $razorpayData = $this->customRazorpayRepository->razorpayApis($object);
        // print_r($razorpayData);

        // $connection = $this->_objectManager->create('\Magento\Framework\App\ResourceConnection');
        // $conn = $connection->getConnection();
        // // $source_code = "600091";
        // // $query = "update HATSUN_SHOP.sales_order_grid set source_code = '".$source_code."' where entity_id =".$order_id;
        // $query = "select * from HATSUN_SHOP.quote_address where quote_id = 1095";
        // $data = $conn->query($query);
        // foreach ($data as $key => $value) {
        //    print_r($value);
        // }
       





        // $resultPage = $this->quoteModelFactory->create();
        // $collection = $resultPage->getCollection(); //Get Collection of module data
        // var_dump($collection->getData());
        // exit;

        // $dunzo_data = $this->_dunzoFactory->create();
        // $order_id_data = $dunzo_data->getCollection()->addFieldToSelect('*')->addFieldToFilter("order_id", 460);
        // $dunzo_info_data = $order_id_data->getData();
        // if(isset($dunzo_info_data[0]['order_id'])&&!is_null($dunzo_info_data[0]['order_id'])){
        //     echo "run inside";
        // }else{
        //     echo "no";
        // }

       //  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       //  $customer = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create(); 
       //  $customer->setWebsiteId(1);
       //  $customer->load($customer_id);
       //  if($customer->getId()){
       //      $customer->setTaxvat($fcm_key);
       //      $customer->save();
       //      $resultArr['status'] = 1;
       //      $resultArr['msg'] = "Data is Updated!";
       //  }else{
       //      $resultArr['status'] = 0;
       //      $resultArr['msg'] = "No Data Found!";
       //  }
       // echo json_encode($resultArr);
       // die();

        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $customer = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create(); 
        // $customer->setWebsiteId(1);
        // $this->resource->processRelation($customer);
        // $customer->getResource()->getConnection()->update(
        //    $customer->getResource()->getTable('customer_entity'),
        //     $customer_id,
        //    $customer->getResource()->getConnection()->quoteInto('entity_id = ?', $customer->getId())
        // );
        // $customer->load($customer_id);
        // return $customer->getId();
    }


    /**
     * @return mixed
     */ 
    public function sendNotification(){
        $url = "https://fcm.googleapis.com/fcm/send";
        $token = array("dSwoR4RuR764zEEQMSqmEy:APA91bFwtfitp4GQ0FTtvJsbEoYIHBRgz8f2ku80CXyocQv4wE-P-gJUxktsnjdLlCj33Ug3Iy8H_1E5AUeUkNKFxlruJixM_9MLU6aOh6nK6HIHJ92R5rsfhE00YQMRu32TIrX_hGjs");
        $serverKey = "AAAAVoP2_7w:APA91bGQZZ3EROWhNTS1AIvgLUIY9KzBFdiNXpYUvBkSE6gEuWXfmh-Pml7rsj_T_sErWzvq2aNHP7clFDTF0IlyRlQpM-LKb8jfRTDbBxmR8ypygDFxiJe4JInb-tdrRmMCoiokDAyi";
        $title = "Notification title";
        $body = "Hello This is HAPDaily Notification";
        $notification = array('title' =>$title , 'body' => $body, 'sound' => 'default', 'badge' => '1');
        $arrayToSend = array('registration_ids' => $token, 'notification' => $notification,'priority'=>'high');
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. $serverKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        //Send the request
        $response = curl_exec($ch);
        //Close request
        if ($response === FALSE) {
        die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
    }

    /**
    * @return mixed
    */  
    public function getTimeToCancel(){
        $time_to_cancel = $this->_scopeConfig->getValue('chopserve_source_mapping/time_cancel/time_to_cancel', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $time_to_cancel;
    }


    /**
    * @param string $email
    * @param string $order_id
    * @param string $template_id
    * @return mixed
    */    
    public function sendMailToUsers($email,$order_id,$template_id){   
        $templateId =$template_id; // template id
        $fromEmail = 'hapdaily.emarketing@hap.in';  // sender Email id
        $fromName = 'HAP Daily';             // sender Name
        $toEmail = $email; // receiver email id 
        try {
            // template variables pass here
           if($templateId == 'storeownermail'){
                $templateVars = [
                    'pincode' => '600091'
                ];
            }else{
                 $templateVars = [
                    'customer_name' => 'test',
                    'msg1' => 'test1'
                ];
            }
            $storeId = $this->storeManager->getStore()->getId(); 
            $from = ['email' => $fromEmail, 'name' => $fromName];
            $this->inlineTranslation->suspend(); 
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
            return 'sent';
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
    }

    /**
    * @param string $phone_number
    * @param string $message
    * @return mixed
    */  
    public function sendsms($phone_number,$message){
        $datap = [
        // "from"=>"Hatsun",
        // "to"=> $phone_number,
        // "msg"=> $message
            "from"=> "Hatsun",
            "to"=>"91".$phone_number,
            "msg"=> $message
        ];
        $curl = curl_init("https://api.tatacommunications.com/mmx/v1/messaging/sms");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($datap));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "tcl-WiKqQYcP3HV6POdbwMOTWryUQOlZ6DeAL67UzVvy:5de42133c13a134cb0c5b0e989bdcde85ca6cf2ca2e49c843c8611e877350c5e");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        $response = curl_exec($curl);
        curl_close($curl);
        // print_r($response);
        return $response;
        die();
    }
}


      //  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       //  $customer = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create(); 
       //  $customer->setWebsiteId(1);
       //  $customer->load($customer_id);
       //  if($customer->getId()){
       //      $customer->setTaxvat($fcm_key);
       //      $customer->save();
       //      $resultArr['status'] = 1;
       //      $resultArr['msg'] = "Data is Updated!";
       //  }else{
       //      $resultArr['status'] = 0;
       //      $resultArr['msg'] = "No Data Found!";
       //  }
       // echo json_encode($resultArr);
       // die();
