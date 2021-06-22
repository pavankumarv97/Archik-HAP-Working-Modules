<?php

namespace Hatsun\CustomeAddressLatAndLong\Setup;

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
            $setup->getTable('custome_latitude_longitude')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity'=>true, 'nullable'=>false,'primary'=>true],
                'ID'
            )->addColumn(
                'latitude',
                 Table::TYPE_TEXT,
                 255,
                 ['nullable'=>false],
                 'Latitude'
            )->addColumn(
                 'longitude',
                  Table::TYPE_TEXT,
                  255,
                  ['nullable'=>false],
                  'Longitude '
            )->addColumn(
                  'customerId',
                   Table::TYPE_INTEGER,
                   255,
                   ['nullable'=>false],
                   'Customer Id'
            )->addColumn(
                'storeId',
                 Table::TYPE_INTEGER,
                 255,
                 ['nullable'=>false],
                 'Store Id'
          )->addColumn(
                 'quoteId',
                  Table::TYPE_INTEGER,
                  255,
                 ['nullable'=>false],
                  'Quote Id'
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
                'Custome Latitude Longitude'
            );

            $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }

}
