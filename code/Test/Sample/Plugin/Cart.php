<?php 

namespace Test\Sample\Plugin;

class Cart{

	protected $_attributeRepository;
    protected $logger;
    protected $cart;
    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * Plugin constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger
        // AttributeSetRepositoryInterface $attributeRepository

    ) {
        $this->quote = $checkoutSession->getQuote();
        $this->logger = $logger;
        // $this->_attributeRepository = $attributeRepository; 
    }
	 /**
     * beforeAddProduct
     *
     * @param      $subject
     * @param      $productInfo
     * @param null $requestInfo
     *
     * @return array
     * @throws LocalizedException
     */
   public function beforeAddProduct(Cart $subject, $productInfo, $requestInfo = null){

       $product = $subject->getProduct();
        $request = $subject->getQtyRequest($product, $requestInfo);
        $this->logger->debug("Product QTY REQUEST BEFORE: ".$result);


        return [$productInfo, $requestInfo];
    }
}