<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\TenantOption;
use App\Repositories\TenantOption\TenantOptionRepository;

class TenantOptionsTest extends TestCase
{
    /**
     * @test
     *
     * Create footer page api
     *
     * @return void
     */
    public function it_should_create_slider()
    {
        $params = [
            'url' => "http://new.anasource.com/team11/s3/sliderimg4.jpg",
            'sort_order' => "1",
            'slider_detail' =>
                [
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'slider_title' => str_random(20),
                        'slider_description' => str_random(200)
                    ]
                ],
            ],
        ];

        $connection = 'tenant';
        $tenant = factory(\App\Models\TenantOption::class)->make();
        $tenant->setConnection($connection);
        $count = $tenant->where('option_name', config('constants.TENANT_OPTION_SLIDER'))->count();

        if ($count >= config('constants.SLIDER_LIMIT')) {
            $this->post("create_slider/", $params, ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')])
            ->seeStatusCode(403)
            ->seeJsonStructure([
                'errors' => [
                        [
                            'status',
                            'type',
                            'code',
                            'message'
                        ]
                    ]
                ]);
        } else {
            $this->post("create_slider/", $params, ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'status',
                'message',
                ]);
        }
        TenantOption::where("option_name", "slider")->orderBy("tenant_option_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Update style
     *
     * @return void
     */
    /*
    public function it_should_update_style()
    {
        // $this->post('style/update-style', ['Authorization' => 'Basic dGF0dmFfYXBpX2tleTp0YXR2YV9hcGlfc2VjcmV0'])
        //  ->type('#999', 'primary_color')
        //  ->type('#ccc', 'secondary_color');

        $values = [
            'primary_color' => '#999',
            'secondary_color' => '#ccc'
        ];
        $uploadedFile = '';
        $response = $this->call(
            'POST',
            'style/update-style',
            ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')],
            $values,
            [],
            ['file' => $uploadedFile]
        );
        $response->assertStatus(200);
    }
    */

    /**
     * @test
     *
     * Reset style to default
     *
     * @return void
     */
    public function it_should_reset_style_to_default()
    {
        $this->get('style/reset-style',  ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')])
        ->seeStatusCode(200);
    }

}
