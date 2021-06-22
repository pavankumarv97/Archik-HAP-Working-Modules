<?php

namespace Chopserve\SourceMapping\Block\Mapping;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * @api
 */
class ViewMapping extends Template
{
    /**
     * @var Registry
     */
    private $coreRegistry;
    /**
     * @param Context $context
     * @param Registry $registry
     * @param $imageBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * get current Source Mapping
     *
     * @return \Chopserve\SourceMapping\Api\Data\MappingInterface
     */
    public function getCurrentMapping()
    {
        return $this->coreRegistry->registry('current_mapping');
    }
}
