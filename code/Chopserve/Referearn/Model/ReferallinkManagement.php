<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Chopserve\Referearn\Model;

class ReferallinkManagement implements \Chopserve\Referearn\Api\ReferallinkManagementInterface
{
	protected $_logger;
	protected $_customerRepositoryInterface;
	public function __construct(
		\Psr\Log\LoggerInterface $logger,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
	) {
		$this->_logger = $logger;
		$this->_customerRepositoryInterface = $customerRepositoryInterface;
	}
    /**
     * {@inheritdoc}
     */
    public function getReferallink($customer_id)
    {
        $customerId = $customer_id;
		$customer = $this->_customerRepositoryInterface->getById($customerId);
		if(null !== $customer->getCustomAttribute('referral_code'))
		{
			$referralCode = $customer->getCustomAttribute('referral_code')->getValue();
			if(isset($referralCode) && $referralCode!=''){
				$baseUrl = 'https://chopserve.com/home?ref=';
				$referralLink = $baseUrl.$referralCode;
				return $referralLink;
			}
		}
		return '';		
    }
}

