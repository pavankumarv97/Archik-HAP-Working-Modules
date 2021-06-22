<?php
namespace Chopserve\Customization\Api;
/**
 * @api
 */
interface CustomizationRepositoryInterface
{
	 /**
     * POST for CustomizationRepository api
     * @param mixed $customization
     * @return array
     */
    public function save($customization);
}
