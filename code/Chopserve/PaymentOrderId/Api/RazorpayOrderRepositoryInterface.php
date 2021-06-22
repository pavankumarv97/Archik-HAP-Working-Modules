<?php


namespace Chopserve\PaymentOrderId\Api;


use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteFactory;

interface RazorpayOrderRepositoryInterface
{

    /**
     * @param int $customerId
	 * @param boolean $use_wallet
     * @return CartInterface Cart object
     */
    public function getOrderId($customerId,$use_wallet = false);
	
	/**
     * POST for updateBalance api
     * @param mixed $param
     * @return array
     */
    public function createOrder($param);
}
