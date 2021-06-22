<?php

namespace Test\Sample\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class CheckoutCartProductAddBefore implements ObserverInterface
{
	protected $_logger;

    public function __construct(
		\Psr\Log\LoggerInterface $logger		
    ) {
        $this->_logger = $logger;
    }

     public function execute(EventObserver $observer)
    {
    	
    	$this->_logger->debug('add to cart before',array (1,2));
    }
}