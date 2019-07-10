<?php

return [
    /**
    * API success messages
    */
    'success' => [
        'MESSAGE_USER_FOUND' => 'FR: User found successfully',
        'MESSAGE_NO_DATA_FOUND' => 'FR: No Data Found',
        'MESSAGE_USER_CREATED' => 'FR: User created successfully',
        'MESSAGE_USER_DELETED' => 'FR: User deleted successfully',
        'MESSAGE_FOOTER_PAGE_CREATED' => 'FR: Page created successfully',
        'MESSAGE_FOOTER_PAGE_UPDATED' => 'FR: Page updated successfully',
        'MESSAGE_FOOTER_PAGE_DELETED' => 'FR: Page deleted successfully',
        'MESSAGE_FOOTER_PAGE_LISTING' => 'FR: Footer pages listing successfully.',
        'MESSAGE_USER_UPDATED' => 'FR: User updated successfully',
        'MESSAGE_NO_RECORD_FOUND' => 'FR: No records found',
        'MESSAGE_USER_LISTING' => 'FR: User listing successfully',
        'MESSAGE_USER_SKILLS_CREATED' => 'FR: User skills linked successfully',
        'MESSAGE_USER_SKILLS_DELETED' => 'FR: User skills unlinked successfully',
        'MESSAGE_SLIDER_ADD_SUCCESS' => 'FR: Slider image added successfully',
        'MESSAGE_USER_LOGGED_IN' => 'FR: You are successfully logged in',
        'MESSAGE_PASSWORD_RESET_LINK_SEND_SUCCESS' => 'FR: Reset Password link is sent to your email account,link will be expire in ' . config('constants.FORGOT_PASSWORD_EXPIRY_TIME') . ' hours',
        'MESSAGE_PASSWORD_CHANGE_SUCCESS' => 'FR: Your password has been changed successfully.',
        'MESSAGE_CUSTOM_FIELD_ADDED' => 'FR: User custom field added successfully',
        'MESSAGE_CUSTOM_FIELD_UPDATED' => 'FR: User custom field updated successfully',
        'MESSAGE_CUSTOM_FIELD_DELETED' => 'FR: User custom field deleted successfully',
        'MESSAGE_APPLICATION_LISTING' => 'FR: Mission application listing successfully',
        'MESSAGE_APPLICATION_UPDATED' => 'FR: Mission application updated successfully',
        'MESSAGE_CUSTOM_STYLE_UPLOADED_SUCCESS' => 'FR: Custom styling data uploaded successfully',
        'MESSAGE_CUSTOM_STYLE_RESET_SUCCESS' => 'FR: Custom styling reset successfully',
        'MESSAGE_CUSTOM_FIELD_LISTING' => 'FR: User custom field listing successfully',
        'MESSAGE_MISSION_ADDED' => 'FR: Mission created successfully',
        'MESSAGE_MISSION_UPDATED' => 'FR: Mission updated successfully',
        'MESSAGE_MISSION_DELETED' => 'FR: Mission deleted successfully',
        'MESSAGE_MISSION_LISTING' => 'FR: Mission listing successfully',
        'MESSAGE_SKILL_LISTING' => 'FR: Skill listing successfully',
        'MESSAGE_THEME_LISTING' => 'FR: Mission theme listing successfully',
        'MESSAGE_CITY_LISTING' => 'FR: City listing successfully',    
        'MESSAGE_MISSION_ADDED_TO_FAVOURITE' => 'FR: Mission added to favourite',
        'MESSAGE_MISSION_DELETED_FROM_FAVOURITE' => 'FR: Mission removed from favourite',
        'MESSAGE_COUNTRY_LISTING' => 'FR: Country listing successfully',
        'MESSAGE_MISSION_FOUND' => 'FR: Mission found successfully',
        'MESSAGE_PAGE_FOUND' => 'FR: Page found successfully',
        'MESSAGE_ASSETS_FILES_LISTING' => 'FR: Assets files listing successfully',
        'MESSAGE_TENANT_SETTING_UPDATE_SUCCESSFULLY' => 'FR: Settings has been update successfully',
        'MESSAGE_TENANT_SETTINGS_LISTING' => 'FR: Settings listing successfully',
		'MESSAGE_THEME_CREATED' => 'FR: Mission theme created successfully',
        'MESSAGE_THEME_UPDATED' => 'FR: Mission theme updated successfully',
        'MESSAGE_THEME_DELETED' => 'FR: Mission theme deleted successfully',
        'MESSAGE_THEME_FOUND' => 'FR: Mission theme found successfully',
        'MESSAGE_SKILL_CREATED' => 'FR: Skill created successfully',
        'MESSAGE_SKILL_UPDATED' => 'FR: Skill updated successfully',
        'MESSAGE_SKILL_DELETED' => 'FR: Skill deleted successfully',
        'MESSAGE_SKILL_FOUND' => 'FR: Skill found successfully',
        'MESSAGE_THEME_FOUND' => 'FR: Mission theme found successfully',
        'MESSAGE_TENANT_OPTION_CREATED' => 'FR: Tenant option created successfully',
        'MESSAGE_TENANT_OPTION_UPDATED' => 'FR: Tenant option update successfully',
        'MESSAGE_TENANT_OPTIONS_LIST' => 'FR: Tenant options listing successfully',
        'MESSAGE_MISSION_RATING_LISTING' => 'FR: Get mission rating successfully',
    ],

        
    /**
    * API Error Codes and Message
    */
    'custom_error_message' => [
        // Custom error code for User Module - 100000 - 109999
        'ERROR_USER_NOT_FOUND' => 'FR: User does not found in system',
        'ERROR_SKILL_INVALID_DATA' => 'FR: Invalid skill data',
        'ERROR_USER_CUSTOM_FIELD_INVALID_DATA' => 'FR: Custom field creation failed. Please check input parameters',
        'ERROR_USER_CUSTOM_FIELD_NOT_FOUND' => 'FR: Requested user custom field does not exist',
        'ERROR_USER_INVALID_DATA' => 'FR: User creation failed. Please check input parameters',
        'ERROR_USER_SKILL_NOT_FOUND' => 'FR: Requested skills for user does not exist',
        'ERROR_SLIDER_IMAGE_UPLOAD' => 'FR: Unable to upload slider image',
        'ERROR_SLIDER_INVALID_DATA' => 'FR: Invalid input data',
        'ERROR_SLIDER_LIMIT' => 'FR: Sorry, you cannot add more than '.config('constants.SLIDER_LIMIT').' slides!',
        'ERROR_NOT_VALID_EXTENSION' => 'FR: File must have .scss type',
        'ERROR_FILE_NAME_NOT_MATCHED_WITH_STRUCTURE' => 'FR: File name doesn`t match with structure',
        
        // Custom error code for CMS Module - 300000 - 309999
        'ERROR_INVALID_ARGUMENT' => 'FR: Invalid argument',
        'ERROR_FOOTER_PAGE_NOT_FOUND' => 'FR: Footer page not found in the system',
        'ERROR_DATABASE_OPERATIONAL' => 'FR: Database operational error',
        'ERROR_NO_DATA_FOUND' => 'FR: No data found',

        // Custom error code for Mission Module - 400000 - 409999
        'ERROR_INVALID_MISSION_APPLICATION_DATA' => 'FR: Invalid application data or missing parameter',
        'ERROR_INVALID_MISSION_DATA' => 'FR: Invalid mission data or missing parameter',
        'ERROR_MISSION_NOT_FOUND' => 'FR: Requested mission does not exist',
        'ERROR_MISSION_DELETION' => 'FR: Mission deletion failed',
        'ERROR_MISSION_REQUIRED_FIELDS_EMPTY' => 'FR: Mission creation failed. Please check input parameters',
        'ERROR_NO_MISSION_FOUND' => 'FR: Mission does not found in system',
        'ERROR_THEME_INVALID_DATA' => 'FR: Mission theme creation failed. Please check input parameters',
        'ERROR_THEME_NOT_FOUND' => 'FR: Mission Theme does not found in system',
        'ERROR_SKILL_NOT_FOUND' => 'FR: Skill does not found in system',
        
        
        // Custom error code for Tenant Authorization - 210000 - 219999
        'ERROR_INVALID_API_AND_SECRET_KEY' => 'FR: Invalid API Key or Secret key',
        'ERROR_API_AND_SECRET_KEY_REQUIRED' => 'FR: API key and Secret key are required',
        'ERROR_EMAIL_NOT_EXIST' => 'FR: Email address does not exist in the system',
        'ERROR_INVALID_RESET_PASSWORD_LINK' => 'FR: Reset password link is expired or invalid',
        'ERROR_RESET_PASSWORD_INVALID_DATA' => 'FR: Invalid input data',
        'ERROR_SEND_RESET_PASSWORD_LINK' => 'FR: Something went wrong while sending reset password link',
        'ERROR_INVALID_DETAIL' => 'FR: Invalid reset password token or email address',
        'ERROR_INVALID_PASSWORD' => 'FR: Invalid password',
        'ERROR_TENANT_DOMAIN_NOT_FOUND' => 'FR: Tenant domain does not found',
        'ERROR_TOKEN_EXPIRED' => 'FR: Provided token is expired',
        'ERROR_IN_TOKEN_DECODE' => 'FR: An error while decoding token',
        'ERROR_TOKEN_NOT_PROVIDED' => 'FR: Token not provided',
        

        // Custom error code for common exception
        'ERROR_OCCURRED' => 'FR: An error has occurred',
        'ERROR_INVALID_JSON' => 'FR: Invalid Json format',
        
        // Custom erro code for other errors - 800000 - 809999
        'ERROR_ON_UPDATING_STYLING_VARIBLE_IN_DATABASE' => 'FR: An error has occured, while updating colors in database',
        'ERROR_WHILE_DOWNLOADING_FILES_FROM_S3_TO_LOCAL' => 'FR: File is failed to download from S3 to local',
        'ERROR_WHILE_COMPILING_SCSS_FILES' => 'FR: An error has occured, while compiling SCSS files to update SCSS changes',
        'ERROR_WHILE_STORE_COMPILED_CSS_FILE_TO_LOCAL' => 'FR: An error has occured, while storing compiled css file to local storage',
        'ERROR_NO_FILES_FOUND_TO_UPLOAD_ON_S3_BUCKET' => 'FR: No files found to upload on s3 bucket',
        'ERROR_FAILD_TO_UPLOAD_COMPILE_FILE_ON_S3' => 'FR: Failed to upload files on S3',
        'ERROR_FAILED_TO_RESET_STYLING' => 'FR: Failed to reset styling settings',
        'ERROR_DEFAULT_THEME_FOLDER_NOT_FOUND' => 'FR: Default theme folder not found on server',
        'ERROR_NO_FILES_FOUND_TO_DOWNLOAD' => 'FR: No assets file found on S3 for tenant',
        'ERROR_TENANT_ASSET_FOLDER_NOT_FOUND_ON_S3' => 'FR: Tenant asset folder not found',
        'ERROR_NO_FILES_FOUND_IN_ASSETS_FOLDER' => 'FR: No files found on S3 assets folder for this tenant',
        'ERROR_BOOSTRAP_SCSS_NOT_FOUND' => 'FR: Boostrap SCSS file not found while compiling SCSS files',
        'ERROR_SETTING_FOUND' => 'FR: Setting not found',
        'ERROR_IMAGE_FILE_NOT_FOUND_ON_S3' => 'FR: Image file not found on S3 server',
        'ERROR_WHILE_UPLOADING_IMAGE_ON_S3' => 'FR: An error while uploading image on S3',
        'ERROR_DOWNLOADING_IMAGE_TO_LOCAL' => 'FR: An error while downloading image from S3 to server',
        'ERROR_IMAGE_UPLOAD_INVALID_DATA' => 'FR: Invalid input file',
        'ERROR_TENANT_OPTION_NOT_FOUND' => 'FR: No tenant option found'
    ]
];
