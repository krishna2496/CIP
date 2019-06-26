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
    ],

        
    /**
    * API Error Codes and Message
    */
    'custom_error_message' => [
        // Custom error code for User Module - 100000 - 109999
        '100000' => 'User does not found in system',
        '100002' => 'Invalid skill data',
        '100003' => 'Custom field creation failed. Please check input parameters',
        '100004' => 'Requested user custom field does not exist',
        '100010' => 'User creation failed. Please check input parameters',
        '100011' => 'Requested skills for user does not exist',
        '100012' => 'Unable to upload slider image',
        '100013' => 'Invalid input data',
        '100014' => 'Sorry, you cannot add more than '.config('constants.SLIDER_LIMIT').' slides!',
        
        // Custom error code for CMS Module - 300000 - 309999
        '300000' => 'Page creation failed. Please check input parameters',
        '300002' => 'Invalid argument',
        '300003' => 'Footer page not found in the system',
        '300004' => 'Database operational error',
        '300005' => 'No data found',

        // Custom error code for Mission Module - 400000 - 409999
        '400000' => 'Invalid application data or missing parameter',
        '400001' => 'Invalid mission data or missing parameter',
        '400002' => 'Error while inserting data to database',
        '400003' => 'Requested mission does not exist',
        '400004' => 'Mission deletion failed',
        '400006' => 'Mission creation failed. Please check input parameters',

        
        // Custom error code for Tenant Authorization - 210000 - 219999
        '210000' => 'Invalid API Key or Secret key',
        '210001' => 'API key and Secret key are required',
        '210002' => 'Email address does not exist in the system',
        '210003' => 'Reset password link is expired or invalid',
        '210004' => 'Invalid input data',
        '210005' => 'Something went wrong while sending reset password link',
        '210006' => 'Invalid reset password token or email address',
        '210007' => 'Invalid password',
        '210008' => 'Tenant domain does not found',
        '210009' => 'Provided token is expired',
        '210010' => 'An error while decoding token',
        '210012' => 'Token not provided',
        

        // Custom error code for common exception
        '999999' => 'An error has occured',
        
    ]
];
