<?php

namespace Hatsun\CustomerViewProduct\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{

    public function install(ModuleDataSetupInterface $setup , ModuleContextInterface $context)
    {
        $setup->startSetup();
        $setup->getConnection()->insert(
            $setup->getTable('recently_viewed_product'),
            [ 'product_id'=>'00001' , 'customer_id'=>'11']
        );

        $setup->getConnection()->insert(
            $setup->getTable('recently_viewed_product'),
            ['product_id'=>'00002' , 'customer_id'=>'15']
        );
        

        $setup->endSetup();
    }

}
