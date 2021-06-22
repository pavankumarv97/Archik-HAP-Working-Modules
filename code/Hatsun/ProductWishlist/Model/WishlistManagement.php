<?php

namespace Hatsun\ProductWishlist\Model;

use Hatsun\ProductWishlist\Api\WishlistManagementInterface;
use Magento\Wishlist\Controller\WishlistProvider;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Helper\ImageFactory as ProductImageHelper;
use Magento\Store\Model\App\Emulation as AppEmulation;

class WishlistManagement implements WishlistManagementInterface
{

    /**
     * @var CollectionFactory
     */
    protected $_wishlistCollectionFactory;

    /**
     * Wishlist item collection
     *
     * @var \Magento\Wishlist\Model\ResourceModel\Item\Collection
     */
    protected $_itemCollection;

    /**
     * @var WishlistRepository
     */
    protected $_wishlistRepository;

    /**
     * @var ProductRepository
     */
    protected $_productRepository;

    /**
     * @var WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * @var Item
     */
    protected $_itemFactory;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     *@var \Magento\Catalog\Helper\ImageFactory
     */
    protected $productImageHelper;

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storemanagerinterface;

    /**
     *@var \Magento\Store\Model\App\Emulation
     */
    protected $appEmulation;

    /**
     *@var \Magento\Catalog\Model\Product
     */
    protected $_productload;

    /**
     *@var \Magento\Directory\Model\CountryFactory
     */
    protected $countryfactory;

    /**
     * @param CollectionFactory $wishlistCollectionFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        CollectionFactory $wishlistCollectionFactory,
        WishlistFactory $wishlistFactory,
        \Magento\Customer\Model\Customer $customer,
        AppEmulation $appEmulation,
        \Magento\Directory\Model\CountryFactory $countryfactory,
        \Magento\Store\Model\StoreManagerInterface $storemanagerinterface,
        ProductImageHelper $productImageHelper,
        \Magento\Catalog\Model\Product $productload,
        \Magento\Wishlist\Model\WishlistFactory $wishlistRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Wishlist\Model\ItemFactory $itemFactory
    ) {
        $this->_wishlistCollectionFactory = $wishlistCollectionFactory;
        $this->_wishlistRepository = $wishlistRepository;
        $this->_productRepository = $productRepository;
        $this->_wishlistFactory = $wishlistFactory;
        $this->countryfactory = $countryfactory;
        $this->storemanagerinterface = $storemanagerinterface;
        $this->_itemFactory = $itemFactory;
        $this->_customer = $customer;
        $this->_productload = $productload;
        $this->appEmulation = $appEmulation;
        $this->productImageHelper = $productImageHelper;
        $this->_customer = $customer;
    }

    /**
     * Get wishlist collection
     * @deprecated
     * @param $customerId
     * @return WishlistData
     */
    public function getWishlistForCustomer($customerId)
    {

        if (empty($customerId) || !isset($customerId) || $customerId == "") {
            throw new InputException(__('Id required'));
        } else {
            $collection =
                $this->_wishlistCollectionFactory->create()
                    ->addCustomerIdFilter($customerId);
            $baseurl = $this->storemanagerinterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/product';
            $wishlistData = [];
            foreach ($collection as $item) {
                $productInfo = $item->getProduct()->toArray();
                if ($productInfo['small_image'] == 'no_selection') {
                  $currentproduct = $this->_productload->load($productInfo['entity_id']);
                  $imageURL = $this->getImageUrl($currentproduct, 'product_base_image');
                  $productInfo['small_image'] = $imageURL;
                  $productInfo['thumbnail'] = $imageURL;
                }else{
                  $imageURL = $baseurl.$productInfo['small_image'];
                  $productInfo['small_image'] = $imageURL;
                  $productInfo['thumbnail'] = $imageURL;
                }
                $data = [
                    "wishlist_item_id" => $item->getWishlistItemId(),
                    "wishlist_id"      => $item->getWishlistId(),
                    "product_id"       => $item->getProductId(),
                    "store_id"         => $item->getStoreId(),
                    "added_at"         => $item->getAddedAt(),
                    "description"      => $item->getDescription(),
                    "qty"              => round($item->getQty()),
                    "product"          => $productInfo
                ];
                $wishlistData[] = $data;
            }
            return $wishlistData;
        }
    }

    /**
     * Add wishlist item for the customer
     * @param int $customerId
     * @param int $productIdId
     * @return array|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addWishlistForCustomer($customerId, $productId)
    {
        if ($productId == null) {
            throw new LocalizedException(__
            ('Invalid product, Please select a valid product'));
        }
        try {
            $product = $this->_productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $product = null;
        }
        try {
            $wishlist = $this->_wishlistRepository->create()->loadByCustomerId
            ($customerId, true);
            $wishlist->addNewItem($product);
            $returnData = $wishlist->save();
        } catch (NoSuchEntityException $e) {

        }
        return true;
    }    

    /**
     * Helper function that provides full cache image url
     * @param \Magento\Catalog\Model\Product
     * @return string
     */
    public function getImageUrl($product, string $imageType = ''){
        $storeId = $this->storemanagerinterface->getStore()->getId();
        $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $imageUrl = $this->productImageHelper->create()->init($product, $imageType)->getUrl();
        $this->appEmulation->stopEnvironmentEmulation();

        return $imageUrl;
    }

    /**
     * Delete wishlist item for customer
     * @param int $customerId
     * @param int $productIdId
     * @return array
     *
     */
    public function deleteWishlistForCustomer($customerId, $wishlistItemId)
    {

        $message = null;
        $status = null;
        if ($wishlistItemId == null) {
            $message = __('Invalid wishlist item, Please select a valid item');
            $status = false;
            $response[] = [
                "message" => $message,
                "status"  => $status
            ];
            return $response;
        }
        $item = $this->_itemFactory->create()->load($wishlistItemId);
        if (!$item->getId()) {
            $message = __('The requested Wish List Item doesn\'t exist .');
            $status = false;

            $response[] = [
                "message" => $message,
                "status"  => $status
            ];
            return $response;
        }
        $wishlistId = $item->getWishlistId();
        $wishlist = $this->_wishlistFactory->create();

        if ($wishlistId) {
            $wishlist->load($wishlistId);
        } elseif ($customerId) {
            $wishlist->loadByCustomerId($customerId, true);
        }
        if (!$wishlist) {
            $message = __('The requested Wish List Item doesn\'t exist .');
            $status = false;
            $response[] = [
                "message" => $message,
                "status"  => $status
            ];
            return $response;
        }
        if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
            $message = __('The requested Wish List Item doesn\'t exist .');
            $status = false;
            $response[] = [
                "message" => $message,
                "status"  => $status
            ];
            return $response;
        }
        try {
            $item->delete();
            $wishlist->save();
        } catch (Exception $e) {
            return false;
        }

        $message = __(' Item has been removed from wishlist .');
        $status = true;
        $response[] = [
            "message" => $message,
            "status"  => $status
        ];
        return $response;
    }

    /**
     * Delete all wishlist items for customer
     * @param int $customerId
     * @return array
     *
     */
    public function clearAllWishlist($customerId)
    {
        if (empty($customerId) || !isset($customerId) || $customerId == "") {
            throw new InputException(__('Id required'));
        } else {
            $collection = $this->_wishlistCollectionFactory->create()
                    ->addCustomerIdFilter($customerId);
            if($collection != ""){
                foreach ($collection as $item) {
                    $item->delete();
                }  
                return true;
            }else{
                return false;
            }    
        }
    }

}