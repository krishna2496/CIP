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
	'distinct'             => 'The :attribute field has a duplicate value.',

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
        'date_volunteered' => [
            'before' => 'You cannot add time entry for future dates',
        ],
        'news_image' => [
            'valid_media_path' => 'Please enter valid media image',
        ],
        'user_thumbnail' => [
            'valid_media_path' => 'Please enter valid media image',
        ],
        'story_images.*' => [
            'valid_story_image_type' => 'Please select valid image type',
            'max' => 'Image size must be ' .
            (config('constants.STORY_IMAGE_SIZE_LIMIT') / 1024) . 'mb or below',
        ],
        'story_videos' => [
            'valid_story_video_url' => 'Please enter valid video url',
            'max_video_url' => 'Maximum '.config('constants.STORY_MAX_VIDEO_LIMIT').' video url can be added',
        ],
        'story_images' => [
            'max' => 'Maximum '.config('constants.STORY_MAX_IMAGE_LIMIT').' images can be added',
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
		'mission_detail.*.short_description' => 'short description',
        'news_content.translations' => 'translations',
        'news_content.translations.*.lang' => 'language code',
        'news_content.translations.*.title' => 'title',
        'news_content.translations.*.description' => 'description',
        'translations.*.title' => 'title',
        'settings.*.notification_type_id' => 'notification type id',
        'user_ids.*' => 'user id',
        'mission_detail.*.custom_information' => 'custom information',
        'mission_detail.*.custom_information.*.title' => 'title',
        'mission_detail.*.custom_information.*.description' => 'description',
        'mission_detail.*.title' => 'title',
        'organisation.organisation_name' => 'organisation name',
        'cities.*.translations.*.lang' => 'language code',
        'cities.*.translations.*.name' => 'name',
        'cities.*.translations' => 'translations',
        'media_images.*.sort_order' => 'sort order',
        'media_videos.*.sort_order' => 'sort order',
        'documents.*.sort_order' => 'sort order', 
        'countries.*.translations.*.lang' => 'language code',
        'countries.*.translations.*.name' => 'name',
        'countries.*.translations' => 'translations',   
        'countries.*.iso' => 'ISO',
        'translations.*.lang' => 'language code',
        'translations.*.name' => 'name',
        'translations' => 'translations',
        'mission_detail.*.section' => 'section',
        'mission_detail.*.section.*.title' => 'title',
        'mission_detail.*.section.*.description' => 'description',
		],

];
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

    'accepted'             => "Das :attribute muss akzeptiert werden.",
    'active_url'           => ":attribute ist keine gültige URL.",
    'after'                => ":attribute muss ein Datum nach :datum sein.",
    'alpha'                => ":attribute darf nur Buchstaben enthalten.",
    'alpha_dash'           => ":attribute darf nur Buchstaben, Zahlen und Bindestriche enthalten.",
    'alpha_num'            => ":attribute darf nur Buchstaben und Zahlen enthalten.",
    'array'                => ":attribute muss ein Array sein.",
    'before'               => ":attribute muss ein Datum vor :date sein.",
    'between'              => [
        'numeric' => ":attribute muss zwischen :min und :max liegen.",
        'file'    => ":attribute muss zwischen :min und :max Kilobyte groß sein.",
        'string'  => ":attribute muss zwischen :min und :max Zeichen haben.",
        'array'   => ":attribute muss über zwischen :min und :max Positionen verfügen.",
    ],
    'boolean'              => "Das :attribute-Feld muss wahr oder falsch sein.",
    'confirmed'            => "Die :attribute-Bestätigung stimmt nicht überein.",
    'date'                 => ":attribute ist kein gültiges Datum.",
    'date_format'          => ":attribute stimmt nicht mit dem Format :format überein.",
    'different'            => ":attribute und :other müssen unterschiedlich sein.",
    'digits'               => ":attribute muss über :digits Stellen verfügen.",
    'digits_between'       => ":attribute muss zwischen :min und :max Stellen liegen.",
    'email'                => ":attribute muss eine gültige E-Mail-Adresse sein.",
    'filled'               => "Das :attribute-Feld ist erforderlich.",
    'exists'               => "Das ausgewählte :attribute ist ungültig.",
    'image'                => ":attribute muss ein Bild sein.",
    'in'                   => "Das ausgewählte :attribute ist ungültig.",
    'integer'              => ":attribute muss eine ganze Zahl sein.",
    'ip'                   => ":attribute muss eine gültige IP-Adresse sein.",
    'max'                  => [
        'numeric' => ":attribute darf :max nicht überschreiten.",
        'file'    => ":attribute darf nicht größer als :max Kilobyte sein.",
        'string'  => ":attribute darf nicht mehr als :max Zeichen haben.",
        'array'   => ":attribute darf über nicht mehr als :max Positionen verfügen.",
    ],
    'mimes'                => ":attribute muss eine Datei vom Typ :values sein.",
    'min'                  => [
        'numeric' => ":attribute muss mindestens :min sein.",
        'file'    => ":attribute muss mindestens :min Kilobyte groß sein.",
        'string'  => ":attribute muss mindestens :min Zeichen lang sein.",
        'array'   => ":attribute muss über mindestens :min Positionen verfügen.",
    ],
    'not_in'               => "Das ausgewählte :attribute ist ungültig.",
    'numeric'              => ":attribute muss eine Zahl sein.",
    'regex'                => ":attribute-Format ist ungültig.",
    'required'             => "Das :attribute-Feld ist erforderlich.",
    'required_if'          => "Das :attribute-Feld ist erforderlich, wenn :other :value ist.",
    'required_with'        => "Das :attribute-Feld ist erforderlich, wenn :values vorhanden ist.",
    'required_with_all'    => "Das :attribute-Feld ist erforderlich, wenn :values vorhanden ist.",
    'required_without'     => "Das :attribute-Feld ist erforderlich, wenn :values nicht vorhanden ist.",
    'required_without_all' => "Das :attribute-Feld ist erforderlich, wenn keine der :values vorhanden sind.",
    'same'                 => ":attribute und :other muss übereinstimmen.",
    'size'                 => [
        'numeric' => ":attribute muss :size groß sein.",
        'file'    => ":attribute muss :size Kilobyte groß sein.",
        'string'  => ":attribute muss :size Zeichen lang sein.",
        'array'   => ":attribute muss :size Positionen enthalten.",
    ],
    'timezone'             => ":attribute muss ein gültiger Bereich sein.",
    'unique'               => ":attribute ist bereits belegt.",
    'url'                  => ":attribute-Format ist ungültig.",
    'present'              => "Das :attribute-Feld ist erforderlich",
	'distinct'             => "Das :attribute-Feld verfügt über einen doppelten Wert.",

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
            'valid_media_path' => "Bitte geben Sie ein gültiges Medienbild ein.",
        ],
        'documents.*.document_path' => [
            'valid_document_path' => "Bitte geben Sie eine gültige Dokumentendatei ein.",
        ],
		'media_videos.*.media_path' => [
            'valid_video_url' => "Bitte gültige YouTube-URL eingeben",
        ],
		'avatar' => [
            'valid_profile_image' => "Ungültige Bilddateien oder Bildtypen sind nicht gestattet. Zulässige Typen: png, jpeg, jpg",
        ],
		'parent_skill' => [
            'valid_parent_skill' => "Ungültige Parent-Fähigkeit",
        ],
        'url' => [
            'valid_media_path' => "Bitte geben Sie eine gültige Bild-URL ein",
        ],
        'linked_in_url' => [
            'valid_linkedin_url' => "Bitte geben Sie eine gültige LinkedIn-URL ein",
        ],
        'documents.*' => [
            'valid_timesheet_document_type' => "Bitte wählen Sie ein gültiges Zeitplan-Dokument aus.",
            'max' => "Das Dokument muss folgende Größe haben "
            (config('constants.TIMESHEET_DOCUMENT_SIZE_LIMIT') / 1024) . 'mb or below',
        ],
        'date_volunteered' => [
            'before' => "Sie können keine Zeiteinträge in der Zukunft machen",
        ],
        'news_image' => [
            'valid_media_path' => "Bitte geben Sie ein gültiges Medienbild ein.",
        ],
        'user_thumbnail' => [
            'valid_media_path' => "Bitte geben Sie ein gültiges Medienbild ein.",
        ],
        'story_images.*' => [
            'valid_story_image_type' => "Bitte wählen Sie einen gültigen Bildtyp aus",
            'max' => "Das Bild muss folgende Größe haben "
            (config('constants.STORY_IMAGE_SIZE_LIMIT') / 1024) . 'mb or below',
        ],
        'story_videos' => [
            'valid_story_video_url' => "Bitte geben Sie eine gültige Video-URL ein",
            'max_video_url' => "Maximal ".config('constants.STORY_MAX_VIDEO_LIMIT').' video url can be added',
        ],
        'story_images' => [
            'max' => "Maximal ".config('constants.STORY_MAX_IMAGE_LIMIT').' images can be added',
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
        'page_details.slug' => "slug",
        'page_details.translations' => "Übersetzungen",
        'page_details.translations.*.lang' => "Sprachcode",
        'page_details.translations.*.title' => "Titel",
        'page_details.translations.*.sections' => "Abschnitte",
        'translations.*.values' => "Werte",
        'media_images.*.media_name' => "Medienbezeichnung",
        'media_images.*.media_type' => "Medientyp",
        'media_images.*.media_path' => "Medienpfad",
        'media_videos.*.media_name' => "Medienbezeichnung",
        'media_videos.*.media_type' => "Medientyp",
        'media_videos.*.media_path' => "Medienpfad",
        'documents.*.document_name' => "Dokumentenbezeichnung",
        'documents.*.document_type' => "Dokumententyp",
        'documents.*.document_path' => "Dokumentenpfad",        
        'slider_detail.translations.*.lang' => "Sprachcode",
        'skills.*.skill_id' => "Fähigkeiten-ID",  
        'location.city' => "Stadt", 
        'location.country' => "Land",   
        'password_confirmation' => "Passwort bestätigen",         
        'translations.*.lang' => "Sprachcode",         
        'is_mandatory' => "vorgeschrieben",       
		'page_details.translations.*.sections.*.title' => "Titel",
		'page_details.translations.*.sections.*.description' => "Beschreibung",
		'location.city_id' => "Stadt",
		'location.country_code' => "Ländercode",
		'organisation.organisation_id' => "Organisations-ID",
		'mission_detail.*.lang' => "Sprachcode",
        'to_user_id' => "Benutzer-ID",
        'custom_fields.*.field_id' => "Feld-ID",
        'settings.*.tenant_setting_id' => "Mieter-Einstellungs-ID",
        'settings.*.value' => "Wert",
        'option_value.translations.*.lang' => "Sprachcode",
        'timesheet_entries.*.timesheet_id' => "Zeitplan-ID",
		'mission_detail.*.short_description' => "Kurzbeschreibung",
        'news_content.translations' => "Übersetzungen",
        'news_content.translations.*.lang' => "Sprachcode",
        'news_content.translations.*.title' => "Titel",
        'news_content.translations.*.description' => "Beschreibung",
        'translations.*.title' => "Titel",
        'settings.*.notification_type_id' => "Typ-ID der Benachrichtigung",
        'user_ids.*' => "Benutzer-ID",
        'mission_detail.*.custom_information' => "Allgemeine Information",
        'mission_detail.*.custom_information.*.title' => "Titel",
        'mission_detail.*.custom_information.*.description' => "Beschreibung",
        'mission_detail.*.title' => "Titel",
        'organisation.organisation_name' => "Name der Organisation",
        'cities.*.translations.*.lang' => "Sprachcode",
        'cities.*.translations.*.name' => "Name",
        'cities.*.translations' => "Übersetzungen",
        'media_images.*.sort_order' => "Sortierreihenfolge",
        'media_videos.*.sort_order' => "Sortierreihenfolge",
        'documents.*.sort_order' => "Sortierreihenfolge", 
        'countries.*.translations.*.lang' => "Sprachcode",
        'countries.*.translations.*.name' => "Name",
        'countries.*.translations' => "Übersetzungen",   
        'countries.*.iso' => "ISO",
        'translations.*.lang' => "Sprachcode",
        'translations.*.name' => "Name",
        'translations' => "Übersetzungen",
        'mission_detail.*.section' => "Abschnitt",
        'mission_detail.*.section.*.title' => "Titel",
        'mission_detail.*.section.*.description' => "Beschreibung",
		],

];
?>