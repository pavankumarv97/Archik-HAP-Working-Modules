<?php
namespace Test\Sample\Observer;
 
use Magento\Framework\Event\ObserverInterface;
 
class RestrictAddToCart implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;
    protected $_logger;
 
    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger
    )
    {
         $this->_logger = $logger;
        $this->_messageManager = $messageManager;

    }
 
    /**
     * add to cart event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $a =1;
        $orderData = $observer->getEvent()->getName();
        $this->_logger->debug('observer event',array($orderData));     
        if ($a) {
                $this->_messageManager->addError(__('your custom message'));
                //set false if you not want to add product to cart
                $observer->getRequest()->setParam('product', false);
                return $this;
         }
 
        return $this;
    }
}