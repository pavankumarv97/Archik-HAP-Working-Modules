<?php
namespace Chopserve\SourceMapping\Controller\Mapping;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\View\Result\Page;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Chopserve\SourceMapping\Api\MappingRepositoryInterface;
use Chopserve\SourceMapping\Model\Mapping\Url as UrlModel;

class View extends Action
{
    /**
     * @var string
     */
    const BREADCRUMBS_CONFIG_PATH = 'chopserve_source_mapping/mapping/breadcrumbs';
    /**
     * @var \Chopserve\SourceMapping\Api\MappingRepositoryInterface
     */
    protected $mappingRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Chopserve\SourceMapping\Model\Mapping\Url
     */
    protected $urlModel;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param Context $context
     * @param MappingRepositoryInterface $mappingRepository
     * @param Registry $coreRegistry
     * @param UrlModel $urlModel
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        MappingRepositoryInterface $mappingRepository,
        Registry $coreRegistry,
        UrlModel $urlModel,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->mappingRepository = $mappingRepository;
        $this->coreRegistry = $coreRegistry;
        $this->urlModel = $urlModel;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $mappingId = (int)$this->getRequest()->getParam('id');
            $mapping = $this->mappingRepository->get($mappingId);

            if (!$mapping->getIsActive()) {
                throw new \Exception();
            }
        } catch (\Exception $e) {
            /** @var Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultForward->forward('noroute');
            return $resultForward;
        }

        $this->coreRegistry->register('current_mapping', $mapping);
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set($mapping->getSourcePincode());
        $pageMainTitle = $resultPage->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle && $pageMainTitle instanceof \Magento\Theme\Block\Html\Title) {
            $pageMainTitle->setPageTitle($mapping->getSourcePincode());
        }
        if ($this->scopeConfig->isSetFlag(self::BREADCRUMBS_CONFIG_PATH, ScopeInterface::SCOPE_STORE)) {
            /** @var \Magento\Theme\Block\Html\Breadcrumbs $breadcrumbsBlock */
            $breadcrumbsBlock = $resultPage->getLayout()->getBlock('breadcrumbs');
            if ($breadcrumbsBlock) {
                $breadcrumbsBlock->addCrumb(
                    'home',
                    [
                        'label' => __('Home'),
                        'link'  => $this->_url->getUrl('')
                    ]
                );
                $breadcrumbsBlock->addCrumb(
                    'Mappings',
                    [
                        'label' => __('Source Mapping'),
                        'link'  => $this->urlModel->getListUrl()
                    ]
                );
                $breadcrumbsBlock->addCrumb(
                    'mapping-' . $mapping->getId(),
                    [
                        'label' => $mapping->getSourcePincode()
                    ]
                );
            }
        }
        return $resultPage;
    }
}
