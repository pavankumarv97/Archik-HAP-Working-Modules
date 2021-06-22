<?php
declare(strict_types=1);

namespace Chopserve\Email\Model;

class SendemailManagement implements \Chopserve\Email\Api\SendemailManagementInterface{
	protected $_objectManager;
	protected $emailHelper;
	public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Chopserve\Email\Helper\Data $emailHelper		
	) {
		$this->_objectManager = $objectManager;
		$this->emailHelper = $emailHelper;
	}

    /**
     * {@inheritdoc}
     */
    public function Sendemail($param)
    {	
		$resultArr = array();
		$result =  json_encode($param);
		$resultSet = json_decode($result,true);	
		$email = $resultSet['email'];
		if(isset($email) && $email != ''){
			$this->emailHelper->sendEmail($resultSet);
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
		return $resultArr;
		//echo json_encode($resultArr,JSON_PRETTY_PRINT);
		//die;
    }

	/**
	* POST for sendemail api
	* @param string $param
	* @return array
	*/
	public function getTemplates($param){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
		$alltemplele = $objectManager->get('Magento\Email\Model\Template\Config');
		$alltemplele_data = $alltemplele->getAvailableTemplates();
		echo json_encode($alltemplele_data);
	}

	/**
	* POST for sendemail pickup api
	* @param mixed $param
	* @return mixed
	*/
	public function pickupemail($param){
		try{
			$this->emailHelper->storePickup($param);
			return 'Sent!';
		} catch(\Exception $e){
			return 'Error';
		}
	}

}