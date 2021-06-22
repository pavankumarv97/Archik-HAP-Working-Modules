<?php
namespace Chopserve\SourceMapping\Api\Data;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface MappingSearchResultInterface
{
    /**
     * get items
     *
     * @return \Chopserve\SourceMapping\Api\Data\MappingInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Chopserve\SourceMapping\Api\Data\MappingInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $count
     * @return $this
     */
    public function setTotalCount($count);
}
