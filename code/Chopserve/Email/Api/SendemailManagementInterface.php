<?php
declare(strict_types=1);
namespace Chopserve\Email\Api;

interface SendemailManagementInterface
{

    /**
     * POST for sendemail api
     * @param mixed $param
     * @return array
     */
    public function Sendemail($param);

    /**
     * POST for sendemail api
     * @param string $param
     * @return array
     */
    public function getTemplates($param);

    /**
     * POST for sendemail pickup api
     * @param mixed $param
     * @return mixed
     */
    public function pickupemail($param);
}