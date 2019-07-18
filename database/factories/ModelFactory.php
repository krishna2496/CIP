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
        'timezone_id' => rand(1, 1),
        'language_id' => rand(1, 1),
        'availability_id' => rand(1, 1),
        'why_i_volunteer' => str_random(10),
        'employee_id' => str_random(10),
        'department' => str_random(10),
        'manager_name' => str_random(10),
        'city_id' => rand(1, 1),
        'country_id' => rand(1, 1),
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
            'values' => "[".rand(1, 5).",".rand(5, 10)."]"
        ]
    ];
});

$factory->define(App\Models\TenantOption::class, function (Faker\Generator $faker) {
    return [
        'option_name' => 'slider'
    ];
});

$factory->define(App\Models\Mission::class, function (Faker\Generator $faker) {
    return [
        "theme_id" => rand(1, 1),
        "city_id" => rand(1, 1),
        "country_id" => rand(1, 1),
        "start_date" => "2019-05-15 10:40:00",
        "end_date" => "2019-10-15 10:40:00",
        "total_seats" => rand(1, 1000),        
        "mission_type" => "GOAL",
        "publication_status" => "DRAFT",
        "organisation_id" => rand(1, 1),
        "organisation_name" => str_random(10),
    ];
});
