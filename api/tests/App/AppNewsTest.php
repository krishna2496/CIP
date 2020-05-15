<?php
use App\Helpers\Helpers;
use App\Models\News;
use App\Models\NewsCategory;

class AppNewsTest extends TestCase
{
    /**
     * @test
     *
     * It should return all news
     * @return void
     */
    public function app_news_it_should_list_news()
    {
        $connection = 'tenant';
        $newsIdsArray = [];

        for ($i=0; $i<5; $i++) {

            $news = factory(\App\Models\News::class)->make();
            $news->setConnection($connection);
            $news->save();

            $newsCategory = factory(\App\Models\NewsCategory::class)->make();
            $newsCategory->setConnection($connection);
            $newsCategory->save();
            
            $newsToCategory = factory(\App\Models\NewsToCategory::class)->make();
            $newsToCategory->setConnection($connection);
            $newsToCategory->news_id = $news->news_id;
            $newsToCategory->news_category_id = $newsCategory->news_category_id;
            $newsToCategory->save();

            $newsLanguage = factory(\App\Models\NewsLanguage::class)->make();
            $newsLanguage->setConnection($connection);
            $newsLanguage->news_id = $news->news_id;            
            $newsLanguage->save();
            
            array_push($newsIdsArray, $news->news_id); 
        } 

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        \DB::setDefaultConnection('mysql');
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));        
        $response = $this->get('app/news', ['token' => $token, 'X-localization' => 'test'])->seeStatusCode(200);

        \DB::setDefaultConnection('mysql');
        $response = $this->get('app/news?order=test', ['token' => $token])->seeStatusCode(400);
        
        News::whereIn('news_id', $newsIdsArray)->delete();
        $newsCategory->delete();
    }

    /**
     * @test
     *
     * It should news details
     * @return void
     */
    public function app_news_it_should_return_news_details()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $newsCategory = factory(\App\Models\NewsCategory::class)->make();
        $newsCategory->setConnection($connection);
        $newsCategory->save();
        
        DB::setDefaultConnection('tenant');
        $params = [
            "news_image" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "user_name" => str_random('5'),
            "user_title" => strtoupper(str_random('3')),
            "user_thumbnail" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "news_category_id" => $newsCategory->news_category_id,
            "status" => "PUBLISHED",
            "news_content" => [
                "translations" => [
                    [  
                        "lang" => "en",
                        "title" => "english_".str_random('10'),
                        "description" => "We can collect the following information: name and job title, contact information, including email address, demographic information such as zip code, preferences and interests, other relevant information for surveys and / or customer offers"
                    ],
                    [
                        "lang" => "fr",
                        "title" => "french_".str_random('10'),
                        "description" => "lNous pouvons collecter les informations suivantes: nom et intitulé du poste, informations de contact, y compris adresse électronique, informations démographiques telles que le code postal, préférences et intérêts, autres informations pertinentes pour les enquêtes et / ou les offres clients"
                    ]
                ]
            ]
        ];
        DB::setDefaultConnection('mysql');
        $response = $this->post('news', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        $newsId = json_decode($response->response->getContent())->data->news_id;

        DB::setDefaultConnection('mysql');
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

        $response = $this->get('app/news/'.$newsId, ['token' => $token])
        ->seeStatusCode(200);
        $newsCategory->delete();
    }

    /**
     * @test
     *
     * It should return news not found on news details
     * @return void
     */
    public function app_news_it_should_return_error_news_not_found_on_news_details()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $newsId = rand(50000000, 500000000);
        DB::setDefaultConnection('mysql');
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

        $response = $this->get('app/news/'.$newsId, ['token' => $token])
        ->seeStatusCode(404);
    }
}
