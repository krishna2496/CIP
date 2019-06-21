<?php

return [
	/**
	* API success messages
	*/
	'success' => [
		'MESSAGE_SLIDER_ADD_SUCCESS' => 'DE: Slider image added successfully',
		'MESSAGE_USER_FOUND' => 'DE: User found successfully',
		'MESSAGE_NO_DATA_FOUND' => 'DE: No Data Found',
		'MESSAGE_USER_CREATED' => 'DE: User created successfully',
		'MESSAGE_USER_DELETED' => 'DE: User deleted successfully',
		'MESSAGE_FOOTER_PAGE_CREATED' => 'DE: Page created successfully',
		'MESSAGE_FOOTER_PAGE_UPDATED' => 'DE: Page updated successfully',
		'MESSAGE_FOOTER_PAGE_DELETED' => 'DE: Page deleted successfully',
		'MESSAGE_USER_LOGGED_IN' => 'DE: You are successfully logged in',
		'MESSAGE_PASSWORD_RESET_LINK_SEND_SUCCESS' => 'DE: Reset Password link is sent to your email account,link will be expire in ' . config('constants.FORGOT_PASSWORD_EXPIRY_TIME') . ' hours',
		'MESSAGE_PASSWORD_CHANGE_SUCCESS' => 'DE: Your password has been changed successfully.',
		'MESSAGE_FOOTER_PAGE_LISTING' => 'DE: Footer pages listing successfully.',
		'MESSAGE_CUSTOM_FIELD_ADDED' => 'DE: User custom field added successfully',
		'MESSAGE_CUSTOM_FIELD_UPDATED' => 'DE: User custom field updated successfully',
		'MESSAGE_CUSTOM_FIELD_DELETED' => 'DE: User custom field deleted successfully',
		'MESSAGE_NO_RECORD_FOUND' => 'DE: No records found',
		'MESSAGE_USER_LISTING' => 'DE: User listing successfully',
		'MESSAGE_USER_SKILLS_CREATED' => 'DE: User skills linked successfully',
        'MESSAGE_USER_SKILLS_DELETED' => 'DE: User skills unlinked successfully',
        'MESSAGE_APPLICATION_LISTING' => 'DE: Mission application listing successfully',
        'MESSAGE_APPLICATION_UPDATED' => 'DE: Mission application updated successfully',
		'MESSAGE_CUSTOM_STYLE_UPLOADED_SUCCESS' => 'DE: Custom styling data uploaded successfully',
		'MESSAGE_CUSTOM_STYLE_RESET_SUCCESS' => 'DE: Custom styling reset successfully',
		'MESSAGE_CUSTOM_FIELD_LISTING' => 'DE: User custom field listing successfully',
		'MESSAGE_USER_UPDATED' => 'DE: User updated successfully',
		'MESSAGE_CMS_LIST_SUCCESS' => 'DE: CMS page listing successfully',
		'MESSAGE_MISSION_ADDED' => 'DE: Mission created successfully',
        'MESSAGE_MISSION_UPDATED' => 'DE: Mission updated successfully',
        'MESSAGE_MISSION_DELETED' => 'DE: Mission deleted successfully',
        'MESSAGE_MISSION_LISTING' => 'DE: Mission listing successfully',
        'MESSAGE_MISSION_LISTING' => 'Mission listing successfully',
        'MESSAGE_SKILL_LISTING' => 'Skill listing successfully',
        'MESSAGE_THEME_LISTING' => 'Mission theme listing successfully',
        'MESSAGE_CITY_LISTING' => 'City listing successfully',
		'MESSAGE_COUNTRY_LISTING' => 'Country listing successfully',
	],
	
	/**
	* HTTP status code
	*/
	'status_code' => [
		'HTTP_STATUS_BAD_REQUEST' => '400',
		'HTTP_STATUS_UNAUTHORIZED' => '401',
		'HTTP_STATUS_FORBIDDEN' => '403',
		'HTTP_STATUS_NOT_FOUND' => '404',
		'HTTP_STATUS_UNPROCESSABLE_ENTITY' => '422',
		'HTTP_STATUS_INTERNAL_SERVER_ERROR' => '500',
		'HTTP_STATUS_BAD_GATEWAY' => '502',
		'HTTP_STATUS_METHOD_NOT_ALLOWED' => '405',
		'HTTP_STATUS_NO_CONTENT' => '204',
		'HTTP_STATUS_CREATED' => '201',
	],
    
   /**
	* HTTP status Types
	*/
	'status_type' => [
		'HTTP_STATUS_TYPE_204' => 'No Content',
		'HTTP_STATUS_TYPE_201' => 'Created',
		'HTTP_STATUS_TYPE_400' => 'Bad Request',
		'HTTP_STATUS_TYPE_401' => 'Unauthorized',
		'HTTP_STATUS_TYPE_403' => 'Forbidden',
		'HTTP_STATUS_TYPE_404' => 'Not Found',
		'HTTP_STATUS_TYPE_405' => 'Method Not Allowed',
		'HTTP_STATUS_TYPE_422' => 'Unprocessable entity',
		'HTTP_STATUS_TYPE_500' => 'Internal Server Error',
		'HTTP_STATUS_TYPE_502' => 'Backend service failure (data store failure)'
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
		'ERROR_40011' => '40011',
		'ERROR_40012' => '40012',
		'ERROR_40013' => '40013',
		'ERROR_40012' => '40012',
		'ERROR_40014' => '40014',
		'ERROR_40016' => '40016',
		'ERROR_40018' => '40018',
		'ERROR_40020' => '40020',
		'ERROR_40022' => '40022',
		
		// Error codes from 2000-
		'ERROR_20002' => '20002',
		'ERROR_20004' => '20004',
		'ERROR_20006' => '20006',
		'ERROR_20008' => '20008',
		'ERROR_20010' => '20010',
		'ERROR_20014' => '20014',
		'ERROR_20016' => '20016',
		'ERROR_20018' => '20018',
		'ERROR_20022' => '20022',		
		'ERROR_20026' => '20026',
		'ERROR_20028' => '20028',
		'ERROR_20020' => '20020',
		'ERROR_20032' => '20032',
		'ERROR_20034' => '20034',
		'ERROR_21000' => '21000',
		'ERROR_20024' => '20024',
		'ERROR_20030' => '20030',
		'ERROR_20036' => '20036',
		'ERROR_20038' => '20038',
		'ERROR_20102' => '20102',

		// Error codes from 1000-
		'ERROR_10006' => '10006',
		
		// Custom error code for User Module - 100000 - 109999
		'ERROR_100000' => '100000',
		'ERROR_100001' => '100001',
        'ERROR_100002' => '100002',
		'ERROR_100003' => '100003',
		'ERROR_100004' => '100004',
		'ERROR_100010' => '100010',
		
		// Custom error code for CMS Module - 300000 - 309999
		'ERROR_300000' => '300000',
		'ERROR_300001' => '300001',
		'ERROR_300002' => '300002',
		'ERROR_300003' => '300003',
		'ERROR_300004' => '300004',

		// Custom error code for Mission Module - 400000 - 409999
        'ERROR_400000' => '400000',
        'ERROR_400001' => '400001',
        'ERROR_400002' => '400002',
        'ERROR_400003' => '400003',
        'ERROR_400004' => '400004',
		
	],
	
	/**
	* API Error Codes and Message
	*/
	'custom_error_message' => [
		// Error codes from 4000- Tenant User
		'40001' => 'DE: Email address or password field is empty',
		'40002' => 'DE: Email address does not exist in the system',
		'40004' => 'DE: Invalid password',
		'40006' => 'DE: Something went wrong while sending reset password link',
		'40008' => 'DE: Tenant domain does not found',
		'41000' => 'DE: Unknown database connection request for this domain',
		'40009' => 'DE: Domain name is required',
		'40010' => 'DE: Email address is required',
		'40011' => 'DE: Invalid reset password token or email address',
		'40013' => 'DE: Reset password link is expired or invalid',
		
		
		// Error codes from 2000-
        '40012' => 'DE: Token not provided',
		'40014' => 'DE: Provided token is expired',
		'40016' => 'DE: An error while decoding token',
		'40018' => 'DE: Database operational error',
		'40020' => 'DE: Sorry, you cannot add more than '.config('constants.SLIDER_LIMIT').' sliders!',
		'40022' => 'DE: Unable to upload slider image',

		// Error codes from 2000- Tenant Admin
		'20002' => 'DE: This email address is already taken, Please try with different email address',
		'20004' => 'DE: Error while inserting data to database',
		'20006' => 'DE: User deletion failed',
		'20008' => 'DE: Invalid API Key or Secret key',
		'20010' => 'DE: API key and Secret key are required',
		'20014' => 'DE: Unauthorised access',
		'20016' => 'DE: Databse not found',
		'21000' => 'DE: Error while creating database connection',
		'20018' => 'DE: Invalid input data',
		'20022' => 'DE: Invalid user data',
		'20024' => 'DE: This page is already added',
		'20030' => 'DE: Missing translation data',
		'20036' => 'DE: Invalid translation data, please check input parameters',
		'20038' => 'DE: The slug field is required',
		'20026' => 'DE: Please add values for this field',
		'20028' => 'DE: User custom field deletion failed',
		'20020' => 'DE: CMS page deletion failed',
		'20032' => 'DE: No data found for given id',
		'20034' => 'DE: Invalid request parameter',		
		'20102' => 'DE: Invalid custom field input parameters or missing data',		
		// Error codes from 1000-
		'10006' => 'DE: Database operational error',
		
		// Custom error code for User Module - 100000 - 109999
		'100000' => 'DE: User does not found in system',
		'100001' => 'DE: Invalid user data',
        '100002' => 'DE: Invalid skill data',
		'100003' => 'DE: Custom field creation failed. Please check input parameters',
		'100004' => 'DE: Requested user custom field does not exist',
		'100010' => 'DE: User creation failed. Please check input parameters',
		
		// Custom error code for CMS Module - 300000 - 309999
		'300000' => 'DE: Page creation failed. Please check input parameters',
		'300001' => 'DE: The slug field is required',
		'300002' => 'DE: Missing translation data',
		'300003' => 'DE: Invalid translation data, please check input parameters',
		'300004' => 'DE: Page creation faild. Invalid input data for sections',
		'300005' => 'DE: Requested page does not exist',

		// Custom error code for Mission Module - 400000 - 409999
        '400000' => 'DE: Invalid application data or missing parameter',
        '400001' => 'DE: Invalid mission data or missing parameter',
        '400002' => 'DE: Error while inserting data to database',
        '400003' => 'DE: Requested mission does not exist',
        '400004' => 'DE: Mission deletion failed',
	]
];
