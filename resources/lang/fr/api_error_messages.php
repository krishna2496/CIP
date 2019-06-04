<?php

return [

	/**
	* HTTP status code
	*/
	'status_code' => [
		'HTTP_STATUS_400' => '400',
		'HTTP_STATUS_401' => '401',
		'HTTP_STATUS_403' => '403',
		'HTTP_STATUS_404' => '404',
		'HTTP_STATUS_422' => '422',
		'HTTP_STATUS_500' => '500',
	],
    
   /**
	* HTTP status Types
	*/
	'status_type' => [
		'HTTP_STATUS_TYPE_400' => 'FR: Bad Request',
		'HTTP_STATUS_TYPE_401' => 'FR: Unauthorized',
		'HTTP_STATUS_TYPE_403' => 'FR: Forbidden',
		'HTTP_STATUS_TYPE_404' => 'FR: Not Found',
		'HTTP_STATUS_TYPE_422' => 'FR: Unprocessable entity',
		'HTTP_STATUS_TYPE_500' => 'FR: Internal Server Error'
	],
	
	/**
	* API Error Codes
	*/
	'custom_error_code' => [
		// Error codes from 4000-
		'ERROR_40001' => 'FR: 40001',
		'ERROR_40002' => 'FR: 40002',
		'ERROR_40004' => 'FR: 40004',
		'ERROR_40006' => 'FR: 40006',
		'ERROR_40008' => 'FR: 40008',
		'ERROR_41000' => 'FR: 41000',
		'ERROR_40009' => 'FR: 40009',
		'ERROR_40010' => 'FR: 40010',
		'ERROR_40011' => 'FR: 40011',
		'ERROR_40012' => 'FR: 40012',
		'ERROR_40013' => 'FR: 40013',
		'ERROR_40012' => 'FR: 40012',
		'ERROR_40014' => 'FR: 40014',
		'ERROR_40016' => 'FR: 40016',
		'ERROR_40018' => 'FR: 40018',
		'ERROR_40020' => 'FR: 40020',
		
		// Error codes from 2000-
		'ERROR_20002' => 'FR: 20002',
		'ERROR_20004' => 'FR: 20004',
		'ERROR_20006' => 'FR: 20006',
		'ERROR_20008' => 'FR: 20008',
		'ERROR_20010' => 'FR: 20010',
		'ERROR_20014' => 'FR: 20014',
		'ERROR_20016' => 'FR: 20016',
		'ERROR_20018' => 'FR: 20018',
		'ERROR_20022' => 'FR: 20022',
		'ERROR_21000' => 'FR: 21000',

		// Error codes from 1000-
		'ERROR_10006' => 'FR: 10006',
		
	],
	
	/**
	* API Error Codes and Message
	*/
	'custom_error_message' => [
		// Error codes from 4000- Tenant User
		'40001' => 'FR: Email address or password field is empty',
		'40002' => 'FR: Email address does not exist in the system',
		'40004' => 'FR: Invalid password',
		'40006' => 'FR: Something went wrong while sending reset password link',
		'40008' => 'FR: Tenant domain does not found',
		'41000' => 'FR: Unknown database connection request for this domain',
		'40009' => 'FR: Domain name is required',
		'40010' => 'FR: Email address is required',
		'40011' => 'FR: Invalid reset password token or email address',
		'40013' => 'FR: Reset password link is expired or invalid',
		
		
		// Error codes from 2000-
                '40012' => 'FR: Token not provided',
		'40014' => 'FR: Provided token is expired',
		'40016' => 'FR: An error while decoding token',
		'40018' => 'FR: Database operational error',
		'40020' => 'FR: Sorry, you cannot add more than '.config('constants.SLIDER_LIMIT').' sliders!',
	
		// Error codes from 2000- Tenant Admin
		'20002' => 'FR: This email address is already taken, Please try with different email address',
		'20004' => 'FR: Error while inserting data to database',
		'20006' => 'FR: User deletion failed',
		'20008' => 'FR: Invalid API Key or Secret key',
		'20010' => 'FR: API key and Secret key are required',
		'20014' => 'FR: Unauthorised access',
		'20016' => 'FR: Databse not found',
		'21000' => 'FR: Error while creating database connection',
		'20018' => 'FR: Invalid input data',
		'20022' => 'FR: Invalid user data',

		// Error codes from 1000-
		'10006' => 'FR: Database operational error',
	]
	
];
