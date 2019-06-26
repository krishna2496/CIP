<?php

return [
    
    /*
     * constants to use any where in system
     */
    'TENANT_OPTION_SLIDER' => 'slider',
    'FORGOT_PASSWORD_EXPIRY_TIME' => '4',
    'SLIDER_LIMIT' => '4',
    'SLIDER_IMAGE_PATH' => 'images/',
    'ACTIVE' => 1,
    'DB_DATE_FORMAT' => 'Y-m-d H:i:s',
    'PER_PAGE_LIMIT' => '10',
    'FRONT_DATE_FORMAT' => 'd/m/Y',
    
    /*
     * User custom field types
     */
     'custom_field_types' => [
        'TEXT' => 'text',
        'EMAIL' => 'email',
        'DROP-DOWN' => 'drop-down',
        'RADIO' => 'radio'
     ],
     
     /*
      * Language constants
      */
     
    'DEFAULT_LANGUAGE' => 'EN',
    'FRONTEND_LANGUAGE_FOLDER' => 'front_end',

    
    /*
     * Mission types
     */
    'mission_type' => [
        'TIME' => 'TIME',
        'GOAL' => 'GOAL'
    ],

    /*
     * Publication status
     */
    'publication_status' => [
        'DRAFT' => 'DRAFT',
        'PENDING_APPROVAL' => 'PENDING_APPROVAL',
        'REFUSED' => 'REFUSED',
        'APPROVED' => 'APPROVED',
        'PUBLISHED_FOR_VOTING' => 'PUBLISHED_FOR_VOTING',
        'PUBLISHED_FOR_APPLYING' => 'PUBLISHED_FOR_APPLYING',
        'UNPUBLISHED' => 'UNPUBLISHED'
    ],

    /*
     * Image types
     */
    'image_types' => [
        'JPG' => 'jpg',
        'JPEG' => 'jpeg',
        'PNG' => 'png'
    ],

    /*
     * Document types
     */
    'document_types' => [
        'DOC' => 'doc',
        'DOCX' => 'docx',
        'XLS' => 'xls',
        'XLSX' => 'xlsx',
        'PDF' => 'pdf',
        'TXT' => 'txt'
    ],

    /*
     * Application status
     */
    'application_status' => [
        'AUTOMATICALLY_APPROVED' => 'AUTOMATICALLY_APPROVED',
        'PENDING' => 'PENDING',
        'REFUSED' => 'REFUSED'
    ],

    'error_codes' => [
        'ERROR_FOOTER_PAGE_REQUIRED_FIELDS_EMPTY' => '300000',
        'ERROR_INVALID_ARGUMENT' => '300002',
        'ERROR_FOOTER_PAGE_NOT_FOUND' => '300003',
        'ERROR_DATABASE_OPERATIONAL' => '300004',
        'ERROR_NO_DATA_FOUND' => '300005',

        'ERROR_USER_NOT_FOUND' => '100000',
        'ERROR_SKILL_INVALID_DATA' => '100002',
        'ERROR_USER_CUSTOM_FIELD_INVALID_DATA' => '100003',
        'ERROR_USER_CUSTOM_FIELD_NOT_FOUND' => '100004',
        'ERROR_USER_INVALID_DATA' => '100010',
        'ERROR_USER_SKILL_NOT_FOUND' => '100011',
        'ERROR_SLIDER_INVALID_DATA' => '100013',
        'ERROR_SLIDER_LIMIT' => '100014',

        'ERROR_EMAIL_NOT_EXIST' => '210002',
        'ERROR_INVALID_RESET_PASSWORD_LINK' => '210003',
        'ERROR_RESET_PASSWORD_INVALID_DATA' => '210004',
        'ERROR_SEND_RESET_PASSWORD_LINK' => '210005',
        'ERROR_INVALID_DETAIL' => '210006',
        'ERROR_INVALID_PASSWORD' => '210007',
        'ERROR_INVALID_PASSWORD' => '210007',
        

        
    ]
    
];
