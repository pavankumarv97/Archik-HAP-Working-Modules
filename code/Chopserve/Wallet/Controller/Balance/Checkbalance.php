<?php
namespace Chopserve\Wallet\Controller\Balance;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Checkbalance extends Action {
	protected $_objectManager = null;
	protected $messageManager;
	protected $request;
	protected $_customerRepositoryInterface;
	public function __construct(
		\Magento\Framework\App\Action\Context $context,		
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		array $data = []) {
			
			$this->_objectManager = $objectManager;
			$this->messageManager = $messageManager;
			$this->request = $request;
			$this->_customerRepositoryInterface = $customerRepositoryInterface;
			parent::__construct($context);
		}

	public function execute()
	 { 
		$resultArr = array();	
		try{
			$userId = $this->request->getParam('user_id');
			 
			if($userId != ''){
				$customerObj = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($userId); 
				if ($customerObj->getId()) {
					$customer = $this->_customerRepositoryInterface->getById($userId);	
					if(null !== $customer->getCustomAttribute('wallet_amount'))
					{
					 $walletAmount = $customer->getCustomAttribute('wallet_amount')->getValue();
					}else{
					  $walletAmount = '';
					}				
					$status = 1;					
					$message = "Success!!";
					$resultArr['status'] = $status;
					$resultArr['message'] = $message;		
					$resultArr['userId'] = $userId;		
					$resultArr['wallet_amount'] = $walletAmount;					
				}else{
					$status = 0;					
					$message = "Failed!! Customer with Id ".$userId." not found";
					$resultArr['status'] = $status;
					$resultArr['message'] = $message;		
				}
			}
			
						
	  }catch (\Magento\Framework\Exception\LocalizedException $e) {
			 $this->messageManager->addError(
				 $e,
				 __('%1', $e->getMessage())
			 );
			$status = 0;					
			$message = "Failed!!";
		}
		echo json_encode($resultArr);
		die;
	 }
}