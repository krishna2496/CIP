<?php

return [

    /**
    * API success messages
    */
    'success' => [
        'MESSAGE_SLIDER_ADD_SUCCESS' => 'FR: Slider image added successfully',
        'MESSAGE_USER_FOUND' => 'FR: User found successfully',
        'MESSAGE_NO_DATA_FOUND' => 'FR: No Data Found',
        'MESSAGE_USER_CREATED' => 'FR: User created successfully',
        'MESSAGE_USER_DELETED' => 'FR: User deleted successfully',
        'MESSAGE_CMS_PAGE_ADD_SUCCESS' => 'FR: Page created successfully',
        'MESSAGE_CMS_PAGE_UPDATE_SUCCESS' => 'FR: Page updated successfully',
        'MESSAGE_CMS_PAGE_DELETE_SUCCESS' => 'FR: Page deleted successfully',
        'MESSAGE_USER_LOGGED_IN' => 'FR: You are successfully logged in',
        'MESSAGE_PASSWORD_RESET_LINK_SEND_SUCCESS' => 'FR: Reset Password link is sent to your email account,link will be expire in ' . config('constants.FORGOT_PASSWORD_EXPIRY_TIME') . ' hours',
        'MESSAGE_PASSWORD_CHANGE_SUCCESS' => 'FR: Your password has been changed successfully.',
        'MESSAGE_CMS_LIST_SUCCESS' => 'FR: CMS page listing successfully.',
        'MESSAGE_CUSTOM_FIELD_ADD_SUCCESS' => 'FR: User custom field added successfully',
        'MESSAGE_CUSTOM_FIELD_UPDATE_SUCCESS' => 'FR: User custom field updated successfully',
        'MESSAGE_CUSTOM_FIELD_DELETE_SUCCESS' => 'FR: User custom field deleted successfully',
        'MESSAGE_NO_RECORD_FOUND' => 'FR: No records found',
        'MESSAGE_USER_LISTING' => 'FR: User listing successfully',
        'MESSAGE_USER_SKILLS_CREATED' => 'FR: User skills linked successfully',
        'MESSAGE_USER_SKILLS_DELETED' => 'FR: User skills unlinked successfully',
        'MESSAGE_APPLICATION_LISTING' => 'FR: Mission application listing successfully',
    ],
    
    /**
    * HTTP status code
    */
    'status_code' => [
        'HTTP_STATUS_CREATED' => '201',
        'HTTP_STATUS_NO_CONTENT' => '204',
        'HTTP_STATUS_BAD_REQUEST' => '400',
        'HTTP_STATUS_401' => '401',
        'HTTP_STATUS_FORBIDDEN' => '403',
        'HTTP_STATUS_NOT_FOUND' => '404',
        'HTTP_STATUS_UNPROCESSABLE_ENTITY' => '422',
        'HTTP_STATUS_INTERNAL_SERVER_ERROR' => '500',
        'HTTP_STATUS_BAD_GATEWAY' => '502',
        'HTTP_STATUS_METHOD_NOT_ALLOWED' => '405',
    ],
    
   /**
    * HTTP status Types
    */
    'status_type' => [
        'HTTP_STATUS_TYPE_400' => 'Bad Request',
        'HTTP_STATUS_TYPE_401' => 'Unauthorized',
        'HTTP_STATUS_TYPE_403' => 'Forbidden',
        'HTTP_STATUS_TYPE_404' => 'Not Found',
        'HTTP_STATUS_TYPE_422' => 'Unprocessable entity',
        'HTTP_STATUS_TYPE_500' => 'Internal Server Error',
        'HTTP_STATUS_TYPE_502' => 'Backend service failure (data store failure)',
        'HTTP_STATUS_TYPE_405' => 'Method Not Allowed',
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
        
        // Error codes from 2000-
        'ERROR_20002' => '20002',
        'ERROR_20004' => '20004',
        'ERROR_20006' => '20006',
        'ERROR_20008' => '20008',
        'ERROR_20010' => '20010',
        'ERROR_20014' => '20014',
        'ERROR_20016' => '20016',
        'ERROR_20018' => '20018',
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
        '20024' => 'FR: This page is already added',
        '20030' => 'FR: Missing translation data',
        '20036' => 'FR: Invalid translation data, please check input parameters',
        '20038' => 'FR: The slug field is required',
        '20026' => 'FR: Please add values for this field',
        '20028' => 'FR: User custom field deletion failed',
        '20020' => 'FR: CMS page deletion failed',
        '20032' => 'FR: No data found for given id',
        '20034' => 'FR: Invalid request parameter',
        '20102' => 'FR: Invalid custom field input parameters or missing data',
        // Error codes from 1000-
        '10006' => 'FR: Database operational error',
        
        // Custom error code for User Module - 100000 - 109999
        '100000' => 'FR: User does not found in system',
        '100001' => 'FR: Invalid user data',
        '100002' => 'FR: Invalid skill data',
    ]
];
