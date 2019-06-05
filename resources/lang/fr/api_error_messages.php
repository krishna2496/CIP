<?php

return [
    
	/**
	* HTTP status code
	*/
	'status_code' => [
		'HTTP_STATUS_400' => '400',
		'HTTP_STATUS_403' => '403',
		'HTTP_STATUS_404' => '404',
		'HTTP_STATUS_422' => '422',
		'HTTP_STATUS_500' => '500'
	],
    
   /**
	* HTTP status Types
	*/
	'status_type' => [
		'HTTP_STATUS_TYPE_400' => 'FR: Bad Request',
		'HTTP_STATUS_TYPE_403' => 'FR: Forbidden',
		'HTTP_STATUS_TYPE_404' => 'FR: Not Found',
		'HTTP_STATUS_TYPE_422' => 'FR: Unprocessable entity',
		'HTTP_STATUS_TYPE_500' => 'FR: Internal Server Error'
	],
	
	/**
	* API Error Codes
	*/
	'custom_error_code' => [
		'ERROR_10001' => 'FR: 10001',
		'ERROR_10002' => 'FR: 10002',
		'ERROR_10004' => 'FR: 10004',
		'ERROR_10006' => 'FR: 10006',
	],
	
	/**
	* API Error Codes and Message
	*/
	'custom_error_message' => [
		'10001' => 'FR: Tenant name or sponsored field is empty',
		'10002' => 'FR: Tenant name is already taken, Please try with different name.',
		'10004' => 'FR: Tenant not found in the system',
		'10006' => 'FR: Database operational error',
		'10008' => 'FR: No data found',
	]
	
];