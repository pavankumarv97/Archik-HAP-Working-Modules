<?php

namespace Chopserve\CancelOrder\Model;

use Chopserve\CancelOrder\Api\CancelOrderRepositoryInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\OrderRepository;
use Hatsun\DunzoIntegration\Model\DunzoRepository;
use Hatsun\CustomRazorpay\Model\CustomeRazorpayPaymentFactory;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Service\CreditmemoService;
use Chopserve\SocialLogin\Model\SocialLoginRepository;

class CancelOrderRepository implements CancelOrderRepositoryInterface
{
    protected $orderManagement;
    protected $dunzoRepository;
    protected $customeRazorpayPaymentFactory;
    protected $creditMemoFactory;
    protected $creditMemoService;
    protected $invoice;
    protected $order;
    protected $orderMo;
    protected $socialLoginRepository;

    public function __construct(
       OrderManagementInterface $orderManagement,
       DunzoRepository $dunzoRepository,
	   OrderRepository $order,
       CustomeRazorpayPaymentFactory $customeRazorpayPaymentFactory,
       CreditmemoFactory $creditMemoFactory,
       CreditmemoService $creditMemoService,
       Invoice $invoice,
       Order $orderMo,
       SocialLoginRepository $socialLoginRepository
    ) {
        $this->orderManagement = $orderManagement;
        $this->order = $order;
        $this->dunzoRepository = $dunzoRepository;
        $this->customeRazorpayPaymentFactory = $customeRazorpayPaymentFactory;
        $this->creditMemoFactory = $creditMemoFactory;
        $this->creditMemoService = $creditMemoService;
        $this->invoice = $invoice;
        $this->orderMo = $orderMo;
        $this->socialLoginRepository = $socialLoginRepository;
    }

    /**
     * @param $orderId
     * @return mixed|void
     */
    public function cancelOrder($orderId)
    {
        $this->orderManagement->cancel($orderId);
        return $orderId;
    }

    /**
     * @param mixed $param
     * @return array
     */
    public function cancellation($param){
    $timings =  $this->socialLoginRepository->getTimeToCancel();
	$order_status = $this->order->get($param['order_id'])->getStatus();
	$order_creation_time = $this->order->get($param['order_id'])->getCreatedAt();
  	$newtimestamp = strtotime($order_creation_time. ' + '.$timings.' minute');
    $newdate = date('Y-m-d H:i:s', $newtimestamp);
	$currentdate = date('Y-m-d H:i:s');
	try{
		if($currentdate > $newdate){
			echo 'Order cannot be cancelled.';die();
		}else{
			if($order_status == 'pending' || $order_status == 'processing'){
                // Dunzo order cancellation
                $task_id = $param['task_id'];                
                if(isset($task_id)){
                    $dunzoCancellation = $this->dunzoRepository->cancel($task_id , $param['cancellation']);
                    //check fr method
                    $orderMo = $this->orderMo->load($param['order_id']);
                    $method = $orderMo->getPayment()->getAdditionalInformation("method_title");
                    if($method == 'Razorpay'){
                        //razorpay
                        $quote_id = $this->order->get($param['order_id'])->getQuoteId();
                        $updateFactory = $this->customeRazorpayPaymentFactory->create()->load($quote_id,"quoteId");
                        $rzp_payment_id = $updateFactory->getRzpPaymentId();
                        //raise a refund
                        $datap = [];
                        $curl = curl_init("https://api.razorpay.com/v1/payments/".$rzp_payment_id."/refund");
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($datap));
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                        curl_setopt($curl, CURLOPT_USERPWD, "rzp_live_Vz8uIp1N7EfRBF:gloEUqmhsls7QuSpshv2kf1W");
                        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
                        $response = curl_exec($curl);
                        curl_close($curl);
                        print_r($response);
                        //create a memo
                        $invoices = $orderMo->getInvoiceCollection();
                        foreach ($invoices as $invoice) {
                            $invoiceincrementid = $invoice->getIncrementId();
                        }
                        $invoiceobj = $this->invoice->loadByIncrementId($invoiceincrementid);
                        $creditmemo = $this->creditMemoFactory->createByOrder($orderMo);
                        // Don't set invoice if you want to do offline refund
                        $creditmemo->setInvoice($invoiceobj);
                        $this->creditMemoService->refund($creditmemo); 
                    }else{
                        //COD
                        $this->orderManagement->cancel($param['order_id']);
                        echo 'Order cancelled.';
                    }
                    $email = $orderMo->getEmail();
                    $emailMessage = $this->socialLoginRepository->sendMailToUsers($email,$param['order_id'],'cancel_email_template');
                    die();
                }
			}elseif($order_status == 'canceled'){
				echo 'Order already cancelled.';die();
			}else{
				echo 'Order cannot be cancelled.';die();
			}

		}
	}catch(Exception $e){
		echo $e;
	}
    }

}