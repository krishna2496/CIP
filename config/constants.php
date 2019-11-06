<?php
    return [
        'PER_PAGE_LIMIT' => '10',
        'PER_PAGE_MAX' => '50',
        'error_codes' => [
            'ERROR_TENANT_REQUIRED_FIELDS_EMPTY' => '200001',
            'ERROR_TENANT_ALREADY_EXIST' => '200002',
            'ERROR_TENANT_NOT_FOUND' => '200003',
            'ERROR_DATABASE_OPERATIONAL' => '200004',
            'ERROR_NO_DATA_FOUND' => '200004',
            'ERROR_INVALID_ARGUMENT' => '200006',
            'FAILED_TO_CREATE_FOLDER_ON_S3' => '200008',
            'ERROR_API_USER_NOT_FOUND' => '200009',
            'ERROR_BOOSTRAP_SCSS_NOT_FOUND' => '200010',
            'ERROR_OCCURRED' => '999999',
            'ERROR_INVALID_JSON' => '900000',
            'ERROR_WHILE_STORE_COMPILED_CSS_FILE_TO_LOCAL' => '200011',
            'ERROR_FAILD_TO_UPLOAD_COMPILE_FILE_ON_S3' => '200012',
            'ERROR_WHILE_COMPILING_SCSS_FILES' => '200013',
            'ERROR_LANGUAGE_NOT_FOUND' => '200021',
            'ERROR_LANGUAGE_REQUIRED_FIELDS_EMPTY' => '200022',
            'ERROR_TENANT_LANGUAGE_REQUIRED_FIELDS_EMPTY' => '200101',
            'ERROR_LANGUAGE_NOT_ACTIVE' => '200102',
            'ERROR_TENANT_LANGUAGE_NOT_FOUND' => '200103',
            'ERROR_TENANT_DEFAULT_LANGUAGE_REQUIRED' => '200104',
            'ERROR_ACTIVITY_LOG_REQUIRED_FIELDS_EMPTY' => '200106',
        ],
        'background_process_status' => [
            'PENDING' => '0',
            'COMPLETED' => '1',
            'IN_PROGRESS' => '2',
            'FAILED' => '-1'
        ],
        'AWS_S3_BUCKET_NAME' => 'optimy-dev-tatvasoft',
        'AWS_S3_DEFAULT_THEME_FOLDER_NAME' => 'default_theme',
        'AWS_S3_ASSETS_FOLDER_NAME' => 'assets',
        'AWS_S3_IMAGES_FOLDER_NAME' => 'images',
        'AWS_S3_SCSS_FOLDER_NAME' => 'scss',
        'AWS_S3_LOGO_IMAGE_NAME' => 'logo.png',
        'EMAIL_TEMPLATE_FOLDER' => 'emails',
        'EMAIL_TEMPLATE_JOB_NOTIFICATION' => 'tenant-notification',
        'ADMIN_EMAIL_ADDRESS' => 'siddharajsinh.zala@tatvasoft.com',
        'activity_log_types' => [
            'TENANT' => 'TENANT',
            'API_USER' => 'API_USER',
            'API_USER_KEY_RENEW' => 'API_USER_KEY_RENEW',
            'TENANT_SETTINGS' => 'TENANT_SETTINGS',
            'LANGUAGE' => 'LANGUAGE',
            'TENANT_LANGUAGE' => 'TENANT_LANGUAGE',
        ],
        'activity_log_actions' => [
            'CREATED' => 'CREATED',
            'UPDATED' => 'UPDATED',
            'DELETED' => 'DELETED',
            'ENABLED' => 'ENABLED',
            'DISABLED' => 'DISABLED'
        ],
        'EMAIL_TESTING_TEMPLATE' => 'test-email',
        'ADMIN_EMAIL_ADDRESS' => 'siddharajsinh.zala@tatvasoft.com',
        'language_status' => [
            'ACTIVE' => '1',
            'INACTIVE' => '0'
        ]
    ];
