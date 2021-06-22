<?php

namespace Chopserve\SourceMapping\Model\Mapping;

use Magento\Framework\UrlInterface;
use Chopserve\SourceMapping\Api\Data\MappingInterface;

class Url
{
    /**
     * url builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return string
     */
    public function getListUrl()
    {
        return $this->urlBuilder->getUrl('chopserve_source_mapping/mapping/index');
    }

    /**
     * @param MappingInterface $mapping
     * @return string
     */
    public function getMappingUrl(MappingInterface $mapping)
    {
        return $this->urlBuilder->getUrl('chopserve_source_mapping/mapping/view', ['id' => $mapping->getId()]);
    }
}
