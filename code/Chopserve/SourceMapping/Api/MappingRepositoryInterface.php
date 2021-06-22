<?php
namespace Chopserve\SourceMapping\Api;

use Chopserve\SourceMapping\Api\Data\MappingInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface MappingRepositoryInterface
{
    /**
     * @param MappingInterface $Mapping
     * @return MappingInterface
     */
    public function save(MappingInterface $Mapping);

    /**
     * @param int $mappingId
     * @return MappingInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($mappingId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Chopserve\SourceMapping\Api\Data\MappingSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param MappingInterface $Mapping
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(MappingInterface $Mapping);

    /**
     * @param int $MappingId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($MappingId);

    /**
     * clear caches instances
     * @return void
     */
    public function clear();

    /**
     * @param int $pincode
     * @return \Chopserve\SourceMapping\Api\Data\MappingInterface[]
     */
    public function getSourcePinCode($pincode);
}
