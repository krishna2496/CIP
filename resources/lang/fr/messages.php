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
        'MESSAGE_COUNTRY_LISTING' => 'FR: Country listing successfully',
    ],

        
    /**
    * API Error Codes and Message
    */
    'custom_error_message' => [
        
        // Custom error code for User Module - 100000 - 109999
        '100000' => 'FR: User does not found in system',
        '100002' => 'FR: Invalid skill data',
        '100003' => 'FR: Custom field creation failed. Please check input parameters',
        '100004' => 'FR: Requested user custom field does not exist',
        '100010' => 'FR: User creation failed. Please check input parameters',
        '100011' => 'FR: Requested skills for user does not exist',
        '100012' => 'FR: Unable to upload slider image',
        '100013' => 'FR: Invalid input data',
        '100014' => 'FR: Sorry, you cannot add more than '.config('constants.SLIDER_LIMIT').' slides!',
        
        // Custom error code for CMS Module - 300000 - 309999
        '300000' => 'FR: Page creation failed. Please check input parameters',
        '300002' => 'FR: Invalid argument',
        '300003' => 'FR: Footer page not found in the system',
        '300004' => 'FR: Database operational error',
        '300005' => 'FR: No data found',

        // Custom error code for Mission Module - 400000 - 409999
        '400000' => 'FR: Invalid application data or missing parameter',
        '400001' => 'FR: Invalid mission data or missing parameter',
        '400002' => 'FR: Error while inserting data to database',
        '400003' => 'FR: Requested mission does not exist',
        '400004' => 'FR: Mission deletion failed',
        '400006' => 'FR: Mission creation failed. Please check input parameters',

        
        // Custom error code for Tenant Authorization - 210000 - 219999
        '210000' => 'FR: Invalid API Key or Secret key',
        '210001' => 'FR: API key and Secret key are required',
        '210002' => 'FR: Email address does not exist in the system',
        '210003' => 'FR: Reset password link is expired or invalid',
        '210004' => 'FR: Invalid input data',
        '210005' => 'FR: Something went wrong while sending reset password link',
        '210006' => 'FR: Invalid reset password token or email address',
        '210007' => 'FR: Invalid password',
        '210008' => 'FR: Tenant domain does not found',
        '210009' => 'FR: Provided token is expired',
        '210010' => 'FR: An error while decoding token',
        '210012' => 'FR: Token not provided',
        

        // Custom error code for common exception
        '999999' => 'FR: An error has occured',
        
    ]
];
