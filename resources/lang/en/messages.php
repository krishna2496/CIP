<?php
return [
	
	/**
	* Success messages
	*/
	'success' => [
		'MESSAGE_TENANT_CREATED' => 'Tenant created successfully',
		'MESSAGE_TENANT_UPDATED' => 'Tenant details updated successfully',
		'MESSAGE_TENANT_DELETED' => 'Tenant deleted successfully',
		'MESSAGE_TENANT_LISTING' => 'Tenant listing successfully',
		'MESSAGE_NO_RECORD_FOUND' => 'No records found'
	],
	
	/**
	* HTTP status code
	*/
	'status_code' => [
		'HTTP_STATUS_BAD_REQUEST' => '400',
		'HTTP_STATUS_403' => '403',
		'HTTP_STATUS_NOT_FOUND' => '404',
		'HTTP_STATUS_422' => '422',
		'HTTP_STATUS_500' => '500',
		'HTTP_STATUS_BAD_GATEWAY' => '502'
	],
    
   /**
	* HTTP status Types
	*/
	'status_type' => [
		'HTTP_STATUS_TYPE_400' => 'Bad Request',
		'HTTP_STATUS_TYPE_403' => 'Forbidden',
		'HTTP_STATUS_TYPE_404' => 'Not Found',
		'HTTP_STATUS_TYPE_422' => 'Unprocessable entity',
		'HTTP_STATUS_TYPE_500' => 'Internal Server Error',
		'HTTP_STATUS_TYPE_502' => 'Backend service failure (data store failure)'
	],
	
	/**
	* API Error Codes
	*/
	'custom_error_code' => [
		'ERROR_10001' => '10001',
		'ERROR_10002' => '10002',
		'ERROR_10004' => '10004',
		'ERROR_10006' => '10006',
	],
	
	/**
	* API Error Codes and Message
	*/
	'custom_error_message' => [
		'10001' => 'Tenant name or sponsored field is empty',
		'10002' => 'Tenant name is already taken, Please try with different name.',
		'10004' => 'Tenant not found in the system',
		'10006' => 'Database operational error',
		'10008' => 'No data found',
	]
	
];