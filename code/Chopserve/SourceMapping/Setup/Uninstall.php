<?php
namespace Chopserve\SourceMapping\Setup;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * Uninstall constructor.
     * @param ResourceConnection $resource
     */
    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.Generic.CodeAnalysis.UnusedFunctionParameter)
     */
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        //remove ui bookmark data
        $this->resource->getConnection()->delete(
            $this->resource->getTableName('ui_bookmark'),
            [
                'namespace IN (?)' => [
                    'chopserve_sourcemapping_mapping_listing',
                ]
            ]
        );
        //remove config data
        $this->resource->getConnection()->delete(
            $this->resource->getTableName('core_config_data'),
            [
                'path LIKE ?' => 'chopserve_source_mapping_%'
            ]
        );
        if ($setup->tableExists('chopserve_source_mapping_mapping')) {
            $setup->getConnection()->dropTable('chopserve_source_mapping_mapping');
        }
    }
}
