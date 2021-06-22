<?php
namespace Chopserve\SourceMapping\Api;

/**
 * @api
 */
interface ExecutorInterface
{
    /**
     * execute
     * @param int $id
     */
    public function execute($id);
}
