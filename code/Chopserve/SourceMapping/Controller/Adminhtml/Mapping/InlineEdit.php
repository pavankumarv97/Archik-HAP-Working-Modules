<?php
namespace Chopserve\SourceMapping\Controller\Adminhtml\Mapping;

use Chopserve\SourceMapping\Api\MappingRepositoryInterface;
use Chopserve\SourceMapping\Api\Data\MappingInterface;
use Chopserve\SourceMapping\Model\ResourceModel\Mapping as MappingResourceModel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class InlineEdit
 */
class InlineEdit extends Action
{
    /**
     * Source Mapping repository
     * @var MappingRepositoryInterface
     */
    protected $mappingRepository;
    /**
     * Data object processor
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * Data object helper
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * JSON Factory
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * Source Mapping resource model
     * @var MappingResourceModel
     */
    protected $mappingResourceModel;

    /**
     * constructor
     * @param Context $context
     * @param MappingRepositoryInterface $mappingRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param JsonFactory $jsonFactory
     * @param MappingResourceModel $mappingResourceModel
     */
    public function __construct(
        Context $context,
        MappingRepositoryInterface $mappingRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        JsonFactory $jsonFactory,
        MappingResourceModel $mappingResourceModel
    ) {
        $this->mappingRepository = $mappingRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->jsonFactory = $jsonFactory;
        $this->mappingResourceModel = $mappingResourceModel;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $mappingId) {
            /** @var \Chopserve\SourceMapping\Model\Mapping|\Chopserve\SourceMapping\Api\Data\MappingInterface $mapping */
            try {
                $mapping = $this->mappingRepository->get((int)$mappingId);
                $mappingData = $postItems[$mappingId];
                $this->dataObjectHelper->populateWithArray($mapping, $mappingData, MappingInterface::class);
                $this->mappingResourceModel->saveAttribute($mapping, array_keys($mappingData));
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithMappingId($mapping, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithMappingId($mapping, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithMappingId(
                    $mapping,
                    __('Something went wrong while saving the Source Mapping.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Source Mapping id to error message
     *
     * @param \Chopserve\SourceMapping\Api\Data\MappingInterface $mapping
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithMappingId(MappingInterface $mapping, $errorText)
    {
        return '[Source Mapping ID: ' . $mapping->getId() . '] ' . $errorText;
    }
}
