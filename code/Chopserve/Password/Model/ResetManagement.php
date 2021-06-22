<?php 
namespace Chopserve\Password\Model;
 
 
class ResetManagement {

    private $objectManager = null;
    protected $customerFactory;
    protected $response;
    protected $request;
    protected $encryptor;
	
    public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Webapi\Rest\Response $response,
        \Magento\Framework\Webapi\Rest\Request $request
	) {
		$this->_objectManager = $objectManager;
        $this->_customerFactory = $customerFactory;
        $this->response = $response;
        $this->request = $request;
        $this->_encryptor = $encryptor;
	}

/**
	 * {@inheritdoc}
	 */

	public function getReset($email,$resetToken,$newPassword)
	{
        try{
            $this->response->setHeader('Access-Control-Allow-Methods', $this->request->getHeader('Access-Control-Request-Method'), true);
            $this->response->setHeader('Access-Control-Allow-Headers', $this->request->getHeader('Access-Control-Request-Headers'), true);
            $result = array();
            $customerObj = $this->_objectManager->create('Magento\Customer\Model\ResourceModel\Customer\Collection'); 
            $customerObj->addAttributeToSelect('*')
            ->addAttributeToFilter('email',$email)
            ->load()->getData();
            if($customerObj != ''){
                foreach($customerObj as $customer){
                    if($email == $customer['email']){
                        //check rp_token with resetToken??
                        //generate rp_token
                        $customer->setPasswordHash($this->_encryptor->getHash($newPassword, true)); // here _encryptor is an instance of \Magento\Framework\Encryption\EncryptorInterface
                        if($customer->save()){
                            $result['status'] = 1;
                            $result['msg'] = "New Password Saved!";
                        } else{
                            $result['status'] = 0;
                            $result['msg'] = "Password unable to Save!";
                        }
                        echo json_encode($result);
                        exit();
                    } else {
                        $result['status'] = 0;
                        $result['msg'] = "Email do not Match!";
                    }
                }
                echo json_encode($result);
            }else{
                $result['status'] = 0;
                $result['msg'] = "Data Empty!";
            }    
        } catch(Exception $e) {
            $result['status'] = 'Failure!';
            $result['msg'] = $e;
            echo json_encode($result);
        }
	}
}


