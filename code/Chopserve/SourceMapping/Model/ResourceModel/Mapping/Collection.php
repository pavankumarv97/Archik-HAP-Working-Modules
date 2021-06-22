<?php
namespace Chopserve\SourceMapping\Model\ResourceModel\Mapping;

use Chopserve\SourceMapping\Model\Mapping;
use Chopserve\SourceMapping\Model\ResourceModel\AbstractCollection;

/**
 * @api
 */
class Collection extends AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            Mapping::class,
            \Chopserve\SourceMapping\Model\ResourceModel\Mapping::class
        );
    }
}
