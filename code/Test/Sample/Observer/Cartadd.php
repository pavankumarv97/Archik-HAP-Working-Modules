<?php

namespace Test\Sample\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Http\Context as customerSession;

class Cartadd implements ObserverInterface{
    protected $cart;
    protected $messageManager;
    protected $redirect;
    protected $request;
    protected $product;
    protected $customerSession;

    public function __construct(RedirectInterface $redirect, Cart $cart, ManagerInterface $messageManager,  RequestInterface $request, Product $product, customerSession $session){
        $this->redirect = $redirect;
        $this->cart = $cart;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->product = $product;
        $this->customerSession = $session;
    }

    public function execute(\Magento\Framework\Event\Observer $observer){
            $postValues = $this->request->getPostValue();
            $cartItemsCount = $this->cart->getQuote()->getItemsCount();
            //your code to restrict add to cart
             $this->messageManager->addErrorMessage(__('before msg . '));
            $a = 1;
            if ($a) {
                $observer->getRequest()->setParam('product', false);
                $this->messageManager->addErrorMessage(__('error msg . '));
        }
    }
}