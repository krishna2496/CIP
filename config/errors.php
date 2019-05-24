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
		'40001' => 'Email address or password field is empty',
		'40002' => 'Email address does not exist in the system',
		'40004' => 'Invalid password',
		'40006' => 'Something went wrong while sending reset password link',
		'40008' => 'Tenant domain does not found',
		'41000' => 'Unknown database connection request for this domain',
		'40009' => 'Domain name is required',
		
		'20002' => 'This email address is already taken, Please try with different email address',
		'20004' => 'Error while inserting data to database',
		'20008' => 'Invalid API Key or Secret key',
		'20010' => 'API key and Secret key are required',
	]
	
];
