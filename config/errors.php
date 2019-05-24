<?php

return [
    
   /**
	* API Error Types
	*/
	'type' => [
		'ERROR_TYPE_400' => 'Bad Request',
		'ERROR_TYPE_403' => 'Forbidden',
		'ERROR_TYPE_404' => 'Not Found',
		'ERROR_TYPE_422' => 'Unprocessable entity',
		'ERROR_TYPE_500' => 'Internal Server Error'
	],
	
	/**
	* API Error Codes and Message
	*/
	'code' => [
		'10001' => 'Tenant name or sponsored field is empty',
		'10002' => 'Tenant name is already taken, Please try with different name.',
		'10004' => 'Tenant not found in the system',
		'10006' => 'Database operational error',
	]
	
];