<?php
namespace Chopserve\SourceMapping\Model\ResourceModel;

class Mapping extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('chopserve_source_mapping_mapping', 'mapping_id');
    }
}
