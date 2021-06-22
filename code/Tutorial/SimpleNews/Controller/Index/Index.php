<?php

namespace Tutorial\SimpleNews\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Tutorial\SimpleNews\Model\NewsFactory;

class Index extends Action
{
    /**
     * @var \Tutorial\SimpleNews\Model\NewsFactory
     */
    protected $_modelNewsFactory;

    /**
     * @param Context $context
     * @param NewsFactory $modelNewsFactory
     */
    public function __construct(
        Context $context,
        NewsFactory $modelNewsFactory
    ) {
        parent::__construct($context);
        $this->_modelNewsFactory = $modelNewsFactory;
    }

    public function execute()
    {
        // /**
        //  * When Magento get your model, it will generate a Factory class
        //  * for your model at var/generaton folder and we can get your
        //  * model by this way
        //  */
        // $newsModel = $this->_modelNewsFactory->create();

        // // Load the item with ID is 1
        // $item = $newsModel->load(1);
        // var_dump($item->getData());

        // // Get news collection
        // $newsCollection = $newsModel->getCollection();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $options = $objectManager->create('Magento\Quote\Model\Quote');
        $_order = $options->load(74);
        // print_r($_order->getExtensionAttributes());
        // print_r($_order->getBillingAddress()->getData());
        // print_r($_order->getBillingAddress()->getFirstName());
        $customerId = 3;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
        $customerAddress = array();
        // print_r($customerObj->getData());
        foreach ($customerObj->getAddresses() as $address)
        {
            $customerAddress[] = $address->toArray();
        }

        // print_r($customerAddress);
        // // foreach ($customerAddress as $customerAddres) {
// 
        //     echo $customerAddres['street'];
        //     echo $customerAddres['city'];
        // }



        // Load all data of collection
        // var_dump($newsCollection->getData());
    }
}