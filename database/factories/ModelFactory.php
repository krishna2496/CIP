<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstname,
        'last_name' => $faker->lastname,
        'email' => $faker->unique()->email,
        'password' => str_random(10),
        'timezone_id' => 1,
        'language_id' => 1,
        'availability_id' => 1,
        'why_i_volunteer' => str_random(10),
        'employee_id' => str_random(10),
        'department' => str_random(10),
        'manager_name' => str_random(10),
        'city_id' => 1,
        'country_id' => 233,
        'profile_text' => str_random(10),
        'linked_in_url' => 'https://www.'.str_random(10).'.com'
    ];
});

$factory->define(App\Models\FooterPage::class, function (Faker\Generator $faker) {
    return [
        'slug' => str_random(20)
    ];
});


$factory->define(App\Models\UserCustomField::class, function (Faker\Generator $faker) {
    $typeArray = config('constants.custom_field_types');
    $randomTypes = array_rand($typeArray,1);
    return [
        'name' => str_random(10),
        'type' => $typeArray[$randomTypes],
        'is_mandatory' => 1,
        'translations' => [
            'lang' => "en",
            'name' => str_random(10),
            'values' => "[".'1:'.rand(1, 5).",".'2:'.rand(5, 10)."]"
        ]
    ];
});

$factory->define(App\Models\Slider::class, function (Faker\Generator $faker) {
    return [
        'url' => 'https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png',
        'sort_order' => '1',
        'translations' =>  [
            [
                'lang' => 'en',
                'slider_title' => str_random(20),
                'slider_description' => str_random(200)
            ]
        ],
    ];
});

$factory->define(App\Models\Mission::class, function (Faker\Generator $faker) {
    return [
        "theme_id" => 1,
        "city_id" => 1,
        "country_id" => 233,
        "start_date" => "2019-05-15 10:40:00",
        "end_date" => "2019-10-15 10:40:00",
        "total_seats" => rand(1, 1000),        
        "mission_type" => config("constants.mission_type.GOAL"),
        "publication_status" => config("constants.publication_status.APPROVED"),
        "organisation_id" => 1,
        "organisation_name" => str_random(10),
        "availability_id" => 1
    ];
});

$factory->define(App\Models\Skill::class, function (Faker\Generator $faker) {
    return [
        'skill_name' => str_random(10),
        'translations' => [
            [
                'lang' => "en",
                'title' => str_random(10)
            ]            
        ],
        'parent_skill' => 0,
    ];
});

$factory->define(App\Models\MissionApplication::class, function (Faker\Generator $faker) {
    return [
        'mission_id' => 1,
        'user_id' => 1,
        'availability_id' => 1,
        'applied_at' => date("Y-m-d H:i:s"),
        'approval_status' => 'AUTOMATICALLY_APPROVED',
        'motivation' => str_random(10)
    ];
});

$factory->define(App\Models\PolicyPage::class, function (Faker\Generator $faker) {
    return [
        'slug' => str_random(20)
    ];
});

$factory->define(App\Models\TenantSetting::class, function (Faker\Generator $faker) {
    return [
        'setting_id' => 2
    ];
});

$factory->define(App\Models\TenantActivatedSetting::class, function (Faker\Generator $faker) {
    return [
        'tenant_setting_id' => 114
    ];
});

$factory->define(App\Models\TimesheetDocument::class, function (Faker\Generator $faker) {
    return [
        'document_name' => 'volunteer9.png',
        'document_path' => 'https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png',
        'document_type' => 'png'
    ];
});

$factory->define(App\Models\UserSkill::class, function (Faker\Generator $faker) {
    return [
        'user_id' => 1,
        'skill_id' => 1
    ];
});

$factory->define(App\Models\City::class, function (Faker\Generator $faker) {
    return [
        'country_id' => 1,
        'name' => 'test'
    ];
});