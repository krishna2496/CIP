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
        'MESSAGE_TENANT_API_USER_LISTING' => 'Tenant\'s API users listed successfully',        
        'MESSAGE_API_USER_FOUND' => 'API user found successfully',
        'MESSAGE_API_USER_CREATED_SUCCESSFULLY' => 'API user created successfully',
        'MESSAGE_API_USER_DELETED' => 'API user deleted successfully',
        'MESSAGE_API_USER_UPDATED_SUCCESSFULLY' => 'API user\'s secret key updated successfully',
        'MESSAGE_TENANT_OPTION_LISTING' => 'Tenant option listed successfully'
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
        '200009' => 'API user not found',
        '999999' => 'An error has occured',
        'ERROR_BOOSTRAP_SCSS_NOT_FOUND' => 'Boostrap SCSS file not found while compiling SCSS files',
    ]
    
];
