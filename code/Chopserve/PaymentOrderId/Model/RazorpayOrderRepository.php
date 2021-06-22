<?php

namespace Chopserve\PaymentOrderId\Model;

use Chopserve\PaymentOrderId\Api\RazorpayOrderRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteFactory;
use Razorpay\Api\Api;
use Razorpay\Magento\Model\Config;

require_once "/var/www/html/hatsun.prod/app/code/Razorpay/Razorpay/Razorpay.php";

class RazorpayOrderRepository implements RazorpayOrderRepositoryInterface
{
    private $quoteFactory;

    private $config;

    private $_objectManager;

    private $quote;

    protected $resultFactory;
    /**
     * @var mixed
     */
    private $key_id;
    /**
     * @var mixed
     */
    private $key_secret;
    /**
     * @var Api
     */
    private $rzp;
	protected $_customerRepositoryInterface;
	protected $_walletFactory;

    /**
     * RazorpayOrderRepository constructor.
     * @param QuoteFactory $quoteFactory
     * @param Config $config
     * @param ObjectManagerInterface $_objectManager
     * @param ResultFactory $resultFactory
     * @param CartInterface $quote
     */
    public function __construct(QuoteFactory $quoteFactory, Config $config, ObjectManagerInterface $_objectManager, ResultFactory $resultFactory, CartInterface $quote,\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,\Chopserve\Wallet\Model\WalletFactory $walletFactory)
    {
        $this->quoteFactory = $quoteFactory;
        $this->config = $config;
        $this->_objectManager = $_objectManager;
        $this->resultFactory = $resultFactory;
        $this->quote = $quote;
		$this->_customerRepositoryInterface = $customerRepositoryInterface;
		$this->_walletFactory = $walletFactory;
        $this->key_id = $this->config->getConfigData(Config::KEY_PUBLIC_KEY);
        $this->key_secret = $this->config->getConfigData(Config::KEY_PRIVATE_KEY);

        $this->rzp = new Api($this->key_id, $this->key_secret);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId($customerId,$use_wallet = false)
    {
        $this->quote = $this->quoteFactory->create()->loadByCustomer($customerId);
        if (!$this->quote->getId()) {
            throw new NoSuchEntityException(__('No Active Cart'));
        }
		
		if($use_wallet){
			$amountValue = $this->getGrandTotal($customerId);
			$amount = (int) (round($amountValue, 2) * 100);			
		}else{
			$amount = (int) (round($this->quote->getGrandTotal(), 2) * 100);
		}
		
        $receipt_id = $this->quote->getId();

        $payment_action = $this->config->getPaymentAction();

        $maze_version = $this->_objectManager->get('Magento\Framework\App\ProductMetadataInterface')->getVersion();
        $module_version =  $this->_objectManager->get('Magento\Framework\Module\ModuleList')->getOne('Razorpay_Magento')['setup_version'];
//        return [$maze_version];

        if ($payment_action === 'authorize') {
            $payment_capture = 0;
        } else {
            $payment_capture = 1;
        }

        $code = 400;

        try {
            $order = $this->rzp->order->create([
                'amount' => $amount,
                'receipt' => $receipt_id,
                'currency' => $this->quote->getQuoteCurrencyCode(),
                'payment_capture' => $payment_capture
            ]);

            $responseContent = [
                'message'   => 'Unable to create your order. Please contact support.',
                'parameters' => []
            ];

            if (null !== $order && !empty($order->id)) {
                $responseContent = [
                     'success'           => true,
                    'rzp_order'         => $order->id,
                    'order_id'          => $receipt_id,
                    'amount'            => $order->amount,
                    // 'amount'            => 100,
                    'quote_currency'    => $this->quote->getQuoteCurrencyCode(),
                    'quote_amount'      => round($this->quote->getGrandTotal(), 2),
                    // 'quote_amount'      => 100,
                    'maze_version'      => $maze_version,
                    'module_version'    => $module_version,



                    
                    // 'success'           => true,
                    // 'rzp_order'         => $order->id,
                    // 'order_id'          => $receipt_id,
                    // 'amount'            => $order->amount,
                    // // 'amount'            => 100,
                    // 'quote_currency'    => $this->quote->getQuoteCurrencyCode(),
                    // 'quote_amount'      => round($this->quote->getGrandTotal(), 2),
                    // // 'quote_amount'      => 100,
                    // 'maze_version'      => $maze_version,
                    // 'module_version'    => $module_version,
                ];				
                $code = 200;
			if($use_wallet){
				$module = 'checkout';
				$balance = $this->getCustomerBalance($customerId);
				$wallet_model = $this->_walletFactory->create();
				$wallet_model->setData('amount',$balance);
				$wallet_model->setData('customer_id',$customerId);
				$wallet_model->setData('rzp_order',$order->id);
				$wallet_model->setData('module',$module);
				$wallet_model->setData('is_used',0);
				$wallet_model->save();
			}
				
            }
        } catch (\Razorpay\Api\Errors\Error $e) {
            $responseContent = [
                'message'   => $e->getMessage(),
                'parameters' => []
            ];
        } catch (\Exception $e) {
            $responseContent = [
                'message'   => $e->getMessage(),
                'parameters' => []
            ];
        }

        return [$responseContent];
    }
	public function getGrandTotal($customerId){
		$cartTotal = round($this->quote->getGrandTotal(), 2);
		$customer = $this->_customerRepositoryInterface->getById($customerId);	
		$customerObj = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($customerId); 
		$walletAmount = 0;
		if ($customerObj->getId()) {
			if(null !== $customer->getCustomAttribute('wallet_amount'))
			{
			 $walletAmount = $customer->getCustomAttribute('wallet_amount')->getValue();
			}
			if($walletAmount >= $cartTotal){
				$amount = $walletAmount - $cartTotal;				
			}elseif($walletAmount < $cartTotal){
				$amount = $cartTotal - $walletAmount;		
			}
		}
		return $amount;
	}
	public function getCustomerBalance($customerId){
		$cartTotal = round($this->quote->getGrandTotal(), 2);
		$customer = $this->_customerRepositoryInterface->getById($customerId);	
		$customerObj = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($customerId); 
		$walletAmount = 0;
		$balance = 0;
		if ($customerObj->getId()) {
			if(null !== $customer->getCustomAttribute('wallet_amount'))
			{
			 $walletAmount = $customer->getCustomAttribute('wallet_amount')->getValue();
			}
			if($walletAmount >= $cartTotal){
				$amount = $walletAmount - $cartTotal;	
				$balance = $amount;
				
			}elseif($walletAmount < $cartTotal){
				$balance = 0;
			}
		}
		return $balance;
	}
	
	 /**
     * @inheritDoc
     */
    public function createOrder($param)
    {
		$resultArr = array();
		$result =  json_encode($param);
		$resultSet = json_decode($result,true);	
		$amount = (int) ($resultSet['amount']);

        //$receipt_id = $this->quote->getId();

        $payment_action = $this->config->getPaymentAction();

        $maze_version = $this->_objectManager->get('Magento\Framework\App\ProductMetadataInterface')->getVersion();
        $module_version =  $this->_objectManager->get('Magento\Framework\Module\ModuleList')->getOne('Razorpay_Magento')['setup_version'];
//        return [$maze_version];

        if ($payment_action === 'authorize') {
            $payment_capture = 0;
        } else {
            $payment_capture = 1;
        }

        $code = 400;

        try {
            $order = $this->rzp->order->create([
                'amount' => $amount,
                'currency' => 'INR',
                'payment_capture' => $payment_capture
            ]);

            $responseContent = [
                'message'   => 'Unable to create your order. Please contact support.',
                'parameters' => []
            ];

            if (null !== $order && !empty($order->id)) {
                $responseContent = [
                    'success'           => true,
                    'rzp_order'         => $order->id,
                    //  was done before
                    'order_id'          => $receipt_id,
                    'amount'            => $order->amount,
                    'quote_currency'    => 'INR',
                    'quote_amount'      => round($amount),
                    'maze_version'      => $maze_version,
                    'module_version'    => $module_version,
                ];

                $code = 200;
				$module = 'wallet';
				$wallet_model = $this->_walletFactory->create();
				$wallet_model->setData('rzp_order',$order->id);
				$wallet_model->setData('module',$module);
				$wallet_model->setData('is_used',0);
				$wallet_model->save();
            }
        } catch (\Razorpay\Api\Errors\Error $e) {
            $responseContent = [
                'message'   => $e->getMessage(),
                'parameters' => []
            ];
        } catch (\Exception $e) {
            $responseContent = [
                'message'   => $e->getMessage(),
                'parameters' => []
            ];
        }

        return [$responseContent];
		
	}
}
