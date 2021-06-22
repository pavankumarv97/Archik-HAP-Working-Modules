<?php
namespace Chopserve\SourceMapping\Model\Mapping;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Chopserve\SourceMapping\Model\ResourceModel\Mapping\CollectionFactory as MappingCollectionFactory;

class DataProvider extends AbstractDataProvider
{
    /**
     * Loaded data cache
     *
     * @var array
     */
    protected $loadedData;

    /**
     * Data persistor
     *
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param MappingCollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        MappingCollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \Chopserve\SourceMapping\Model\Mapping $mapping */
        foreach ($items as $mapping) {
            $this->loadedData[$mapping->getId()] = $mapping->getData();
        }
        $data = $this->dataPersistor->get('chopserve_source_mapping_mapping');
        if (!empty($data)) {
            $mapping = $this->collection->getNewEmptyItem();
            $mapping->setData($data);
            $this->loadedData[$mapping->getId()] = $mapping->getData();
            $this->dataPersistor->clear('chopserve_source_mapping_mapping');
        }
        return $this->loadedData;
    }
}
