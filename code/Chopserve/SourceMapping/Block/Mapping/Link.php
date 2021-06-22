<?php

namespace Chopserve\SourceMapping\Block\Mapping;

use Magento\Framework\View\Element\Html\Link\Current;
use Chopserve\SourceMapping\Model\Mapping\Url;
use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * @api
 */
class Link extends Current
{
    /**
     * @var Url
     */
    private $urlModel;

    /**
     * Constructor
     *
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     * @param array $data
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        Url $urlModel,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->urlModel = $urlModel;
    }

    /**
     * Get href URL
     *
     * @return string
     */
    public function getHref()
    {
        return $this->urlModel->getListUrl();
    }

    /**
     * Check if link leads to URL equivalent to URL of currently displayed page
     *
     * @return bool
     */
    public function isCurrent()
    {
        return $this->getCurrent() || $this->getRequest()->getFullActionName() == $this->getSpecialLayoutHandle();
    }

    /**
     * @return string
     */
    protected function getSpecialLayoutHandle()
    {
        return 'chopserve_source_mapping_mapping_index';
    }
}
