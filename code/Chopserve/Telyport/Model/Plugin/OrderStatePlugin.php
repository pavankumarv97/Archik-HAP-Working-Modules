<?php
namespace Chopserve\Telyport\Model\Plugin;

use Magento\InventoryShipping\Model\ResourceModel\ShipmentSource\GetSourceCodeByShipmentId;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
class OrderStatePlugin
{
	protected $_logger;
	protected $_httpClientFactory;
	protected $_telyportFactory;
	protected $orderRepository;
	protected $_customerRepository;
	protected $sourceRepository;
    public function __construct(
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
		\Chopserve\Telyport\Model\TelyportFactory $telyportFactory,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		GetSourceCodeByShipmentId $sourceCodeByShipmentId,
		SourceRepositoryInterface $sourceRepository
    ) {
        $this->_logger = $logger;
		$this->_httpClientFactory   = $httpClientFactory;
		$this->_telyportFactory = $telyportFactory;
		$this->orderRepository = $orderRepository;
		$this->_customerRepository = $customerRepository;
		$this->sourceCodeByShipmentId = $sourceCodeByShipmentId;
		$this->sourceRepository = $sourceRepository;
    }
	public function afterSave(
		\Magento\Sales\Api\OrderRepositoryInterface $subject,
		$result
	) {
		$this->_logger->info('--state-'.$result->getState());
		$this->_logger->info('--status-'.$result->getStatus());
		
		return $result;
	}
}