<?php

namespace Chopserve\AuthCustomer\Model;

use Chopserve\AuthCustomer\Api\OtpRepositoryInterface;
use Chopserve\AuthCustomer\Model\AuthOtpFactory as OtpFactory;
use Chopserve\AuthCustomer\Model\ResourceModel\AuthOtp\CollectionFactory as OtpCollectionFactory;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Webapi\Exception;
use Magento\Integration\Model\Oauth\TokenFactory;





class OtpRepository implements OtpRepositoryInterface
{
    private $httpClientFactory;
    private $_objectManager = null;
    private $otpFactory;
    private $otpCollectionFactory;
    private $customer;
    private $tokenModelFactory;
    protected $_logger;

    public function __construct(
        ZendClientFactory $httpClientFactory, 
        OtpFactory $otpFactory, OtpCollectionFactory $otpCollectionFactory, 
        Customer $customer, TokenFactory $tokenFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Psr\Log\LoggerInterface $logger
    ){
        $this->httpClientFactory = $httpClientFactory;
        $this->otpFactory = $otpFactory;
        $this->otpCollectionFactory = $otpCollectionFactory;
        $this->customer = $customer;
        $this->tokenModelFactory = $tokenFactory;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->_objectManager = $objectManager;
        $this->_logger = $logger;

    }

    /**
     * @inheritDoc
     */
    public function login($customerNumber)
    {
        $objectManager = ObjectManager::getInstance();
        $customerObj = $objectManager->create('Magento\Customer\Model\ResourceModel\Customer\Collection');
        $collection = $customerObj->addAttributeToSelect('*')
            ->addAttributeToFilter('phone_number', $customerNumber)
            ->load();

        $c_data = $collection->getData();

        if (!empty($c_data)) {
            $otpIfGenerated = $this->otpFactory->create()->getCollection()
                ->addFieldToSelect("*")
                ->addFieldToFilter('customer_number', $customerNumber)
                ->load()->getData();

            if (empty($otpIfGenerated[0]['otp'])) {
                // echo "not exits";
                $otp = rand(100000, 999999);
                // $otp = 123456;
                $response = $this->sendOtp($customerNumber, $otp);
                if ($response['status'] == 202) {
                    $otpObj = $this->otpFactory->create();
                    $otpObj->setCustomerNumber($customerNumber);
                    $otpObj->setOtp($otp);
                    $otpObj->save();
                    return [$customerNumber, [$response], [$otpIfGenerated]];
                }
            }

            if (!empty($otpIfGenerated[0]['otp'])) {
                $response = $this->sendOtp($customerNumber, $otpIfGenerated[0]['otp']);
                // echo "exists";
                // print_r($response);
                 if ($response['status'] == 202){
                    $otp = rand(100000, 999999);
                    $otpObj = $this->otpFactory->create();
                    $otpObj->setCustomerNumber($customerNumber);
                    $otpObj->setOtp($otp);
                    $otpObj->save();
                    $resultArr['customer_number'] = $customerNumber;
                    $resultArr['response'] = [$response];
                    $resultArr['otp'] = 123456;
                    // return $resultArr;
                    return [$customerNumber, [$response], [$otpIfGenerated]];
                }
            }

            throw new NoSuchEntityException(__('Something went wrong. Please try again'));
        } else {
            throw new NoSuchEntityException(__('Not a valid data'));
        }
    }

   
    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function checkOtp($customerNumber, $otp)
    {
        $objectManager = ObjectManager::getInstance();
        $customerObj = $objectManager->create('Magento\Customer\Model\ResourceModel\Customer\Collection');
        $collection = $customerObj->addAttributeToSelect('*')
            ->addAttributeToFilter('phone_number', $customerNumber)
            ->load();

        $c_data = $collection->getData();
        // $this->_logger->debug('-checking website id-',array ($c_data));
        if (empty($c_data)) {
            throw new NoSuchEntityException(__('No User Found'));
        }
        // $this->_logger->debug('-checking where code get terminated-',array (1));
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId(1);
        // print_r($c_data);
        // if (!$customer->loadByEmail($c_data[0]['email'])->getIsVerified()) {
        //     throw new Exception(__('User not verified'), 400);
        // }
        // if (!$this->customer->loadByEmail($c_data[0]['email'])->getIsVerified()) {
        //     throw new Exception(__('User not verified'), 400);
        // }

        // $this->_logger->debug('-checking where code get terminated-',array (2));
        $otpList = $this->otpCollectionFactory->create()->getItems();

        foreach ($otpList as $otpItem) {
            // print_r($otpItem->getData());
            if ($otpItem->getOtp() == $otp && $otpItem->getCustomerNumber() == $customerNumber) {
                // $customer = $this->customer->loadByEmail($c_data[0]['email']);
                $customer = $customer->loadByEmail($c_data[0]['email']);
//                return [$customer];
// $this->_logger->debug('-checking where code get terminated-',array (3));
                if ($customer->getId()) {
                    $customerToken = $this->tokenModelFactory->create();
                    $tokenKey = $customerToken->createCustomerToken($customer->getId())->getToken();
                    $otpModel = $this->otpFactory->create();
                    $otpModel->load($otpItem->getId());
                    $otpModel->delete();
                    $resultArr['object'] = ["userId" => $customer->getId() , "token"=> $tokenKey];
                    // $resultArr['token'] = ["token"=> $tokenKey];
                    return $resultArr;
                }
                throw new NoSuchEntityException(__('Something went wrong.Please try again'));
            }
        }
        // throw new NoSuchEntityException(__('Not a valid data'));
    }


    /**
     * @param string $email
     * @param int $customerNumber
     * @param int $otp
     * @return mixed[]|void
     * @throws NoSuchEntityException
     */
    public function verifyNumber($email, $customerNumber, $otp)
    {
        $customerToVerify = $this->customer->setWebsiteId(1)->loadByEmail($email);

        if (!$customerToVerify->getId()) {
            throw new NoSuchEntityException(__('No User Found'));
        }

        $otpList = $this->otpCollectionFactory->create()->getItems();

        foreach ($otpList as $otpItem) {
             // print_r($customerToVerify->getData());

            if ($otpItem->getOtp() == $otp && $otpItem->getCustomerNumber() == $customerNumber || $customerToVerify->getEmail() == $email && $customerToVerify->getPhoneNumber() == $customerNumber) {
                // if ($customerToVerify->getIsVerified()) {
                //     throw new NoSuchEntityException(__('Already Verified'));
                // } 
                $customerToVerify->setIsVerified(1)->setWebsiteId(1);
                $customerToVerify->save();
                $customerToken = $this->tokenModelFactory->create();
                $tokenKey = $customerToken->createCustomerToken($customerToVerify->getId())->getToken();
                $otpModel = $this->otpFactory->create();
                $otpModel->load($otpItem->getId());
                $otpModel->delete();
                $resultArr['object'] = ["userId" =>  $customerToVerify->getId(), "token"=> $tokenKey];
                return $resultArr;
                // echo json_encode($resultArr);
                // die();
            }
        }
        // die();
        // throw new NoSuchEntityException(__('Not Authorized'));
    }

    /**
     * @param string $email
     * @param int $customerNumberNew
     * @return array|mixed[]
     * @throws Exception
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function updatePhoneNumber($email, $customerNumberNew)
    {
        //load customer by email. throw exception if not found.
        $customerToVerify = $this->customer->setWebsiteId(1)->loadByEmail($email);

        if (!$customerToVerify->getId()) {
            throw new NoSuchEntityException(__('No User Found'));
        }

        //get customers's isVerified property. if already verified. throw bad req exception.
        if ($customerToVerify->getIsVerified()) {
            throw new NoSuchEntityException(__('Already Verified'));
        }

        //get customers old phone number match with new if same throw bad req exception.
        if ($customerToVerify->getPhoneNumber() == $customerNumberNew) {
            $otpIfGenerated = $this->otpFactory->create()->getCollection()
                ->addFieldToSelect("*")
                ->addFieldToFilter('customer_number', $customerNumberNew)
                ->load()->getData();

            if (empty($otpIfGenerated[0]['otp'])) {
                $otp = rand(100000, 999999);
                $response = $this->sendOtp($customerNumberNew, $otp);
                 if ($response['status'] == 202){
                    $otpObj = $this->otpFactory->create();
                    $otpObj->setCustomerNumber($customerNumberNew);
                    $otpObj->setOtp($otp);
                    $otpObj->save();
                    return [$customerNumberNew, [$response], [$otpIfGenerated]];
                    die();
                }
            }

            if (!empty($otpIfGenerated[0]['otp'])) {
                $response = $this->sendOtp($customerNumberNew, $otpIfGenerated[0]['otp']);
                 if ($response['status'] == 202){
                    return [$customerNumberNew, [$response], [$otpIfGenerated]];
                    die();
                }
            }
            //or can be used to send otp
        }

        //search in customers for new number. if found match email of found customer and received email. if not true then throw unauthorised exception
        $objectManager = ObjectManager::getInstance();
        $customerObj = $objectManager->create('Magento\Customer\Model\ResourceModel\Customer\Collection');
        $collection = $customerObj->addAttributeToSelect('*')
            ->addAttributeToFilter('phone_number', $customerNumberNew)
            ->load();
        $c_data = $collection->getData();

        if (!empty($c_data) && $c_data[0]['email'] !== $email) {
            throw new Exception(__('No. already associated with another customer'), 403);
        }

        //get saved otp from auth_customer_otp mapped to old phone number. if success then delete it.
        $otpList = $this->otpCollectionFactory->create()->getItems();
        foreach ($otpList as $otpItem) {
            if ($otpItem->getCustomerNumber() == $customerToVerify->getPhoneNumber()) {
                $otpModel = $this->otpFactory->create();
                $otpModel->load($otpItem->getId());
                $otpModel->delete();
                break;
            }
        }

        //update customer number
        $customerToVerify->setPhoneNumber($customerNumberNew);
        $customerToVerify->save();

        //generate otp
        //send otp
        //save otp
        //return true

        $otpIfGenerated = $this->otpFactory->create()->getCollection()
            ->addFieldToSelect("*")
            ->addFieldToFilter('customer_number', $customerNumberNew)
            ->load()->getData();

        if (empty($otpIfGenerated[0]['otp'])) {
            $otp = rand(100000, 999999);
            $response = $this->sendOtp($customerNumberNew, $otp);
             if ($response['status'] == 202){
                $otpObj = $this->otpFactory->create();
                $otpObj->setCustomerNumber($customerNumberNew);
                $otpObj->setOtp($otp);
                $otpObj->save();
                return [$customerNumberNew, [$response], [$otpIfGenerated]];
            }
        }


    }

    /**
     * @param $customerNumber
     * @param $otp
     * @return mixed
     */
    public function sendOtp($customerNumber, $otp)
    {
            $datap = [
            // "from"=>"Hatsun",
            // "to"=> $phone_number,
            // "msg"=> $message
                "from"=> "Hatsun",
                "to"=>"91".$customerNumber,
                "msg"=> $otp." is your Hatsun account verification code"
            ];
            $curl = curl_init("https://api.tatacommunications.com/mmx/v1/messaging/sms");
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($datap));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, "tcl-WiKqQYcP3HV6POdbwMOTWryUQOlZ6DeAL67UzVvy:5de42133c13a134cb0c5b0e989bdcde85ca6cf2ca2e49c843c8611e877350c5e");
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            $responseNew = [];
            $responseNew['status'] = $httpcode; 
            $responseNew['result'] = $response;
            
            return $responseNew;
        
    }


    /**
     * @param mixed $params
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function createCustomer($params){
        try{
            $store = $this->storeManager->getStore();
            $storeId = $store->getStoreId();
            // $websiteId = $this->storeManager->getStore()->getWebsiteId();
            $websiteId = 1;
            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId);
            // $this->_logger->debug('-checking website id-',array (json_encode($websiteId)));
            $customer->loadByEmail($params['email']);// load customer by email to check if customer is availalbe or not
            if(!$customer->getId()){
                $otp = rand(100000, 999999);
               
                $customer->setWebsiteId($websiteId)
                        ->setStore($store)
                        ->setFirstname($params['firstname'])
                        ->setLastname($params['lastname'])
                        ->setPhoneNumber($params['phone_number'])
                        ->setEmail($params['email'])
                        ->setPassword("testuser")
                        ->setIsVerified(0);               
                 // $otpObj = $this->otpFactory->create();
                 //    $otpObj->setCustomerNumber($params['phone_number']);
                 //    $otpObj->setOtp($params['otp']);
                 //    $otpObj->save();
                    $customer->save();
                    $response = $this->sendOtp($params['phone_number'], $otp);
                    // $this->_logger->debug('-get website id from customer-',array (json_encode($customer->getWebsiteId())));
                $resultArr['status'] = 1;
                $resultArr['message'] = "Your Account created successfully!";
            }else{
                $resultArr['status'] = 0;
                $resultArr['message'] = "Customer with same email id already exists!";
                throw new NoSuchEntityException(__('Customer with same email id already exists!'));
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
}