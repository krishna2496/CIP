<?php

return [
    
   /**
	* API Error Types
	*/
	'type' => [
		'ERROR_TYPE_404' => 'Not Found',
		'ERROR_TYPE_400' => 'Bad Request',
		'ERROR_TYPE_422' => 'Unprocessable entity',
		'ERROR_TYPE_500' => 'Internal Server Error'
	],
	
	/**
	* API Error Codes and Message
	*/
	'code' => [
		'40001' => 'Email address or password field is empty',
		'40002' => 'Email address does not exist in the system',
		'40004' => 'Invalid password',
		'40006' => 'Something went wrong while sending reset password link'
	]
	
];
