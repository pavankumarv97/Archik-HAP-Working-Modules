<?php
namespace Chopserve\SourceMapping\Block\Adminhtml\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Save implements ButtonProviderInterface
{
    /**
     * @var string | null
     */
    private $label;

    /**
     * Save constructor.
     * @param string $label
     */
    public function __construct(
        $label = null
    ) {
        $this->label = $label;
    }

    /**
     * get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => $this->getLabel(),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }

    /**
     * @return \Magento\Framework\Phrase|null|string
     */
    private function getLabel()
    {
        if ($this->label === null) {
            return __('Save');
        }
        return $this->label;
    }
}
