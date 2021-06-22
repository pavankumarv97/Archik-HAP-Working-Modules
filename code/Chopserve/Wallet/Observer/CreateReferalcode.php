<?php
namespace Chopserve\Wallet\Observer;
use Magento\Framework\Event\ObserverInterface;

class CreateReferalcode implements ObserverInterface
{
    protected $_customerRepositoryInterface;
	public $mathRandom;
	

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		\Magento\Framework\Math\Random $mathRandom
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
		$this->mathRandom = $mathRandom;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
		$referralCode = $this->getRandomString(8);
		$customer->setCustomAttribute('referral_code', $referralCode);
		$this->_customerRepositoryInterface->save($customer);		
    }
	public function getRandomString($length,  $chars = null)
    {
		$chars = \Magento\Framework\Math\Random::CHARS_LOWERS
            . \Magento\Framework\Math\Random::CHARS_UPPERS
            . \Magento\Framework\Math\Random::CHARS_DIGITS;
        return $this->mathRandom->getRandomString($length, $chars);
    }
}