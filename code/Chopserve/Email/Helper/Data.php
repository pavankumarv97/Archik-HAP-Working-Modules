<?php
namespace Chopserve\Email\Helper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Customer;
use Magento\Sales\Model\Order;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_logger;	
	protected $transportBuilder;
    protected $storeManager;
    protected $inlineTranslation;
	protected $_scopeConfig;
	protected $customer;
	
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,       
		\Psr\Log\LoggerInterface $logger,
		TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $state,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		Customer $customer,
		Order $order
	) {
	   $this->_logger = $logger;	   
	   $this->transportBuilder = $transportBuilder;
       $this->storeManager = $storeManager;
       $this->inlineTranslation = $state;
	   $this->_scopeConfig = $scopeConfig;
	   $this->customer = $customer;
	   $this->order = $order;
       parent::__construct($context);
    }
    public function storePickup($param){
    	$orderId = isset($param['orderId']) ? $param['orderId'] : '';
        $customer_id = isset($param['customer_id']) ? $param['customer_id'] : '';
    	$templateId = 'storepickup'; // template id
        $fromEmail = 'mohanade2503@gmail.com';  // sender Email id
        $fromName = 'HAP Daily';             // sender Name
        $toEmail = $this->customer->load($customer_id)->getEmail();
        try {
            // template variables pass here
            $templateVars = [
                'msg' => 'test',
                'msg1' => 'test1'
            ];
 
            $storeId = $this->storeManager->getStore()->getId();
 
            $from = ['email' => $fromEmail, 'name' => $fromName];
            $this->inlineTranslation->suspend();
 
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
    }
	public function sendEmail($resultSet)
    {
        //$templateId = 'my_custom_email_template'; // template id
        $fromEmail = $resultSet['email'];  // sender Email id
        $fromName = 'HAP Daily';             // sender Name
		$orderId = isset($resultSet['orderId']) ? $resultSet['orderId'] : '';
		$heading = isset($resultSet['heading']) ? $resultSet['heading'] : '';
		$option = isset($resultSet['option']) ? $resultSet['option'] : '';
        //$toEmail = 'jafarp26@gmail.com'; // receiver email id 
		$emails = ['mohanade2503@gmail.com'];
        try {
        	$storeId = $this->storeManager->getStore()->getId(); 
            $from = ['email' => $fromEmail, 'name' => $fromName];
			$templateVars = [
                'email' => $fromEmail,
                'orderId' => $orderId,
                'heading' => $heading,
                'option' => $option
            ];
            $this->inlineTranslation->suspend(); 
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;		
			
			$templateId = $this->_scopeConfig->getValue('my_custom/email/template',$storeScope);
			// $shipping_description = $this->order->getShippingDescription();
			// if($shipping_description == 'storepickup - storepickup'){
			// 	$templateId = "storepickup";
			// }
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
                ->setTemplateOptions($templateOptions)
				->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($emails)
				->addBcc('hapdaily.emarketing@hap.in')
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
			$this->customerEmail($fromEmail,$orderId);
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
    }
	public function customerEmail($toEmail,$orderId){
		 try {
			$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;			
			//$templateId = 'customer_email_chopserve'; // template id
			$templateId = $this->_scopeConfig->getValue('customer/email/chopserve',$storeScope);
			$fromName = 'HAP Daily';  
			$customer = $this->customer->loadByEmail($toEmail);
			if ($customer->getId()) {
				$customerName = $customer->getName();
				$storeId = $this->storeManager->getStore()->getId(); 
				
				$fromEmail = $this->_scopeConfig->getValue('trans_email/ident_support/email',$storeScope);
				$senderName = $this->_scopeConfig->getValue('trans_email/ident_support/name',$storeScope);
				$from = ['email' => $fromEmail, 'name' => $fromName];
				$templateVars = [
					'email' => $toEmail,
					'orderId' => $orderId,
					'name' => $customerName,
					
				];
				$this->inlineTranslation->suspend();             
				$templateOptions = [
					'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
					'store' => $storeId
				];
				$transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
					->setTemplateOptions($templateOptions)
					->setTemplateVars($templateVars)
					->setFrom($from)
					->addTo($toEmail)
					->addBcc('hapdaily.emarketing@hap.in')
					->getTransport();
				$transport->sendMessage();
				$this->inlineTranslation->resume();
			}
           
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
	}


    public function emailcron()
    {
        // send email
        $templateId = 'my_custom_email_template'; // template id
        $fromEmail = '';  // sender Email id
        $fromName = 'Admin';             // sender Name
        $toEmail = ''; // receiver email id
 
        try {
            // template variables pass here
            $templateVars = [
                'msg' => 'test',
                'msg1' => 'test1'
            ];
 
            $storeId = $this->storeManager->getStore()->getId();
 
            $from = ['email' => $fromEmail, 'name' => $fromName];
            $this->inlineTranslation->suspend();
 
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
    }
	
}
