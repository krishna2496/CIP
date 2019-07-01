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
        'timezone_id' => rand(1,1),
        'language_id' => rand(1,1),
        'availability_id' => rand(1,50),
        'why_i_volunteer' => str_random(10),
        'employee_id' => str_random(10),
        'department' => str_random(10),
        'manager_name' => str_random(10),
        'city_id' => rand(1,1),
        'country_id' => rand(1,1),
        'profile_text' => str_random(10),
        'linked_in_url' => 'https://www.'.str_random(10).'.com'   
    ];
});

$factory->define(App\Models\FooterPage::class, function (Faker\Generator $faker) {
    return [
        'slug' => str_random(20)
    ];
});


$factory->define(App\Models\FooterPage::class, function (Faker\Generator $faker) {
    return [ 
        'slug' => str_random(10),
        'type' => random('radio', 'drop-down'),
        'is_mandatory' => 1,
        'translations' => [
            'lang' => "en",
            'name' => str_random(10),
            'values' => "['10-15','16-20','21-30']"
        ]
    ];
});


{  
    "name":"Age",
    "type":"radio",
    "is_mandatory":"1",
    "translations":[  
          {  
             "lang":"en",
             "name":"Age",
             "values":"['10-15','16-20','21-30']"
          },
          {  
             "lang":"de",
             "name":"Alter",
             "values":"['10-15','16-20','21-30']"
          }
       ]
 }
