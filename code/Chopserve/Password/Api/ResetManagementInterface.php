<?php 
namespace Chopserve\Password\Api;
 
 
interface ResetManagementInterface {


	/**
	 * GET for Post api
	 * @param string $email
     * @param string $resetToken
	 * @param string $newPassword
	 * @return string
	 */
	
	public function getReset($email,$resetToken,$newPassword);
}