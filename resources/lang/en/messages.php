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
        'MESSAGE_TENANT_API_USER_LISTING' => 'Tenant api users listing successfully',        
        'MESSAGE_API_USER_FOUND' => 'Api user found successfully',
        'MESSAGE_API_USER_CREATED_SUCCESSFULLY' => 'Api user created successfully',
        'MESSAGE_API_USER_DELETED' => 'Api user deleted successfully'
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
        '200006' => 'Invalid argument',
        '200008' => 'Error while creating folder on S3 bucket',
        '200009' => 'Api user not found',

        '999999' => 'An error has occured'
    ]
    
];
