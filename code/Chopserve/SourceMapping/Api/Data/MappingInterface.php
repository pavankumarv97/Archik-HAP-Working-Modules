<?php
namespace Chopserve\SourceMapping\Api\Data;

/**
 * @api
 */
interface MappingInterface
{
    const MAPPING_ID = 'mapping_id';
    const SOURCE_PINCODE = 'source_pincode';
    const PINCODES = 'pincodes';
    /**
     * @var string
     */
    const IS_ACTIVE = 'is_active';
    /**
     * @var int
     */
    const STATUS_ENABLED = 1;
    /**
     * @var int
     */
    const STATUS_DISABLED = 2;
    /**
     * @param int $id
     * @return MappingInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return MappingInterface
     */
    public function setMappingId($id);

    /**
     * @return int
     */
    public function getMappingId();

    /**
     * @param string $sourcePincode
     * @return MappingInterface
     */
    public function setSourcePincode($sourcePincode);

    /**
     * @return string
     */
    public function getSourcePincode();
    /**
     * @param string $pincodes
     * @return MappingInterface
     */
    public function setPincodes($pincodes);

    /**
     * @return string
     */
    public function getPincodes();
    /**
     * @param int $isActive
     * @return MappingInterface
     */
    public function setIsActive($isActive);

    /**
     * @return int
     */
    public function getIsActive();
}
