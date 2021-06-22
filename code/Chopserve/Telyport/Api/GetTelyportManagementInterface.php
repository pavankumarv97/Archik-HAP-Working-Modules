<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Chopserve\Telyport\Api;

interface GetTelyportManagementInterface
{

	 /**
     * GET for getTelyport api
     * @param mixed $order_id
     * @return array
     */
    public function getTelyport($order_id);
}

