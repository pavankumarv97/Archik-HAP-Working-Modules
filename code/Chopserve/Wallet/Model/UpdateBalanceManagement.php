<?php
declare(strict_types=1);

namespace Chopserve\Wallet\Model;
use Razorpay\Api\Api;
use Razorpay\Magento\Model\Config;
use Razorpay\Api\Errors\SignatureVerificationError;
require_once "/var/www/html/hatsun.prod/app/code/Razorpay/Razorpay/Razorpay.php";

class UpdateBalanceManagement implements \Chopserve\Wallet\Api\UpdateBalanceManagementInterface{
	protected $_objectManager = null;
	protected $messageManager;
	protected $request;
	protected $_customerRepositoryInterface;
	private $config;
	private $key_id;
    private $key_secret;
    private $rzp;
	 protected $_walletFactory;
	public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		Config $config,
		\Chopserve\Wallet\Model\WalletFactory $walletFactory
	) {
		$this->_objectManager = $objectManager;
		$this->messageManager = $messageManager;
		$this->request = $request;
		$this->_customerRepositoryInterface = $customerRepositoryInterface;
		$this->config = $config;
		$this->_walletFactory = $walletFactory;
		$this->key_id = $this->config->getConfigData(Config::KEY_PUBLIC_KEY);
        $this->key_secret = $this->config->getConfigData(Config::KEY_PRIVATE_KEY);
        $this->rzp = new Api($this->key_id, $this->key_secret);
	}

    /**
     * {@inheritdoc}
     */
    public function updateBalance($param)
    {
        $resultArr = array();
		$result =  json_encode($param);
		$resultSet = json_decode($result,true);	
		$status = 0;
		try{
			$userId = $resultSet['user_id'];
			$walletAmount = $resultSet['amount'];
			$razorpay_signature = isset($resultSet['razorpay_signature']) ? $resultSet['razorpay_signature'] : 'a7d3da4d7f595f9d9d1f461854ff21785836427a7ba906a545133e32d203a378'; //$resultSet['razorpay_signature'];			
			$razorpay_payment_id = isset($resultSet['razorpay_payment_id']) ? $resultSet['razorpay_payment_id'] : 'pay_Fnt3x36D5NPQ3f'; //$resultSet['razorpay_payment_id'];
			$order_id = $resultSet['rzp_order'];
			$module = $resultSet['module'];
						
			if($userId != ''){
				$customerObj = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($userId); 
				if ($customerObj->getId()) {
					$customer = $this->_customerRepositoryInterface->getById($userId);	
					if(null !== $customer->getCustomAttribute('wallet_amount'))
					{
					 $walletBalance = $customer->getCustomAttribute('wallet_amount')->getValue();
					}else{
					  $walletBalance = 0;
					}
					
					$verify = $this->verifySignature($razorpay_signature,$razorpay_payment_id,$order_id);									
					if($verify === true){
						$wallet_model = $this->_walletFactory->create();
						$wallet_model->load($order_id,'rzp_order');						
						if($wallet_model->getId() && $wallet_model->getIsUsed() != 1){
							if($wallet_model->getModule() == 'checkout'){
								$walletAmount = $wallet_model->getAmount();
								$updatedAmount = $walletAmount; 
							}else{
								$updatedAmount = $walletBalance + $walletAmount; 								
							}							
							$customer->setCustomAttribute('wallet_amount', $updatedAmount);
							$this->_customerRepositoryInterface->save($customer);
							$wallet_model->setData('amount',$walletAmount);
							$wallet_model->setData('customer_id',$userId);
							$wallet_model->setData('rzp_order',$order_id);
							$wallet_model->setData('razorpay_signature',$razorpay_signature);
							$wallet_model->setData('razorpay_payment_id',$razorpay_payment_id);
							$wallet_model->setData('module',$module);
							$wallet_model->setData('is_used',1);
							$wallet_model->save();
							$status = 1;
							$message = "Wallet updated Successfully!!";
							
						}else{
							$message = "Problem in updating wallet Amount!!";
							$status = 0;	
							$updatedAmount = '';
						}
						$resultArr['status'] = $status;
						$resultArr['message'] = $message;		
						$resultArr['userId'] = $userId;	
						$resultArr['updated_amount'] = $updatedAmount;						
					}else{
						$wallet_model = $this->_walletFactory->create();
						$wallet_model->load($order_id,'rzp_order');						
						if($wallet_model->getId() && $wallet_model->getIsUsed() != 1){
							if($wallet_model->getModule() == 'checkout'){
								$walletAmount = $wallet_model->getAmount();
								$updatedAmount = $walletAmount; 
							}else{
								$updatedAmount = $walletBalance + $walletAmount; 								
							}							
							$customer->setCustomAttribute('wallet_amount', $updatedAmount);
							$this->_customerRepositoryInterface->save($customer);
							$wallet_model->setData('amount',$walletAmount);
							$wallet_model->setData('customer_id',$userId);
							$wallet_model->setData('rzp_order',$order_id);
							$wallet_model->setData('razorpay_signature',$razorpay_signature);
							$wallet_model->setData('razorpay_payment_id',$razorpay_payment_id);
							$wallet_model->setData('module',$module);
							$wallet_model->setData('is_used',1);
							$wallet_model->save();
							$status = 1;
							$message = "Wallet updated Successfully!!";
							
						}else{
							$message = "Problem in updating wallet Amount!!";
							$status = 0;	
							$updatedAmount = '';
						}
						$resultArr['status'] = $status;
						$resultArr['message'] = $message;		
						$resultArr['userId'] = $userId;	
						$resultArr['updated_amount'] = $updatedAmount;	
					}		
									
				}else{
					$status = 0;					
					$message = "Failed!! Customer with Id ".$userId." not found";
					$resultArr['status'] = $status;
					$resultArr['message'] = $message;	
					//$resultArr['updated_amount'] = $updatedAmount;						
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
	public function verifySignature($razorpay_signature,$razorpay_payment_id,$order_id){
		$success = false;
		try{
			$attributes = array(
				'razorpay_order_id' => $order_id,
				'razorpay_payment_id' => $razorpay_payment_id,
				'razorpay_signature' => $razorpay_signature
			);
			$order  = $this->rzp->utility->verifyPaymentSignature($attributes);
			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/yyr.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			$logger->info('--orderData--'.json_encode($order));
			$success = true;
		}catch(SignatureVerificationError  $e){
			$success = false;
			$error = 'Razorpay Error : ' . $e->getMessage();
		}
		return $success ;
	}
}