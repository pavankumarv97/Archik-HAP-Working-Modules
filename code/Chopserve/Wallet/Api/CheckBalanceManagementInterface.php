<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Chopserve\Wallet\Api;

interface CheckBalanceManagementInterface
{

    /**
     * POST for checkBalance api
     * @param mixed $param
     * @return array
     */
    public function checkBalance($param);
}
