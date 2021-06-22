<?php

namespace Chopserve\AuthCustomer\Plugin;

use Chopserve\AuthCustomer\Model\AuthOtpFactory as OtpFactory;
use Chopserve\AuthCustomer\Model\OtpRepository;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\State\InputMismatchException;
use Psr\Log\LoggerInterface;

class AuthPreCheck
{
    protected $customerRepository;
    private $logger;
    private $customerModel;
    private $otpRepository;
    private $otpFactory;

    public function __construct(CustomerRepositoryInterface $customerRepository, LoggerInterface $logger, Customer $customer, OtpRepository $otpRepository, OtpFactory $otpFactory)
    {
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
        $this->customerModel = $customer;
        $this->otpRepository = $otpRepository;
        $this->otpFactory = $otpFactory;
    }

    public function aroundCreateAccount(AccountManagement $subject, callable $proceed, $customer, $password = null, $redirectUrl = '')
    {
        $objectManager = ObjectManager::getInstance();
        $customerObj = $objectManager->create('Magento\Customer\Model\ResourceModel\Customer\Collection');
        $collection = $customerObj->addAttributeToSelect('*')
            ->addAttributeToFilter('phone_number', $customer->getCustomAttribute('phone_number')->getValue())
            ->load();

        $c_data = $collection->getData();

        if (!empty($c_data)) {
            if (!$this->customerModel->loadByEmail($customer->getEmail())->getIsVerified()) {
                $customerNumber = $this->customerModel->loadByEmail($customer->getEmail())->getPhoneNumber();

                $otpIfGenerated = $this->otpFactory->create()->getCollection()
                    ->addFieldToSelect("*")
                    ->addFieldToFilter('customer_number', $customerNumber)
                    ->load()->getData();

                if (empty($otpIfGenerated[0]['otp'])) {
                    $otp = rand(100000, 999999);
                    $response = $this->otpRepository->sendOtp($customerNumber, $otp);
                     if ($response['status'] == 202){
                        $otpObj = $this->otpFactory->create();
                        $otpObj->setCustomerNumber($customerNumber);
                        $otpObj->setOtp($otp);
                        $otpObj->save();
                    }
                }

                if (!empty($otpIfGenerated[0]['otp'])) {
                    $this->otpRepository->sendOtp($customerNumber, $otpIfGenerated[0]['otp']);
                }

                return true;
            }
            throw new InputMismatchException(
                __('A customer with the same Mobile number already exist.')
            );
        } else {
            $customer->setCustomAttribute('is_verified', 0);
            $returnValue = $proceed($customer, $password = null, $redirectUrl = '');

            $customerNumber = $this->customerModel->loadByEmail($customer->getEmail())->getPhoneNumber();

            $otpIfGenerated = $this->otpFactory->create()->getCollection()
                ->addFieldToSelect("*")
                ->addFieldToFilter('customer_number', $customerNumber)
                ->load()->getData();

            if (empty($otpIfGenerated[0]['otp'])) {
                $otp = rand(100000, 999999);
                $response = $this->otpRepository->sendOtp($customerNumber, $otp);
                 if ($response['status'] == 202){
                    $otpObj = $this->otpFactory->create();
                    $otpObj->setCustomerNumber($customerNumber);
                    $otpObj->setOtp($otp);
                    $otpObj->save();
                }
            }

            if (!empty($otpIfGenerated[0]['otp'])) {
                $this->otpRepository->sendOtp($customerNumber, $otpIfGenerated[0]['otp']);
            }
        }
        return $returnValue;
    }
}
