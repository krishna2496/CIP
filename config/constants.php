<?php
    return [
        'PER_PAGE_LIMIT' => '10',
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
        'ADMIN_EMAIL_ADDRESS' => 'siddharajsinh.zala@tatvasoft.com'
    ];
