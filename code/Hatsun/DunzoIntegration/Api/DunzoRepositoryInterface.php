<?php

namespace Hatsun\DunzoIntegration\Api;

/**
 * Interface DunzoRepositoryInterface
 * @api
 */
interface DunzoRepositoryInterface
{

    /**
     * 
     * @return string
     */
    public function getToken();


    /**
     * @param mixed $object
     * @return mixed
     */
    public function getQuote($object);

    /**
     * @param mixed $object
     * @return mixed
     */
    public function createTasks($object);


    /**
     * @param mixed $task_id
     * @return mixed
     */
    public function getStatus($task_id);

    /**
     * @param mixed $task_id
     * @param mixed $cancellation
     * @return mixed
     */
    public function cancel($task_id , $cancellation);

    /**
     * @param string $orderId
     * @return string
     */
    public function getTrackingId($orderId);

    /**
     * @param mixed $params
     * @return mixed
     */
    public function grandtotal($params);

}