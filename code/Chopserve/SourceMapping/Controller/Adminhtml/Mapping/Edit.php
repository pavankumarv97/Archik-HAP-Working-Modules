<?php
namespace Chopserve\SourceMapping\Controller\Adminhtml\Mapping;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Chopserve\SourceMapping\Api\MappingRepositoryInterface;

class Edit extends Action
{
    /**
     * @var MappingRepositoryInterface
     */
    private $mappingRepository;
    /**
     * @var Registry
     */
    private $registry;

    /**
     * Edit constructor.
     * @param Context $context
     * @param MappingRepositoryInterface $mappingRepository
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        MappingRepositoryInterface $mappingRepository,
        Registry $registry
    ) {
        $this->mappingRepository = $mappingRepository;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * get current Source Mapping
     *
     * @return null|\Chopserve\SourceMapping\Api\Data\MappingInterface
     */
    private function initMapping()
    {
        $mappingId = $this->getRequest()->getParam('mapping_id');
        try {
            $mapping = $this->mappingRepository->get($mappingId);
        } catch (NoSuchEntityException $e) {
            $mapping = null;
        }
        $this->registry->register('current_mapping', $mapping);
        return $mapping;
    }

    /**
     * Edit or create Source Mapping
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $mapping = $this->initMapping();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Chopserve_SourceMapping::sourcemapping_mapping');
        $resultPage->getConfig()->getTitle()->prepend(__('Source Mapping'));

        if ($mapping === null) {
            $resultPage->getConfig()->getTitle()->prepend(__('New Source Mapping'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend($mapping->getSourcePincode());
        }
        return $resultPage;
    }
}
