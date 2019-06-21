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
        'MESSAGE_NO_RECORD_FOUND' => 'No records found',
        'MESSAGE_TENANT_FOUND' => 'Tenant found successfully',
    ],
    
    /**
    * HTTP status code
    */
    'status_code' => [
        'HTTP_CREATED' => '201',
        'HTTP_NO_CONTENT' => '204',
        'HTTP_STATUS_BAD_REQUEST' => '400',
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
        'ERROR_200001' => '200001',
        'ERROR_200002' => '200002',
        'ERROR_200003' => '200003',
        'ERROR_200004' => '200004',
    ],
    
    /**
    * API Error Codes and Message
    */
    'custom_error_message' => [
        '200001' => 'Tenant name or sponsored field is empty',
        '200002' => 'Tenant name is already taken, Please try with different name.',
        '200003' => 'Tenant not found in the system',
        '200004' => 'Database operational error',
        '200005' => 'No data found',
    ]
    
];
