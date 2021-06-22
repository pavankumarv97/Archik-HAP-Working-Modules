<?php

namespace Chopserve\AuthCustomer\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception;

interface OtpRepositoryInterface
{

    /**
     * @param int $customerNumber
     * @return array[]
     * @throws NoSuchEntityException
     */
    public function login($customerNumber);
//    public function generateOtp($customerNumber);

    /**
     * @param int $customerNumber
     * @param int $otp
     * @return mixed[]
     * @throws NoSuchEntityException
     */
    public function checkOtp($customerNumber, $otp);


    /**
     * @param string $email
     * @param int $customerNumber
     * @param int $otp
     * @return mixed[]
     * @throws NoSuchEntityException
     */
    public function verifyNumber($email, $customerNumber, $otp);

    /**
     * @param string $email
     * @param int $customerNumberNew
     * @return mixed[]
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updatePhoneNumber($email, $customerNumberNew);


    /**
     * @param mixed $params
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function createCustomer($params);


}
