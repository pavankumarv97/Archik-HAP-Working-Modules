<?php

namespace Hatsun\CustomRazorpay\Setup;

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
            $setup->getTable('custom_razorpay')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity'=>true, 'nullable'=>false,'primary'=>true],
                'ID'
            )->addColumn(
                'rzp_order_id',
                 Table::TYPE_TEXT,
                 255,
                 ['nullable'=>false],
                 'Rzp Order Id'
            )->addColumn(
                 'rzp_payment_id',
                  Table::TYPE_TEXT,
                  255,
                  ['nullable'=>false],
                  'Rzp Payment Id '
            )->addColumn(
                'rzp_signature',
                 Table::TYPE_TEXT,
                 255,
                 ['nullable'=>false],
                 'Rzp Signature'
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
                'Custom Razorpay'
            );

            $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }

}
