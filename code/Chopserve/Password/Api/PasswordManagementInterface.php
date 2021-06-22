<?php 
namespace Chopserve\Password\Api;
 
 
interface PasswordManagementInterface {


	/**
	 * GET for Post api
	 * @param string $email
     * @param string $template
     * @param integer $website_id
	 * @return string
	 */
	
	public function getPost($email,$template,$website_id);
}