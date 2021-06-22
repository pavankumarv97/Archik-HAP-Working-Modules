<?php

namespace Chopserve\CancelOrder\Api;

interface CancelOrderRepositoryInterface
{

    /**
     * @param string $orderId
     * @return bool
     */
    public function cancelOrder($orderId);

    /**
     * @param mixed $param
     * @return array
     */
    public function cancellation($param);

}
