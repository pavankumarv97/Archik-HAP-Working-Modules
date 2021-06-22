<?php 
namespace Chopserve\Password\Model;
 
 
class PasswordManagement {

    private $objectManager = null;
    protected $customerFactory;
    protected $request;
    protected $response;
    protected $encryptor;
    protected $customerAccountManagement;
	
    public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement,
        \Magento\Framework\Webapi\Rest\Response $response,
        \Magento\Framework\Webapi\Rest\Request $request
	) {
		$this->_objectManager = $objectManager;
        $this->_customerFactory = $customerFactory;
        $this->request = $request;
        $this->response = $response;
        $this->_customerAccountManagement = $customerAccountManagement;
        $this->_encryptor = $encryptor;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPost($email,$template,$website_id)
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
                        //send reset link to customer
                        try{
                            $this->_customerAccountManagement->initiatePasswordReset($email,$template,$website_id);
                            $result['status'] = 1;
                            $result['msg'] = "Success!";
                        }catch(Exception $err){
                            $result['status'] = 0;
                            $result['msg'] = "Not able to send!";
                        }
                        $result['resetToken'] = $customer['rp_token'];
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


