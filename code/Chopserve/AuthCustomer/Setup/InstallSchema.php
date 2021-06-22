<?php

namespace Chopserve\AuthCustomer\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * @inheritDoc
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable(
            $setup->getTable('auth_customer_otp')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity'=>true, 'nullable'=> false, 'primary'=>true],
            'Otp Id'
        )->addColumn(
            'customer_number',
            Table::TYPE_TEXT,
            255,
            ['nullable'=>false],
            'Customer Mobile number'
        )->addColumn(
            'otp',
            Table::TYPE_INTEGER,
            null,
            ['nullable'=>false],
            'Otp send to mobile number'
        )->addIndex(
            $setup->getIdxName('auth_customer_otp', ['customer_number']),
            ['customer_number']
        )->setComment(
            'Customer otp auth table'
        );

        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }
}
