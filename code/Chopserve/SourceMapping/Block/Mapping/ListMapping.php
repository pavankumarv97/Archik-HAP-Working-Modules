<?php

namespace Chopserve\SourceMapping\Block\Mapping;

use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Theme\Block\Html\Pager;
use Chopserve\SourceMapping\Api\Data\MappingInterface;
use Chopserve\SourceMapping\Model\Mapping;
use Chopserve\SourceMapping\Model\ResourceModel\Mapping\CollectionFactory as MappingCollectionFactory;
use Chopserve\SourceMapping\Model\Mapping\Url;

/**
 * @api
 */
class ListMapping extends Template
{
    /**
     * @var MappingCollectionFactory
     */
    private $mappingCollectionFactory;
    /**
     * @var \Chopserve\SourceMapping\Model\ResourceModel\Mapping\Collection
     */
    private $mappings;
    /**
     * @var Url
     */
    private $urlModel;
    /**
     * @param Context $context
     * @param MappingCollectionFactory $mappingCollectionFactory
     * @param Url $urlModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        MappingCollectionFactory $mappingCollectionFactory,
        Url $urlModel,
        array $data = []
    ) {
        $this->mappingCollectionFactory = $mappingCollectionFactory;
        $this->urlModel = $urlModel;
        parent::__construct($context, $data);
    }

    /**
     * @return \Chopserve\SourceMapping\Model\ResourceModel\Mapping\Collection
     */
    public function getMappings()
    {
        if (is_null($this->mappings)) {
            $this->mappings = $this->mappingCollectionFactory->create()
                ->addFieldToFilter('is_active', MappingInterface::STATUS_ENABLED)
                ->setOrder('source_pincode', 'ASC');
        }
        return $this->mappings;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var \Magento\Theme\Block\Html\Pager $pager */
        $pager = $this->getLayout()->createBlock(Pager::class, 'chopserve.source_mapping.mapping.list.pager');
        $pager->setCollection($this->getMappings());
        $this->setChild('pager', $pager);
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param MappingInterface $mapping
     * @return string
    */
    public function getMappingUrl(MappingInterface $mapping)
    {
        return $this->urlModel->getMappingUrl($mapping);
    }
}
