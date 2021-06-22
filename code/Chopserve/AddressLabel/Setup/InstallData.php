<?php

namespace Chopserve\AddressLabel\Setup;

    use Magento\Customer\Setup\CustomerSetup;
    use Magento\Customer\Setup\CustomerSetupFactory;
    use Magento\Eav\Model\Config;
    use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
    use Magento\Eav\Setup\EavSetupFactory;
    use Magento\Framework\Setup\InstallDataInterface;
    use Magento\Framework\Setup\ModuleContextInterface;
    use Magento\Framework\Setup\ModuleDataSetupInterface;

    class InstallData implements InstallDataInterface
    {
        /**
         * @var Config
         */
        private $eavConfig;

        /**
         * @var EavSetupFactory
         */
        private $_eavSetupFactory;

        /**
         * @var AttributeSetFactory
         */
        private $attributeSetFactory;
        private $customerSetupFactory;

        /**
         * @param Config $eavConfig
         * @param EavSetupFactory $eavSetupFactory
         * @param AttributeSetFactory $attributeSetFactory
         */
        public function __construct(
            Config $eavConfig,
            EavSetupFactory $eavSetupFactory,
            AttributeSetFactory $attributeSetFactory,
            CustomerSetupFactory $customerSetupFactory
        ) {
            $this->eavConfig = $eavConfig;
            $this->_eavSetupFactory = $eavSetupFactory;
            $this->attributeSetFactory = $attributeSetFactory;
            $this->customerSetupFactory = $customerSetupFactory;
        }

        public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
        {
            /**
             * @var CustomerSetup $customerSetup
             */
            $customerSetup = $this->customerSetupFactory->create(['setup'=>$setup]);

            $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer_address');
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $customerSetup->addAttribute('customer_address', 'address_label', [
            'type' => 'varchar',
            'input' => 'select',
            'label' => 'Address Label',
            'visible' => true,
            'required' => true,
            'user_defined' => true,
            'system' => false,
            'source' => '\Chopserve\AddressLabel\Model\Config\Source\Options',
            'visible_on_front' => true,
                'default'=>0,
        ]);

            $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'address_label')
                ->addData([
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms' => ['adminhtml_customer_address', 'customer_address_edit', 'customer_register_address'],
                ]);
            $attribute->save();

            $setup->endSetup();
        }
    }
