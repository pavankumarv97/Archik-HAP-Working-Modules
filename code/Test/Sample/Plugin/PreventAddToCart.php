<?php

namespace Test\Sample\Plugin;

use Magento\Checkout\Model\Cart;

class PreventAddToCart
{

    public function beforeAddProduct(Cart $subject, $productInfo, $requestInfo = null)
    {
        // if (!something) {
        //     throw new \Magento\Framework\Exception\LocalizedException(__("ha ha"));
        // }
        
        return [$productInfo,$requestInfo];
    }
}