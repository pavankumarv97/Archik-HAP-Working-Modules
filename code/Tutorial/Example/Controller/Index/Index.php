<?php

namespace Tutorial\Example\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;

    }

    public function execute()
    {

        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();        
 
$paymentHelper = $objectManager->get('Magento\Payment\Helper\Data');
$allPaymentMethods = $paymentHelper->getPaymentMethods();
$allPaymentMethodsArray = $paymentHelper->getPaymentMethodList();
 
var_dump($allPaymentMethodsArray);
var_dump($allPaymentMethods);
 
$paymentConfig = $objectManager->get('Magento\Payment\Model\Config');
$activePaymentMethods = $paymentConfig->getActiveMethods();
 
var_dump(array_keys($activePaymentMethods));
 
$orderPaymentCollection = $objectManager->get('\Magento\Sales\Model\ResourceModel\Order\Payment\Collection');
$orderPaymentCollection->getSelect()->group('method');
$paymentMethods[] = array('value' => '', 'label' => 'Any');
foreach ($orderPaymentCollection as $col) { 
    $paymentMethods[] = array('value' => $col->getMethod(), 'label' => $col->getAdditionalInformation()['method_title']);            
}  
print_r($paymentMethods);
        // $resultPageFactory = $this->resultPageFactory->create();

        // // Add page title
        // $resultPageFactory->getConfig()->getTitle()->set(__('Example module 123'));

        // // Add breadcrumb
        // /** @var \Magento\Theme\Block\Html\Breadcrumbs */
        // $breadcrumbs = $resultPageFactory->getLayout()->getBlock('breadcrumbs');
        // $breadcrumbs->addCrumb('home',
        //     [
        //         'label' => __('Home'),
        //         'title' => __('Home'),
        //         'link' => $this->_url->getUrl('')
        //     ]
        // );
        // $breadcrumbs->addCrumb('tutorial_example',
        //     [
        //         'label' => __('Example1'),
        //         'title' => __('Example1')
        //     ]
        // );

        // return $resultPageFactory;

        //     $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $options = $objectManager->create('Magento\Quote\Model\Quote');
        // $_order = $options->load(74);
        // // print_r($_order->getExtensionAttributes());
        // // print_r($_order->getBillingAddress()->getData());
        // // print_r($_order->getBillingAddress()->getFirstName());
        // $customerId = 3;
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $customerObj = $objectManager->create('Magento\Catalog\Model\Product')->getCollection();
        // print_r($customerObj->getData());
        // $customerAddress = array();
        // // print_r($customerObj->getData());
        // foreach ($customerObj->getAddresses() as $address)
        // {
        //     $customerAddress[] = $address->toArray();
        // }
        // print_r($customerObj->getAddresses());





    }
}