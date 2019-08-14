<?php
return [
    
    /**
    * Success messages
    */
    'success' => [
        'MESSAGE_TENANT_CREATED' => 'FR: Tenant created successfully',
        'MESSAGE_TENANT_UPDATED' => 'FR: Tenant details updated successfully',
        'MESSAGE_TENANT_DELETED' => 'FR: Tenant deleted successfully',
        'MESSAGE_TENANT_LISTING' => 'FR: Tenant listing successfully',
        'MESSAGE_NO_RECORD_FOUND' => 'FR: No records found',
        'MESSAGE_TENANT_FOUND' => 'FR: Tenant found successfully',
        'MESSAGE_TENANT_SETTING_LISTING' => 'FR: Tenant setting listed successfully',
        'MESSAGE_TENANT_SETTINGS_CREATED' => 'FR: Tenant settings updated successfully',
    ],
    
    /**
    * HTTP status code
    */
    'status_code' => [
        'HTTP_CREATED' => '201',
        'HTTP_NO_CONTENT' => '204',
        'HTTP_STATUS_BAD_REQUEST' => '400',
        'HTTP_STATUS_403' => '403',
        'HTTP_STATUS_NOT_FOUND' => '404',
        'HTTP_STATUS_422' => '422',
        'HTTP_STATUS_500' => '500',
        'HTTP_STATUS_BAD_GATEWAY' => '502',
        'HTTP_STATUS_METHOD_NOT_ALLOWED' => '405',
    ],
    
   /**
    * HTTP status Types
    */
    'status_type' => [
        'HTTP_STATUS_TYPE_400' => 'FR: Bad Request',
        'HTTP_STATUS_TYPE_403' => 'FR: Forbidden',
        'HTTP_STATUS_TYPE_404' => 'FR: Not Found',
        'HTTP_STATUS_TYPE_422' => 'FR: Unprocessable entity',
        'HTTP_STATUS_TYPE_500' => 'FR: Internal Server Error',
        'HTTP_STATUS_TYPE_502' => 'FR: Backend service failure (data store failure)',
        'HTTP_STATUS_TYPE_405' => 'FR: Method Not Allowed',
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
        '200001' => 'FR: Tenant name or sponsored field is empty',
        '200002' => 'FR: Tenant name is already taken, Please try with different name.',
        '200003' => 'FR: Tenant not found in the system',
        '200004' => 'FR: Database operational error',
        '200005' => 'FR: No data found',
    ]
    
];
