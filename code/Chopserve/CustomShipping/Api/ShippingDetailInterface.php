<?php

namespace Chopserve\CustomShipping\Api;

use Magento\Framework\Exception\NoSuchEntityException;

interface ShippingDetailInterface
{



    /**
     * @return mixed[]
     * @throws NoSuchEntityException
     */
    public function getdetails();
}
