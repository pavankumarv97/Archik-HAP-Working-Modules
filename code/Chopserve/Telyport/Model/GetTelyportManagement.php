<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Chopserve\Telyport\Model;

use Magento\Sales\Model\OrderFactory;

class GetTelyportManagement implements \Chopserve\Telyport\Api\GetTelyportManagementInterface
{
	protected $_logger;
	protected $_telyportFactory;
	protected $orderFactory;
	public function __construct(
		\Psr\Log\LoggerInterface $logger,
		\Chopserve\Telyport\Model\TelyportFactory $telyportFactory,
		OrderFactory $orderFactory
	) {
		$this->_logger = $logger;
		$this->_telyportFactory = $telyportFactory;
		$this->orderFactory = $orderFactory;
	}
    /**
     * {@inheritdoc}
     */
    public function getTelyport($order_id)
    {
       $resultArr = array();
	   try{
		   $orderModel = $this->orderFactory->create();
		   $this->_logger->info('--order_id--'.$order_id);
		   $order = $orderModel->loadByIncrementId($order_id);
		   $orderId = $order->getId();
		   $this->_logger->info('--orderId--'.$orderId);
		   $telyport_model = $this->_telyportFactory->create();
		   $telyport_model->load($orderId,'order_id');
		   if($telyport_model->getId()){
			   $telyportInfo = $telyport_model->getData();
			   //$this->_logger->info('--telyportInfo--'.json_encode($telyportInfo));
			  return $telyportInfo['telyport_id']; 
		   }
		   return '';
		   
		   
	   }catch (\Exception $e) {
			$this->_logger->info('--telyport Api--'.$e->getMessage());
		} 
	   
    }
}

