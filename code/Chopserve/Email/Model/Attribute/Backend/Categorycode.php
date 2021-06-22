<?php
namespace Chopserve\Email\Model\Attribute\Backend;

/**
 * Class Categorycode
 */
class Categorycode extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{

    /**
     * @param \Magento\Framework\DataObject $object
     *
     * @return $this
     */
    public function afterLoad($object)
    {
        // your after load logic

        return parent::afterLoad($object);
    }

    /**
     * @param \Magento\Framework\DataObject $object
     *
     * @return $this
     */
    public function beforeSave($object)
    {
        $this->validateCode($object);

        return parent::beforeSave($object);
    }

    public function validateCode($object)
    {
        /** @var string $attributeCode */
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $attributeLabel = $this->getAttribute()->getFrontendLabel();
        /** @var int $value */
        $value = $object->getData($attributeCode);
        /** @var int $minimumValueLength */
        $specialCharsExist = $this->checkSpecialChars($value);

        if ($specialCharsExist) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Special Characters not allowed for attribute "'.$attributeLabel.'"')
            );
        }

        return true;
    }

    /**
     * Get minimum attribute value length
     * 
     * @return int
     */
    public function checkSpecialChars($string)
    {
		if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $string))
		{
			return true;
		}else{
			
		}
        return false;
    }
}