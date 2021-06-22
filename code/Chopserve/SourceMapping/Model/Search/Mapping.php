<?php
namespace Chopserve\SourceMapping\Model\Search;

use Magento\Framework\DataObject;
use Magento\Backend\Helper\Data;
use Chopserve\SourceMapping\Model\ResourceModel\Mapping\CollectionFactory;

/**
 * @api
 */
class Mapping extends DataObject
{
    /**
     * Adminhtml data
     *
     * @var Data
     */
    protected $adminhtmlData = null;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Data $adminhtmlData
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Data $adminhtmlData
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->adminhtmlData = $adminhtmlData;
    }

    /**
     * Load search results
     *
     * @return $this
     */
    public function load()
    {
        $result = [];
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($result);
            return $this;
        }

        $query = $this->getQuery();
        $collection = $this->collectionFactory->create()->addFieldToFilter(
            'source_pincode',
            ['like' => '%' . $query . '%']
        )->setCurPage(
            $this->getStart()
        )->setPageSize(
            $this->getLimit()
        )->load();

        foreach ($collection as $item) {
            $result[] = [
                'id' => 'mapping' . $item->getId(),
                'type' => __('Source Mapping'),
                'name' => __('Source Mapping %1', $item->getSourcePincode()),
                'description' => __('Source Mapping %1', $item->getSourcePincode()),
                'url' => $this->adminhtmlData->getUrl(
                    'chopserve_source_mapping/mapping/edit',
                    ['mapping_id' => $item->getId()]
                ),
            ];
        }

        $this->setResults($result);

        return $this;
    }
}
