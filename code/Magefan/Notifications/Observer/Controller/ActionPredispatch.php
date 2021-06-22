<?php
/**
 * Copyright © 2017 Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\Notifications\Observer\Controller;
use Magento\Framework\Notification\NotifierInterface as NotifierPool;

class ActionPredispatch implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Check every 10 min
     */
    const TIMEOUT = 600;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    private $cacheTypeList;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $backendSession;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory
     */
    protected $reviewCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    protected $notifierPool;
    protected $orderInterface;
    protected $emailHelper;

    /**
     * Initialization
     * @param \Magento\Framework\App\Cache\TypeListInterface               $cacheTypeList
     * @param \Magento\Framework\Message\ManagerInterface                  $messageManager
     * @param \Magento\Backend\Model\Auth\Session                          $backendSession
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                  $date,
     * @param \Magento\Framework\UrlInterface                              $url
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory   $orderCollectionFactory
     * @param \Magento\Framework\Notification\NotifierInterface        $notifierPool
     */
    public function __construct(
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\UrlInterface $url,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        NotifierPool $notifierPool,
        \Magento\Backend\Model\Url $backendUrlManager,
        \Magento\Sales\Api\Data\OrderInterface $orderInterface,
        \Magento\Sales\Model\ResourceModel\Order $orderResourceModel,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Chopserve\Email\Helper\Data $emailHelper

    ) {
        $this->cacheTypeList = $cacheTypeList;
        $this->messageManager = $messageManager;
        $this->backendSession = $backendSession;
        $this->date = $date;
        $this->url = $url;
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->notifierPool = $notifierPool;
        $this->backendUrlManager  = $backendUrlManager;
        $this->orderInterface = $orderInterface;
        $this->orderResourceModel = $orderResourceModel;
        $this->orderRepository = $orderRepository;
        $this->emailHelper = $emailHelper;
    }
    public function getBackendUrl(){
        return $this->backendUrlManager->getUrl('adminhtml/');
    }
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        if (!$this->backendSession->isLoggedIn()) {
            return; // Isn't logged in
        }

        if ($observer->getRequest()->isXmlHttpRequest()) {
            return; // It's ajax request
        }

        if ($observer->getRequest()->getMethod() == 'POST') {
            return; // It's post request
        }



        $this->checkCacheTypes();
        $this->checkReviews();
        $this->checkOrders();
      //  $this->addNotification();
    }

    /**
     * Check if cache types are enabled
     * @return void
     */
    protected function checkCacheTypes()
    {
        $disabled = [];
        foreach ($this->cacheTypeList->getTypes() as $cacheType) {
            if (!$cacheType->getStatus()) {
                 $disabled[] = $cacheType->getCacheType();
            }
        }

        if (count($disabled)) {
            $this->messageManager->addNotice(
                __('The following Cache Type(s) are disabled: %1. <a href="%2">Manage Cache</a>.',
                    implode(', ', $disabled),
                    $this->url->getUrl('adminhtml/cache')
                )
            );
        }
    }

    /**
     * Check if any pending review exists
     * @return void
     */
    protected function checkReviews()
    {
        $pendignReview = $this->reviewCollectionFactory->create()
            ->addFieldToFilter('status_id', \Magento\Review\Model\Review::STATUS_PENDING)
            ->setPageSize(1)
            ->getFirstItem();

        if ($pendignReview->getId()) {
            $this->messageManager->addNotice(
                __('Some customer reviews are pending for approval. <a href="%1">Manage Reviews</a>.',
                    $this->url->getUrl('review/product/index')
                )
            );
        }
    }

    /**
     * Check if any pending order exists
     * @return void
     */
    protected function checkOrders()
    {
        $admin_sc = $this->backendSession->getUser()->getSourceCode();
        $order1 = $this->orderCollectionFactory->create()
            ->addFieldToFilter('source_code', $admin_sc)  
            ->addFieldToFilter('status', array('pending','processing'));
        $order1->setOrder('entity_id','DESC');
        $order1->setPageSize(1)->setCurPage(1);
         //  
        $newOrder = $order1->getData();
        $order = $this->orderRepository->get($newOrder[0]['entity_id']);
        if($order->getIsNotified() == 1){
            return;
        }else{
            $order->setIsNotified(1);
            $this->emailHelper->customerEmail('pavankumarv287.pk@gmail.com','000000461');
            $this->orderResourceModel->save($order);  
            $sampleUrl = $this->getBackendUrl();
            // Add notice
            $this->notifierPool->addNotice('New Order Alert!', 'Please check your orders dashboard for more details!');    
             
        }
    }

    
}
