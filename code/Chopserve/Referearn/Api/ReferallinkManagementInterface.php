<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Chopserve\Referearn\Api;

interface ReferallinkManagementInterface
{

    /**
     * GET for Referallink api
     * @param string $customer_id
     * @return array
     */
    public function getReferallink($customer_id);
}

