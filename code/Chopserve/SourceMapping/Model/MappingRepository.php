<?php
namespace Chopserve\SourceMapping\Model;

use Chopserve\SourceMapping\Api\Data\MappingInterface;
use Chopserve\SourceMapping\Api\Data\MappingInterfaceFactory;
use Chopserve\SourceMapping\Api\Data\MappingSearchResultInterfaceFactory;
use Chopserve\SourceMapping\Api\MappingRepositoryInterface;
use Chopserve\SourceMapping\Model\ResourceModel\Mapping as MappingResourceModel;
use Chopserve\SourceMapping\Model\ResourceModel\Mapping\Collection;
use Chopserve\SourceMapping\Model\ResourceModel\Mapping\CollectionFactory as MappingCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;

class MappingRepository implements MappingRepositoryInterface
{
    /**
     * Cached instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Source Mapping resource model
     *
     * @var MappingResourceModel
     */
    protected $resource;

    /**
     * Source Mapping collection factory
     *
     * @var MappingCollectionFactory
     */
    protected $mappingCollectionFactory;

    /**
     * Source Mapping interface factory
     *
     * @var MappingInterfaceFactory
     */
    protected $mappingInterfaceFactory;

    /**
     * Data Object Helper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Search result factory
     *
     * @var MappingSearchResultInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * constructor
     * @param MappingResourceModel $resource
     * @param MappingCollectionFactory $mappingCollectionFactory
     * @param MappingnterfaceFactory $mappingInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param MappingSearchResultInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        MappingResourceModel $resource,
        MappingCollectionFactory $mappingCollectionFactory,
        MappingInterfaceFactory $mappingInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        MappingSearchResultInterfaceFactory $searchResultsFactory
    ) {
        $this->resource             = $resource;
        $this->mappingCollectionFactory = $mappingCollectionFactory;
        $this->mappingInterfaceFactory  = $mappingInterfaceFactory;
        $this->dataObjectHelper     = $dataObjectHelper;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * Save Source Mapping.
     *
     * @param \Chopserve\SourceMapping\Api\Data\MappingInterface $mapping
     * @return \Chopserve\SourceMapping\Api\Data\MappingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(MappingInterface $mapping)
    {
        /** @var MappingInterface|\Magento\Framework\Model\AbstractModel $mapping */
        try {
            $this->resource->save($mapping);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the Source Mapping: %1',
                $exception->getMessage()
            ));
        }
        return $mapping;
    }

    /**
     * Retrieve Source Mapping
     *
     * @param int $mappingId
     * @return \Chopserve\SourceMapping\Api\Data\MappingInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($mappingId)
    {
        if (!isset($this->instances[$mappingId])) {
            /** @var MappingInterface|\Magento\Framework\Model\AbstractModel $mapping */
            $mapping = $this->mappingInterfaceFactory->create();
            $this->resource->load($mapping, $mappingId);
            if (!$mapping->getId()) {
                throw new NoSuchEntityException(__('Requested Source Mapping doesn\'t exist'));
            }
            $this->instances[$mappingId] = $mapping;
        }
        return $this->instances[$mappingId];
    }

    /**
     * Retrieve Source Mapping matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Chopserve\SourceMapping\Api\Data\MappingSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Chopserve\SourceMapping\Api\Data\MappingSearchResultInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Chopserve\SourceMapping\Model\ResourceModel\Mapping\Collection $collection */
        $collection = $this->mappingCollectionFactory->create();

        //Add filters from root filter group to the collection
        /** @var \Magento\Framework\Api\Search\FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();
        /** @var SortOrder $sortOrder */
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? SortOrder::SORT_ASC : SortOrder::SORT_DESC
                );
            }
        } else {
            $collection->addOrder('main_table.' . MappingInterface::MAPPING_ID, SortOrder::SORT_ASC);
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var MappingInterface[] $mappings */
        $mappings = [];
        /** @var \Chopserve\SourceMapping\Model\Mapping $mapping */
        foreach ($collection as $mapping) {
            /** @var MappingInterface $mappingDataObject */
            $mappingDataObject = $this->mappingInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $mappingDataObject,
                $mapping->getData(),
                MappingInterface::class
            );
            $mappings[] = $mappingDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($mappings);
    }

    /**
     * Delete Source Mapping
     *
     * @param MappingInterface $mapping
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(MappingInterface $mapping)
    {
        /** @var MappingInterface|\Magento\Framework\Model\AbstractModel $mapping */
        $id = $mapping->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($mapping);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to removeSource Mapping %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete Source Mapping by ID.
     *
     * @param int $mappingId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($mappingId)
    {
        $mapping = $this->get($mappingId);
        return $this->delete($mapping);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        Collection $collection
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
        return $this;
    }

    /**
     * clear caches instances
     * @return void
     */
    public function clear()
    {
        $this->instances = [];
    }

    /**
     * @param int $pincode
     * @return \Chopserve\SourceMapping\Api\Data\MappingInterface[]
     */
    public function getSourcePinCode($pincode)
    {
        return $this->getSourcePostcode($pincode);
    }

    private function getSourcePostcode($postcode)
    {
        $collection = $this->mappingCollectionFactory->create()->getItems();
        foreach ($collection as $item) {
            $desPostcodes = explode(",", $item['pincodes']);
            foreach ($desPostcodes as $desPostcode) {
                if ($desPostcode == $postcode) {
                    return [$item];
                }
            }
        }
        return ["no store found"];
    }
}
