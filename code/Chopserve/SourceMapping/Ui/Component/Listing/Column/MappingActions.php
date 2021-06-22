<?php
namespace Chopserve\SourceMapping\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class MappingActions extends Column
{
    /**
     * Url path  to edit
     * @var string
     */
    const URL_PATH_EDIT = 'chopserve_sourcemapping/mapping/edit';

    /**
     * Url path  to delete
     * @var string
     */
    const URL_PATH_DELETE = 'chopserve_sourcemapping/mapping/delete';

    /**
     * Url builder
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * constructor
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['mapping_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'mapping_id' => $item['mapping_id']
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'mapping_id' => $item['mapping_id']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete "${ $.$data.source_pincode }"'),
                                'message' => __('Are you sure you want to delete the Source Mapping "${ $.$data.source_pincode }" ?')
                            ]
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
