<?php
namespace Chopserve\Email\Controller\Index;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Sendemail extends Action {
	protected $_objectManager = null;
	protected $messageManager;
	protected $request;
	protected $emailHelper;
	protected $formKey;
	public function __construct(
		\Magento\Framework\App\Action\Context $context,		
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\Request\Http $request,
		\Chopserve\Email\Helper\Data $emailHelper,
		 \Magento\Framework\Data\Form\FormKey $formKey,
		array $data = []) {			
			$this->_objectManager = $objectManager;
			$this->messageManager = $messageManager;
			$this->request = $request;
			$this->emailHelper = $emailHelper;
			$this->formKey = $formKey;
			parent::__construct($context);
		}

	public function execute()
	 {
		$resultArr = array();	
		try{
			
			$email = $this->request->getParam('email');	
	
			if($email != ''){		
				$this->emailHelper->sendEmail($email);		
				$status = 1;					
				$message = "Success!!";
				$resultArr['status'] = $status;
				$resultArr['message'] = $message;
			}else{
				$status = 0;					
				$message = "Failed!! email id can not be empty!!";
				$resultArr['status'] = $status;
				$resultArr['message'] = $message;		
			}			
						
	  }catch (\Magento\Framework\Exception\LocalizedException $e) {
			$this->messageManager->addError($e->getMessage());
			$status = 0;					
			$message = "Failed!!".$e->getMessage();
			$resultArr['status'] = $status;
			$resultArr['message'] = $message;		
		}
		echo json_encode($resultArr);
		die;
	 }
}