<?php

namespace Tutorial\SimpleNews\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class News extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('tutorial_simplenews', 'id');
    }
}