<?php
namespace Chopserve\SourceMapping\Ui\Provider;

interface CollectionProviderInterface
{
    /**
     * @return \Chopserve\SourceMapping\Model\ResourceModel\AbstractCollection
     */
    public function getCollection();
}
