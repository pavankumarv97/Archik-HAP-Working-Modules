<?php
declare(strict_types=1);

namespace Chopserve\Wallet\Model;

class CheckBalanceManagement implements \Chopserve\Wallet\Api\CheckBalanceManagementInterface{
	protected $_objectManager = null;
	protected $messageManager;
	protected $request;
	protected $_customerRepositoryInterface;
	public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface		
	) {
		$this->_objectManager = $objectManager;
		$this->messageManager = $messageManager;
		$this->request = $request;
		$this->_customerRepositoryInterface = $customerRepositoryInterface;
	}

    /**
     * {@inheritdoc}
     */
    public function checkBalance($param)
    {
        $resultArr = array();
		$result =  json_encode($param);
		$resultSet = json_decode($result,true);	
		try{
			$userId = $resultSet['user_id'];			
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
		return $resultArr;
    }
}