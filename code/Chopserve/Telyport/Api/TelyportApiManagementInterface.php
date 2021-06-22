<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Chopserve\Telyport\Api;

interface TelyportApiManagementInterface
{

	 /**
     * POST for getTelyport api
     * @param mixed $param
     * @return array
     */
    public function telyportApi($param);
}

