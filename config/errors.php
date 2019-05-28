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
		'HTTP_STATUS_TYPE_400' => 'Bad Request',
		'HTTP_STATUS_TYPE_403' => 'Forbidden',
		'HTTP_STATUS_TYPE_404' => 'Not Found',
		'HTTP_STATUS_TYPE_422' => 'Unprocessable entity',
		'HTTP_STATUS_TYPE_500' => 'Internal Server Error'
	],
	
	/**
	* API Error Codes
	*/
	'custom_error_code' => [
		// Error codes from 4000-
		'ERROR_40001' => '40001',
		'ERROR_40002' => '40002',
		'ERROR_40004' => '40004',
		'ERROR_40006' => '40006',
		'ERROR_40008' => '40008',
		'ERROR_41000' => '41000',
		'ERROR_40009' => '40009',
		'ERROR_40010' => '40010',
		'ERROR_40012' => '40012',
		
		// Error codes from 2000-
		'ERROR_20002' => '20002',
		'ERROR_20004' => '20004',
		'ERROR_20006' => '20006',
		'ERROR_20008' => '20008',
		'ERROR_20010' => '20010',
		'ERROR_20014' => '20014',
		'ERROR_20016' => '20016',
		'ERROR_21000' => '21000',

		// Error codes from 1000-
		'ERROR_10006' => '10006',
		
	],
	
	/**
	* API Error Codes and Message
	*/
	'custom_error_message' => [
		// Error codes from 4000-
		'40001' => 'Email address or password field is empty',
		'40002' => 'Email address does not exist in the system',
		'40004' => 'Invalid password',
		'40006' => 'Something went wrong while sending reset password link',
		'40008' => 'Tenant domain does not found',
		'41000' => 'Unknown database connection request for this domain',
		'40009' => 'Domain name is required',
		'40010' => 'Email address is required',
	
		// Error codes from 2000-
		'20002' => 'This email address is already taken, Please try with different email address',
		'20004' => 'Error while inserting data to database',
		'20006' => 'User deletion failed',
		'20008' => 'Invalid API Key or Secret key',
		'20010' => 'API key and Secret key are required',
		'20014' => 'Unauthorised access',
		'20016' => 'Invalid input data',
		'21000' => 'Error while creating database connection',

		// Error codes from 1000-
		'10006' => 'Database operational error',
	]
	
];
