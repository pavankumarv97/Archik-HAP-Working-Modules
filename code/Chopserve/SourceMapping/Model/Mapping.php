<?php
namespace Chopserve\SourceMapping\Model;

use Chopserve\SourceMapping\Api\Data\MappingInterface;
use Magento\Framework\Model\AbstractModel;
use Chopserve\SourceMapping\Model\ResourceModel\Mapping as MappingResourceModel;

/**
 * @method \Chopserve\SourceMapping\Model\ResourceModel\Mapping _getResource()
 * @method \Chopserve\SourceMapping\Model\ResourceModel\Mapping getResource()
 */
class Mapping extends AbstractModel implements MappingInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'chopserve_sourcemapping_mapping';
    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'chopserve_sourcemapping_mapping';
    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'mapping';
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(MappingResourceModel::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get Page id
     *
     * @return array
     */
    public function getMappingId()
    {
        return $this->getData(MappingInterface::MAPPING_ID);
    }

    /**
     * set Source Mapping id
     *
     * @param  int $mappingId
     * @return MappingInterface
     */
    public function setMappingId($mappingId)
    {
        return $this->setData(MappingInterface::MAPPING_ID, $mappingId);
    }

    /**
     * @param string $sourcePincode
     * @return MappingInterface
     */
    public function setSourcePincode($sourcePincode)
    {
        return $this->setData(MappingInterface::SOURCE_PINCODE, $sourcePincode);
    }

    /**
     * @return string
     */
    public function getSourcePincode()
    {
        return $this->getData(MappingInterface::SOURCE_PINCODE);
    }

    /**
     * @param string $pincodes
     * @return MappingInterface
     */
    public function setPincodes($pincodes)
    {
        return $this->setData(MappingInterface::PINCODES, $pincodes);
    }

    /**
     * @return string
     */
    public function getPincodes()
    {
        return $this->getData(MappingInterface::PINCODES);
    }

    /**
     * @param int $isActive
     * @return MappingInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(MappingInterface::IS_ACTIVE, $isActive);
    }

    /**
     * @return int
     */
    public function getIsActive()
    {
        return $this->getData(MappingInterface::IS_ACTIVE);
    }
}
