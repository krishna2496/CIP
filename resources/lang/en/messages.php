<?php

return [
    /**
    * API success messages
    */
    'success' => [
        'MESSAGE_USER_FOUND' => 'User found successfully',
        'MESSAGE_NO_DATA_FOUND' => 'No Data Found',
        'MESSAGE_USER_CREATED' => 'User created successfully',
        'MESSAGE_USER_DELETED' => 'User deleted successfully',
        'MESSAGE_FOOTER_PAGE_CREATED' => 'Page created successfully',
        'MESSAGE_FOOTER_PAGE_UPDATED' => 'Page updated successfully',
        'MESSAGE_FOOTER_PAGE_DELETED' => 'Page deleted successfully',
        'MESSAGE_FOOTER_PAGE_LISTING' => 'Footer pages listing successfully.',
        'MESSAGE_USER_UPDATED' => 'User updated successfully',
        'MESSAGE_NO_RECORD_FOUND' => 'No records found',
        'MESSAGE_USER_LISTING' => 'User listing successfully',
        'MESSAGE_USER_SKILLS_CREATED' => 'User skills linked successfully',
        'MESSAGE_USER_SKILLS_DELETED' => 'User skills unlinked successfully',
        'MESSAGE_SLIDER_ADD_SUCCESS' => 'Slider image added successfully',
        'MESSAGE_USER_LOGGED_IN' => 'You are successfully logged in',
        'MESSAGE_PASSWORD_RESET_LINK_SEND_SUCCESS' => 'Reset Password link is sent to your email account,link will be expire in ' . config('constants.FORGOT_PASSWORD_EXPIRY_TIME') . ' hours',
        'MESSAGE_PASSWORD_CHANGE_SUCCESS' => 'Your password has been changed successfully.',
        'MESSAGE_CUSTOM_FIELD_ADDED' => 'User custom field added successfully',
        'MESSAGE_CUSTOM_FIELD_UPDATED' => 'User custom field updated successfully',
        'MESSAGE_CUSTOM_FIELD_DELETED' => 'User custom field deleted successfully',
        'MESSAGE_APPLICATION_LISTING' => 'Mission application listing successfully',
        'MESSAGE_APPLICATION_UPDATED' => 'Mission application updated successfully',
        'MESSAGE_CUSTOM_STYLE_UPLOADED_SUCCESS' => 'Custom styling data uploaded successfully',
        'MESSAGE_CUSTOM_STYLE_RESET_SUCCESS' => 'Custom styling reset successfully',
        'MESSAGE_CUSTOM_FIELD_LISTING' => 'User custom field listing successfully',
        'MESSAGE_MISSION_ADDED' => 'Mission created successfully',
        'MESSAGE_MISSION_UPDATED' => 'Mission updated successfully',
        'MESSAGE_MISSION_DELETED' => 'Mission deleted successfully',
        'MESSAGE_MISSION_LISTING' => 'Mission listing successfully',
        'MESSAGE_SKILL_LISTING' => 'Skill listing successfully',
        'MESSAGE_THEME_LISTING' => 'Mission theme listing successfully',
        'MESSAGE_CITY_LISTING' => 'City listing successfully',
        'MESSAGE_COUNTRY_LISTING' => 'Country listing successfully',
        'MESSAGE_MISSION_FOUND' => 'Mission found successfully',
        'MESSAGE_PAGE_FOUND' => 'Page found successfully',
        'MESSAGE_ASSETS_FILES_LISTING' => "Assets files listing successfully",
        'MESSAGE_TENANT_SETTING_UPDATE_SUCCESSFULLY' => 'Settings has been update successfully',
        'MESSAGE_TENANT_SETTINGS_LISTING' => 'Settings listing successfully',
		'MESSAGE_THEME_CREATED' => 'Mission theme created successfully',
        'MESSAGE_THEME_UPDATED' => 'Mission theme updated successfully',
        'MESSAGE_THEME_DELETED' => 'Mission theme deleted successfully',
        'MESSAGE_THEME_FOUND' => 'Mission theme found successfully',
        'MESSAGE_SKILL_CREATED' => 'Skill created successfully',
        'MESSAGE_SKILL_UPDATED' => 'Skill updated successfully',
        'MESSAGE_SKILL_DELETED' => 'Skill deleted successfully',
        'MESSAGE_SKILL_FOUND' => 'Skill found successfully',
        'MESSAGE_THEME_FOUND' => 'Mission theme found successfully',
        'MESSAGE_TENANT_OPTION_CREATED' => 'Tenant option created successfully',
        'MESSAGE_TENANT_OPTION_UPDATED' => 'Tenant option update successfully',
        'MESSAGE_TENANT_OPTIONS_LIST' => 'Tenant options listing successfully',
        'MESSAGE_MISSION_RATING_LISTING' => 'Get mission rating successfully',
    ],

        
    /**
    * API Error Codes and Message
    */
    'custom_error_message' => [
        // Custom error code for User Module - 100000 - 109999
        'ERROR_USER_NOT_FOUND' => 'User does not found in system',
        'ERROR_SKILL_INVALID_DATA' => 'Invalid skill data',
        'ERROR_USER_CUSTOM_FIELD_INVALID_DATA' => 'Custom field creation failed. Please check input parameters',
        'ERROR_USER_CUSTOM_FIELD_NOT_FOUND' => 'Requested user custom field does not exist',
        'ERROR_USER_INVALID_DATA' => 'User creation failed. Please check input parameters',
        'ERROR_USER_SKILL_NOT_FOUND' => 'Requested skills for user does not exist',
        'ERROR_SLIDER_IMAGE_UPLOAD' => 'Unable to upload slider image',
        'ERROR_SLIDER_INVALID_DATA' => 'Invalid input data',
        'ERROR_SLIDER_LIMIT' => 'Sorry, you cannot add more than '.config('constants.SLIDER_LIMIT').' slides!',
        'ERROR_NOT_VALID_EXTENSION' => 'File must have .scss type',
        'ERROR_FILE_NAME_NOT_MATCHED_WITH_STRUCTURE' => 'File name doesn`t match with structure',
        
        // Custom error code for CMS Module - 300000 - 309999
        'ERROR_INVALID_ARGUMENT' => 'Invalid argument',
        'ERROR_FOOTER_PAGE_NOT_FOUND' => 'Footer page not found in the system',
        'ERROR_DATABASE_OPERATIONAL' => 'Database operational error',
        'ERROR_NO_DATA_FOUND' => 'No data found',

        // Custom error code for Mission Module - 400000 - 409999
        'ERROR_INVALID_MISSION_APPLICATION_DATA' => 'Invalid application data or missing parameter',
        'ERROR_INVALID_MISSION_DATA' => 'Invalid mission data or missing parameter',
        'ERROR_MISSION_NOT_FOUND' => 'Requested mission does not exist',
        'ERROR_MISSION_DELETION' => 'Mission deletion failed',
        'ERROR_MISSION_REQUIRED_FIELDS_EMPTY' => 'Mission creation failed. Please check input parameters',
        'ERROR_NO_MISSION_FOUND' => 'Mission does not found in system',
        'ERROR_THEME_INVALID_DATA' => 'Mission theme creation failed. Please check input parameters',
        'ERROR_THEME_NOT_FOUND' => 'Mission Theme does not found in system',
        'ERROR_SKILL_NOT_FOUND' => 'Skill does not found in system',
        
        
        // Custom error code for Tenant Authorization - 210000 - 219999
        'ERROR_INVALID_API_AND_SECRET_KEY' => 'Invalid API Key or Secret key',
        'ERROR_API_AND_SECRET_KEY_REQUIRED' => 'API key and Secret key are required',
        'ERROR_EMAIL_NOT_EXIST' => 'Email address does not exist in the system',
        'ERROR_INVALID_RESET_PASSWORD_LINK' => 'Reset password link is expired or invalid',
        'ERROR_RESET_PASSWORD_INVALID_DATA' => 'Invalid input data',
        'ERROR_SEND_RESET_PASSWORD_LINK' => 'Something went wrong while sending reset password link',
        'ERROR_INVALID_DETAIL' => 'Invalid reset password token or email address',
        'ERROR_INVALID_PASSWORD' => 'Invalid password',
        'ERROR_TENANT_DOMAIN_NOT_FOUND' => 'Tenant domain does not found',
        'ERROR_TOKEN_EXPIRED' => 'Provided token is expired',
        'ERROR_IN_TOKEN_DECODE' => 'An error while decoding token',
        'ERROR_TOKEN_NOT_PROVIDED' => 'Token not provided',
        

        // Custom error code for common exception
        'ERROR_OCCURRED' => 'An error has occurred',
        'ERROR_INVALID_JSON' => 'Invalid Json format',
        
        // Custom erro code for other errors - 800000 - 809999
        'ERROR_ON_UPDATING_STYLING_VARIBLE_IN_DATABASE' => "An error has occured, while updating colors in database",
        'ERROR_WHILE_DOWNLOADING_FILES_FROM_S3_TO_LOCAL' => "File is failed to download from S3 to local",
        'ERROR_WHILE_COMPILING_SCSS_FILES' => 'An error has occured, while compiling SCSS files to update SCSS changes',
        'ERROR_WHILE_STORE_COMPILED_CSS_FILE_TO_LOCAL' => 'An error has occured, while storing compiled css file to local storage',
        'ERROR_NO_FILES_FOUND_TO_UPLOAD_ON_S3_BUCKET' => 'No files found to upload on s3 bucket',
        'ERROR_FAILD_TO_UPLOAD_COMPILE_FILE_ON_S3' => 'Failed to upload files on S3',
        'ERROR_FAILED_TO_RESET_STYLING' => 'Failed to reset styling settings',
        'ERROR_DEFAULT_THEME_FOLDER_NOT_FOUND' => 'Default theme folder not found on server',
        'ERROR_NO_FILES_FOUND_TO_DOWNLOAD' => 'No assets file found on S3 for tenant',
        'ERROR_TENANT_ASSET_FOLDER_NOT_FOUND_ON_S3' => 'Tenant asset folder not found',
        'ERROR_NO_FILES_FOUND_IN_ASSETS_FOLDER' => 'No files found on S3 assets folder for this tenant',
        'ERROR_BOOSTRAP_SCSS_NOT_FOUND' => 'Boostrap SCSS file not found while compiling SCSS files',
        'ERROR_SETTING_FOUND' => 'Setting not found',
        'ERROR_IMAGE_FILE_NOT_FOUND_ON_S3' => 'Image file not found on S3 server',
        'ERROR_WHILE_UPLOADING_IMAGE_ON_S3' => 'An error while uploading image on S3',
        'ERROR_DOWNLOADING_IMAGE_TO_LOCAL' => 'An error while downloading image from S3 to server',
        'ERROR_IMAGE_UPLOAD_INVALID_DATA' => 'Invalid input file',
        'ERROR_TENANT_OPTION_NOT_FOUND' => 'No tenant option found'
    ]
];
