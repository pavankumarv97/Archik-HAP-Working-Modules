<?php

namespace Hatsun\CustomerViewProduct\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Db\Ddl\Table;


class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup , ModuleContextInterface $context)
    {

        $setup->startSetup();
        $table = $setup->getConnection()->newTable(
            $setup->getTable('recently_viewed_product')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity'=>true, 'nullable'=>false,'primary'=>true],
                'ID'
            )->addColumn(
                'product_id',
                Table::TYPE_TEXT,
                null,
                ['nullable'=>false],
                'PRODUCT ID'
            )->addColumn(
                'customer_id',
                Table::TYPE_TEXT,
                null,
                ['nullable'=>false],
                'CUSTOMER ID'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable'=>false,'default'=>Table::TIMESTAMP_INIT],
                'CREATED AT'
            )->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable'=>false,'default'=>Table::TIMESTAMP_INIT_UPDATE],
                'UPDATED AT'
            )->setComment(
                'Recently Viwed Product Table'
            );

            $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }

}
