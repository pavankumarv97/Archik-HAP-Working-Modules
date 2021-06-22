<?php

namespace Tutorial\SimpleNews\Model\ResourceModel\News;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Tutorial\SimpleNews\Model\News',
            'Tutorial\SimpleNews\Model\ResourceModel\News'
        );
    }
}