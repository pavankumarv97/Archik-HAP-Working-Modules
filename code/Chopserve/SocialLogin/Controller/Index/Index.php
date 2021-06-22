<?php

namespace Chopserve\SocialLogin\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Chopserve\SocialLogin\Model\ItemFactory ;


class Index extends \Magento\Framework\App\Action\Action
{
    protected $_objectManager = null;
    protected $messageManager;
    protected $request;
    protected $_customerRepositoryInterface;
    protected $_pageFactory;
    protected $_socialloginFactory;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\App\Request\Http $request,
        \Chopserve\SocialLogin\Model\ItemFactory $apiCollectionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        array $data = []) {
        $this->_pageFactory = $pageFactory;
        $this->_objectManager = $objectManager;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->_socialloginFactory = $apiCollectionFactory;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($context);
    }

    public function execute()
    {
//        $contactModel = $this->_objectManager->create('Chopserve\SocialLogin\Model\Apiauth');
//        $collection = $contactModel->getCollection();
//        foreach($collection as $contact) {
//            var_dump($contact->getData());
//        }
        $contactModel = $this->_objectManager->create('Chopserve\SocialLogin\Model\Item');
        $contactModel->load(1,'id');
        $resultArr['status'] = 0;
       print_r($contactModel);
        echo json_encode($resultArr);
        die();

    }



}
