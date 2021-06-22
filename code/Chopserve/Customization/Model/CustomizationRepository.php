<?php
namespace Chopserve\Customization\Model;
use Chopserve\Customization\Api\CustomizationRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
class CustomizationRepository implements CustomizationRepositoryInterface
{
	protected $orderRepo;
	protected $orderInterface;
	protected $resourceConnection;
	protected $_customizationFactory;
    public function __construct(   
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepo,
		\Magento\Sales\Api\Data\OrderInterface $orderInterface,
		ResourceConnection $resourceConnection,
		\Chopserve\Customization\Model\CustomizationFactory $customizationFactory
    ) {
		$this->orderRepo = $orderRepo;
		$this->orderInterface = $orderInterface;
		$this->resourceConnection = $resourceConnection;
		$this->_customizationFactory = $customizationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save($customization)
    {		
		$resultArr = array();
		$result =  json_encode($customization);
		$resultSet = json_decode($result,true);	
        try {
			if(isset($resultSet['customization_id']) && $resultSet['customization_id'] != ''){
				$orderId = $resultSet['customization_id'];
				$connection  = $this->resourceConnection->getConnection();
				$tableName   = $this->resourceConnection->getTableName("sales_order"); //$connection->getTableName('sales_order');
				
				$binds_orderId = array(
				'entity_id'    => $orderId,
				);	

				$orderQry = "SELECT * FROM ".$tableName." WHERE entity_id = :entity_id";
				$orderQryRes = $connection->query($orderQry,$binds_orderId);
				$orderData = $orderQryRes->fetchAll();				
				$incrementId = $orderData[0]['increment_id'];
				$customizationText = $resultSet['customization'];
				$customizeTable   = $this->resourceConnection->getTableName("chopserve_customization");
				$binds_insert_data = array(
						'customization_id'    => $incrementId,
						'customization'   => $customizationText,
					);	
				$customizeQry = "insert into ".$customizeTable."(customization_id,customization) values (:customization_id,:customization)";
					$connection->query($customizeQry,$binds_insert_data);
				
				/* $customization['customization_id'] = $incrementId;
				$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/jafar.log');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);				
				
				$customizationId = $customization['customization_id'];
				$customizationId = $incrementId;
				$logger->info('--customizationId --'.$customizationId);
				$customizationText = $customization['customization'];
				$customizationText = $resultSet['customization'];
				$logger->info('--customizationText --'.$customizationText );
				$customize_model = $this->_customizationFactory->create();
				$logger->info('--customize_model --'.json_encode($customize_model->getData()));
				$customize_model->setData('customization_id',$customizationId);
				$customize_model->setData('customization',$customizationText);
				 $customize_model->setCustomizationId($customizationId);
				$customize_model->setCustomization($customizationText); 
				$customize_model->save();
				$customize_model->save($customization); */
			}
            
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the Customization: %1',
                $exception->getMessage()
            ));
        }
        return $customization;
    }


}
