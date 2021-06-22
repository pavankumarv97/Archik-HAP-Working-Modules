<?php
namespace Chopserve\SourceMapping\Block\Adminhtml\Button\Mapping;

use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete implements ButtonProviderInterface
{
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * Delete constructor.
     * @param Registry $registry
     * @param UrlInterface $url
     */
    public function __construct(Registry $registry, UrlInterface $url)
    {
        $this->registry = $registry;
        $this->url = $url;
    }

    /**
     * get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getMappingId()) {
            $data = [
                'label' => __('Delete Source Mapping'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return \Chopserve\SourceMapping\Api\Data\MappingInterface | null
     */
    private function getMapping()
    {
        return $this->registry->registry('current_mapping');
    }

    /**
     * @return int|null
     */
    private function getMappingId()
    {
        $mapping = $this->getMapping();
        return ($mapping) ? $mapping->getId() : null;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->url->getUrl(
            '*/*/delete',
            [
                'mapping_id' => $this->getmappingId()
            ]
        );
    }
}
