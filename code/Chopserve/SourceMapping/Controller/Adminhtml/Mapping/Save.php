<?php
namespace Chopserve\SourceMapping\Controller\Adminhtml\Mapping;

use Chopserve\SourceMapping\Api\MappingRepositoryInterface;
use Chopserve\SourceMapping\Api\Data\MappingInterface;
use Chopserve\SourceMapping\Api\Data\MappingInterfaceFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;

/**
 * Class Save
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends Action
{
    /**
     * Source Mapping factory
     * @var MappingInterfaceFactory
     */
    protected $mappingFactory;
    /**
     * Data Object Processor
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * Data Object Helper
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * Data Persistor
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * Core registry
     * @var Registry
     */
    protected $registry;
    /**
     * Source Mapping repository
     * @var MappingRepositoryInterface
     */
    protected $mappingRepository;

    /**
     * Save constructor.
     * @param Context $context
     * @param MappingInterfaceFactory $mappingFactory
     * @param MappingRepositoryInterface $mappingRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        MappingInterfaceFactory $mappingFactory,
        MappingRepositoryInterface $mappingRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        Registry $registry
    ) {
        $this->mappingFactory = $mappingFactory;
        $this->mappingRepository = $mappingRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * run the action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var MappingInterface $mapping */
        $mapping = null;
        $postData = $this->getRequest()->getPostValue();
        $data = $postData;
        $id = !empty($data['mapping_id']) ? $data['mapping_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($id) {
                $mapping = $this->mappingRepository->get((int)$id);
            } else {
                unset($data['mapping_id']);
                $mapping = $this->mappingFactory->create();
            }
            $this->dataObjectHelper->populateWithArray($mapping, $data, MappingInterface::class);
            $this->mappingRepository->save($mapping);
            $this->messageManager->addSuccessMessage(__('You saved the Source Mapping'));
            $this->dataPersistor->clear('chopserve_source_mapping_mapping');
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('*/*/edit', ['mapping_id' => $mapping->getId()]);
            } else {
                $resultRedirect->setPath('*/*');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('chopserve_source_mapping_mapping', $postData);
            $resultRedirect->setPath('*/*/edit', ['mapping_id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the Source Mapping'));
            $this->dataPersistor->set('chopserve\source_mapping_mapping', $postData);
            $resultRedirect->setPath('*/*/edit', ['mapping_id' => $id]);
        }
        return $resultRedirect;
    }
}
