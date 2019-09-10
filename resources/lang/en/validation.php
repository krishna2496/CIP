<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'The :attribute must be accepted.',
    'active_url'           => 'The :attribute is not a valid URL.',
    'after'                => 'The :attribute must be a date after :date.',
    'alpha'                => 'The :attribute may only contain letters.',
    'alpha_dash'           => 'The :attribute may only contain letters, numbers, and dashes.',
    'alpha_num'            => 'The :attribute may only contain letters and numbers.',
    'array'                => 'The :attribute must be an array.',
    'before'               => 'The :attribute must be a date before :date.',
    'between'              => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file'    => 'The :attribute must be between :min and :max kilobytes.',
        'string'  => 'The :attribute must be between :min and :max characters.',
        'array'   => 'The :attribute must have between :min and :max items.',
    ],
    'boolean'              => 'The :attribute field must be true or false.',
    'confirmed'            => 'The :attribute confirmation does not match.',
    'date'                 => 'The :attribute is not a valid date.',
    'date_format'          => 'The :attribute does not match the format :format.',
    'different'            => 'The :attribute and :other must be different.',
    'digits'               => 'The :attribute must be :digits digits.',
    'digits_between'       => 'The :attribute must be between :min and :max digits.',
    'email'                => 'The :attribute must be a valid email address.',
    'filled'               => 'The :attribute field is required.',
    'exists'               => 'The selected :attribute is invalid.',
    'image'                => 'The :attribute must be an image.',
    'in'                   => 'The selected :attribute is invalid.',
    'integer'              => 'The :attribute must be an integer.',
    'ip'                   => 'The :attribute must be a valid IP address.',
    'max'                  => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file'    => 'The :attribute may not be greater than :max kilobytes.',
        'string'  => 'The :attribute may not be greater than :max characters.',
        'array'   => 'The :attribute may not have more than :max items.',
    ],
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => 'The :attribute must be at least :min.',
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'string'  => 'The :attribute must be at least :min characters.',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'not_in'               => 'The selected :attribute is invalid.',
    'numeric'              => 'The :attribute must be a number.',
    'regex'                => 'The :attribute format is invalid.',
    'required'             => 'The :attribute field is required.',
    'required_if'          => 'The :attribute field is required when :other is :value.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'timezone'             => 'The :attribute must be a valid zone.',
    'unique'               => 'The :attribute has already been taken.',
    'url'                  => 'The :attribute format is invalid.',
    'present'              => 'The :attribute field is required',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'media_images.*.media_path' => [
            'valid_media_path' => 'Please enter valid media image',
        ],
        'documents.*.document_path' => [
            'valid_document_path' => 'Please enter valid document file',
        ],
		'media_videos.*.media_path' => [
            'valid_video_url' => 'Please enter valid youtube url',
        ],
		'avatar' => [
            'valid_profile_image' => 'Invalid image file or image type is not allowed. Allowed types: png, jpeg, jpg',
        ],
		'parent_skill' => [
            'valid_parent_skill' => 'Invalid parent skill',
        ],
        'url' => [
            'valid_media_path' => 'Please enter valid image url',
        ],
        'linked_in_url' => [
            'valid_linkedin_url' => 'Please enter valid linkedIn url',
        ],
        'documents.*' => [
            'valid_timesheet_document_type' => 'Please select valid timesheet documents',
            'max' => 'Document file size must be ' .
            (config('constants.TIMESHEET_DOCUMENT_SIZE_LIMIT') / 1024) . 'mb or below',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'page_details.slug' => 'slug',
        'page_details.translations' => 'translations',
        'page_details.translations.*.lang' => 'language code',
        'page_details.translations.*.title' => 'title',
        'page_details.translations.*.sections' => 'sections',
        'translations.*.values' => 'values',
        'media_images.*.media_name' => 'media name',
        'media_images.*.media_type' => 'media type',
        'media_images.*.media_path' => 'media path',
        'media_videos.*.media_name' => 'media name',
        'media_videos.*.media_type' => 'media type',
        'media_videos.*.media_path' => 'media path',
        'documents.*.document_name' => 'document name',
        'documents.*.document_type' => 'document type',
        'documents.*.document_path' => 'document path',        
        'slider_detail.translations.*.lang' => 'language code',
        'skills.*.skill_id' => 'skill id',  
        'location.city' => 'city', 
        'location.country' => 'country',   
        'password_confirmation' => 'confirm password',         
        'translations.*.lang' => 'language code',         
        'is_mandatory' => 'mandatory',       
		'page_details.translations.*.sections.*.title' => 'title',
		'page_details.translations.*.sections.*.description' => 'description',
		'location.city_id' => 'city',
		'location.country_code' => 'country code',
		'organisation.organisation_id' => 'organisation id',
		'mission_detail.*.lang' => 'language code',
        'to_user_id' => 'user id',
        'custom_fields.*.field_id' => 'field id',
        'settings.*.tenant_setting_id' => 'tenant setting id',
        'settings.*.value' => 'value',
        'option_value.translations.*.lang' => 'language code',
        'timesheet_entries.*.timesheet_id' => 'timesheet id',
        ],

];
