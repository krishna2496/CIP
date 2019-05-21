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
		'10001' => 'Unprocessable entity',
		'10002' => 'Tenant name is already registered',
		'10004' => 'Tenant not found',
		'10006' => 'Database operational error',
	]
	
];