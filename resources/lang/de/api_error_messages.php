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
        'HTTP_STATUS_TYPE_400' => 'Bad Request',
        'HTTP_STATUS_TYPE_401' => 'Unauthorized',
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
        'ERROR_100001' => '100001',
        'ERROR_100002' => '100002',

        // Error codes from 1000-
        'ERROR_10006' => '10006',
        
    ],
    
    /**
    * API Error Codes and Message
    */
    'custom_error_message' => [
        // Error codes from 4000- Tenant User
        '40001' => 'Email address or password field is empty',
        '40002' => 'Email address does not exist in the system',
        '40004' => 'Invalid password',
        '40006' => 'Something went wrong while sending reset password link',
        '40008' => 'Tenant domain does not found',
        '41000' => 'Unknown database connection request for this domain',
        '40009' => 'Domain name is required',
        '40010' => 'Email address is required',
        '40011' => 'Invalid reset password token or email address',
        '40013' => 'Reset password link is expired or invalid',
        
        
        // Error codes from 2000-
                '40012' => 'Token not provided',
        '40014' => 'Provided token is expired',
        '40016' => 'An error while decoding token',
        '40018' => 'Database operational error',
        '40020' => 'Sorry, you cannot add more than '.config('constants.SLIDER_LIMIT').' sliders!',
    
        // Error codes from 2000- Tenant Admin
        '20002' => 'This email address is already taken, Please try with different email address',
        '20004' => 'Error while inserting data to database',
        '20006' => 'User deletion failed',
        '20008' => 'Invalid API Key or Secret key',
        '20010' => 'API key and Secret key are required',
        '20014' => 'Unauthorised access',
        '20016' => 'Databse not found',
        '21000' => 'Error while creating database connection',
        '20018' => 'Invalid input data',
        '20024' => 'This page is already added',
        '20030' => 'Missing translation data',
        '20036' => 'Invalid translation data, please check input parameters',
        '20038' => 'The slug field is required',
        '20026' => 'Please add values for this field',
        '20028' => 'User custom field deletion failed',
        '20020' => 'CMS page deletion failed',
        '20032' => 'No data found for given id',
        '20034' => 'Invalid request parameter',
        '20102' => 'Invalid custom field input parameters or missing data',
        '100001' => 'Invalid user data',
        '100002' => 'Invalid skill data',
        // Error codes from 1000-
        '10006' => 'Database operational error',
    ]
    
];
