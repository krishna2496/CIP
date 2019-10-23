<?php
return [
    
    /**
    * Success messages
    */
    'success' => [
        'MESSAGE_TENANT_CREATED' => 'Tenant created successfully',
        'MESSAGE_TENANT_UPDATED' => 'Tenant details updated successfully',
        'MESSAGE_TENANT_DELETED' => 'Tenant deleted successfully',
        'MESSAGE_TENANT_LISTING' => 'Tenants listed successfully',
        'MESSAGE_NO_RECORD_FOUND' => 'No records found',
        'MESSAGE_TENANT_FOUND' => 'Tenant found successfully',
        'MESSAGE_TENANT_API_USER_LISTING' => 'Tenant\'s API users listed successfully',        
        'MESSAGE_API_USER_FOUND' => 'API user found successfully',
        'MESSAGE_API_USER_CREATED_SUCCESSFULLY' => 'API user created successfully',
        'MESSAGE_API_USER_DELETED' => 'API user deleted successfully',
        'MESSAGE_API_USER_UPDATED_SUCCESSFULLY' => 'API user\'s secret key updated successfully',
        'MESSAGE_TENANT_SETTING_LISTING' => 'Tenant setting listed successfully',
        'MESSAGE_TENANT_SETTINGS_UPDATED' => 'Tenant settings updated successfully',
        'MESSAGE_ALL_SETTING_LISTING' => 'All settings listed successfully',
        'MESSAGE_LANGUAGE_FOUND' => 'Language found successfully',
        'MESSAGE_LANGUAGE_LISTING' => 'Language listed sucessfully',
        'MESSAGE_LANGUAGE_CREATED' => 'Language added sucessfully',
        'MESSAGE_LANGUAGE_UPDATED' => 'Language details updated sucessfully',
        'MESSAGE_NEWS_DELETED' => 'Language deleted sucessfully',
        'MESSAGE_TENANT_LANGUAGE_ADDED' => 'Tenant language added sucessfully',
        'MESSAGE_TENANT_LANGUAGE_UPDATED' => 'Tenant language updated sucessfully',
        'MESSAGE_TENANT_LANGUAGE_LISTING' => 'Tenant language listed sucessfully',
        'MESSAGE_TENANT_LANGUAGE_DELETED' => 'Tenant language deleted sucessfully',
    ],
    
    /**
    * API Error Codes and Message
    */
    'custom_error_message' => [
        'ERROR_TENANT_REQUIRED_FIELDS_EMPTY' => 'Tenant name or sponsored field is empty',
        'ERROR_TENANT_ALREADY_EXIST' => 'Tenant name is already taken, Please try with different name.',
        'ERROR_TENANT_NOT_FOUND' => 'Tenant not found in the system',
        'ERROR_DATABASE_OPERATIONAL' => 'Database operational error',
        'ERROR_NO_DATA_FOUND' => 'No data found',
        'ERROR_INVALID_ARGUMENT' => 'Invalid argument',
        'FAILED_TO_CREATE_FOLDER_ON_S3' => 'Error while creating folder on S3 bucket',
        'ERROR_API_USER_NOT_FOUND' => 'API user not found',
        'ERROR_OCCURRED' => 'An error has occured',
        'ERROR_BOOSTRAP_SCSS_NOT_FOUND' => 'Boostrap SCSS file not found while compiling SCSS files',
        'ERROR_INVALID_JSON' => 'Format Json invalid',
        'ERROR_WHILE_STORE_COMPILED_CSS_FILE_TO_LOCAL' => 'Error while storing compiled CSS to local',
        'ERROR_FAILD_TO_UPLOAD_COMPILE_FILE_ON_S3' => 'Error while uploading compiled CSS to S3',
        'ERROR_WHILE_COMPILING_SCSS_FILES' => 'Error while compiling SCSS files',
        'ERROR_LANGUAGE_NOT_FOUND' => 'Language not found in the system',
        'ERROR_LANGUAGE_NOT_ACTIVE' => 'Selected language is not active',
        'ERROR_TENANT_LANGUAGE_NOT_FOUND' => 'Tenant language not found in the system',
        'ERROR_TENANT_DEFAULT_LANGUAGE_REQUIRED' => 'Atleast one default language is required',
    ],
    'email_text' => [
        'ERROR' => 'Error',
        'SUCCESS' => 'Success',
        'JOB_FOR' => 'Job For ',
        'PASSED' => 'Passed',
        'FAILED' => 'Failed',
        'TENANT' => 'tenant',
        'BACKGROUND_JOB_NAME' => 'Background Job Name',
        'BACKGROUND_JOB_STATUS' => 'Background Job Status',
        'COMPILE_SCSS_FILES' => 'Compile SCSS Files',
        'CREATE_FOLDER_ON_S3_BUCKET' => 'Create Folder On S3 Bucket',
        'TENANT_DEFAULT_LANGUAGE' => 'Tenant Default Language',
        'TENANT_MIGRATION' => 'Tenant Migration',
        'JOB_PASSED_SUCCESSFULLY' => 'Job passed successufully',
        'ALL_JOBS_EXECUTED_SUCCESSFULLY' => 'All background jobs executed successfully',
        'ON_BACKGROUND_JOBS' => 'On background jobs for',
        'MESSAGE' => 'Message',
        'BACKGROUND_JOB_EXCEPTION_MESSAGE' => 'Background job exception message'
    ]
    
];
