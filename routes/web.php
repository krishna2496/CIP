<?php
/*
|--------------------------------------------------------------------------
| Authentication routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->group(['middleware' => 'localization'], function ($router) {
    
    /* Get api to fetch user default language, from it's mail. */
    $router->get('/app/get-user-language', ['as' => 'connect', 'middleware' => 'tenant.connection',
     'uses' => 'App\User\UserController@getUserDefaultLanguage']);

    /* Connect first time to get styling data. */
    $router->get('/app/connect', ['as' => 'connect', 'middleware' => 'tenant.connection',
     'uses' => 'App\Tenant\TenantOptionController@getTenantOption']);

    /* User login routing using jwt token */
    $router->post('/app/login', ['as' =>'login', 'middleware' => 'tenant.connection',
     'uses' => 'App\Auth\AuthController@authenticate']);

    /* Forgot password routing */
    $router->post('/app/request-password-reset', ['middleware' => 'tenant.connection|JsonApiMiddleware',
     'uses' => 'App\Auth\AuthController@requestPasswordReset']);

    /* Password reset routing */
    $router->post('/reset-password/{token}', ['as' => 'password.reset',
     'uses' => 'App\Auth\AuthController@reset_password']);

    /* reset password  */
    $router->put('/app/password-reset', ['middleware' => 'localization|tenant.connection',
     'uses' => 'App\Auth\AuthController@passwordReset']);

    /* CMS footer pages  */
    $router->get('/app/cms/listing', ['as' => 'cms.listing', 'middleware' => 'localization|tenant.connection',
     'uses' => 'App\FooterPage\FooterPageController@index']);
    $router->get('/app/cms/detail', ['as' => 'cms.detail', 'middleware' => 'localization|tenant.connection',
     'uses' => 'App\FooterPage\FooterPageController@cmsList']);
    $router->get('/app/cms/{slug}', ['as' => 'cms.show', 'middleware' => 'localization|tenant.connection',
     'uses' => 'App\FooterPage\FooterPageController@show']);
    
    /* Get custom css url  */
    $router->get('/app/custom-css', ['as' => 'custom_css', 'middleware' => 'tenant.connection',
     'uses' => 'App\Tenant\TenantOptionController@getCustomCss']);
    
    /* Get mission listing  */
    $router->get('/app/missions/', ['as' => 'app.missions',
    'middleware' => 'localization|tenant.connection|jwt.auth|PaginationMiddleware',
    'uses' => 'App\Mission\MissionController@getMissionList']);

    /* Get user filter  */
    $router->get('/app/user-filter', ['middleware' => 'tenant.connection|jwt.auth',
     'uses' => 'App\UserFilterController@index']);

    /* Get explore mission  */
    $router->get('/app/explore-mission', ['middleware' => 'tenant.connection|jwt.auth',
    'uses' => 'App\Mission\MissionController@exploreMission']);

    /* Get user filter  */
    $router->get('/app/filter-data', ['middleware' => 'tenant.connection|jwt.auth',
    'uses' => 'App\Mission\MissionController@filters']);

    /* Add/remove favourite */
    $router->post('/app/mission/favourite', [
        'middleware' => 'localization|tenant.connection|jwt.auth|JsonApiMiddleware',
        'uses' => 'App\Mission\MissionController@missionFavourite']);

    /* Mission Invite  */
    $router->post('/app/mission/invite', ['as' => 'app.missions.invite',
    'middleware' => 'localization|tenant.connection|jwt.auth',
    'uses' => 'App\Mission\MissionInviteController@missionInvite']);

    /* Fetch tenant option */
    $router->post('/app/tenant-option', ['as' =>'app.tenant-option',
    'middleware' => 'tenant.connection|jwt.auth|JsonApiMiddleware',
    'uses' => 'App\Tenant\TenantOptionController@fetchTenantOptionValue']);

    /* Fetch tenant settings */
    $router->get('/app/tenant-settings', ['as' =>'tenant-settings',
    'middleware' => 'tenant.connection',
    'uses' => 'App\Tenant\TenantSettingsController@index']);

    /* Apply to a mission */
    $router->post(
        'app/mission/application',
        ['middleware' => 'tenant.connection|jwt.auth|JsonApiMiddleware',
        'uses' => 'App\Mission\MissionApplicationController@missionApplication']
    );

    /* Store mission ratings */
    $router->post(
        'app/mission/rating',
        ['middleware' => 'tenant.connection|jwt.auth|JsonApiMiddleware',
        'uses' => 'App\Mission\MissionRatingController@store']
    );
    
    /* Fetch user */
    $router->get('/app/user', ['as' =>'app.user',
    'middleware' => 'tenant.connection|jwt.auth|PaginationMiddleware',
    'uses' => 'App\User\UserController@index']);

    /* Fetch search-user */
    $router->get('/app/search-user', ['as' =>'app.user',
    'middleware' => 'tenant.connection|jwt.auth|PaginationMiddleware',
    'uses' => 'App\User\UserController@index']);

    /* Get mission detail  */
    $router->get('/app/mission/{missionId}', [
    'middleware' => 'localization|tenant.connection|jwt.auth',
    'uses' => 'App\Mission\MissionController@getMissionDetail']);
    
    /* Fetch recent volunteers */
    $router->get('/app/mission/{missionId}/volunteers', [
    'middleware' => 'tenant.connection|jwt.auth|PaginationMiddleware',
    'uses' => 'App\Mission\MissionApplicationController@getVolunteers']);
     
    /* Get mission related listing  */
    $router->get('/app/related-missions/{missionId}', ['as' => 'app.related-missions',
    'middleware' => 'localization|tenant.connection|jwt.auth',
    'uses' => 'App\Mission\MissionController@getRelatedMissions']);
   
    /* Get mission media listing  */
    $router->get('/app/mission-media/{missionId}', ['as' => 'app.mission-media',
    'middleware' => 'localization|tenant.connection|jwt.auth',
    'uses' => 'App\Mission\MissionMediaController@getMissionMedia']);

    /* Get mission comments  */
    $router->get('/app/mission/{missionId}/comments', [
        'middleware' => 'localization|tenant.connection|jwt.auth',
        'uses' => 'App\Mission\MissionCommentController@getComments']);

    /* Store mission comment */
    $router->post('/app/mission/comment', [
        'middleware' => 'localization|tenant.connection|jwt.auth',
        'uses' => 'App\Mission\MissionCommentController@store']);

    /* Get api to fetch user details for edit. */
    $router->get('/app/user-detail/{userId}', ['as' => 'connect', 'middleware' => 'tenant.connection|jwt.auth',
     'uses' => 'App\User\UserController@show']);
});


/* Fetch Language json file */
$router->get('language/{lang}', ['as' => 'language', 'uses' => 'App\Language\LanguageController@fetchLangaugeFile']);

/*
|
|--------------------------------------------------------------------------
| Tenant Admin Routs
|--------------------------------------------------------------------------
|
| These are tenant admin routes to manage tenant users, settings, and etc.
|
*/

/* Set user data for tenant specific */
$router->group(
    ['prefix' => 'users', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
    function ($router) {
        $router->get('/', ['as' => 'users', 'middleware' => ['PaginationMiddleware'],
        'uses' => 'Admin\User\UserController@index']);
        $router->get('/{userId}', ['as' => 'users.show', 'uses' => 'Admin\User\UserController@show']);
        $router->post('/', ['as' => 'users.store', 'uses' => 'Admin\User\UserController@store']);
        $router->patch('/{userId}', ['as' => 'users.update', 'uses' => 'Admin\User\UserController@update']);
        $router->delete('/{userId}', ['as' => 'usersdelete', 'uses' => 'Admin\User\UserController@destroy']);
    }
);

/* Set custom slider data for tenant specific */
$router->post('/create_slider', ['as' => 'create_slider', 'middleware' => 'localization|auth.tenant.admin',
 'uses' => 'Admin\Tenant\TenantOptionsController@storeSlider']);

/* Set Footer Page data for tenant specific */
$router->group(
    ['prefix' => 'cms', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
    function ($router) {
        $router->get('/', ['as' => 'cms', 'middleware' => ['PaginationMiddleware'],
        'uses' => 'Admin\FooterPage\FooterPageController@index']);
        $router->get('/{pageId}', ['as' => 'cms.show', 'uses' => 'Admin\FooterPage\FooterPageController@show']);
        $router->post('/', ['as' => 'cms.store', 'uses' => 'Admin\FooterPage\FooterPageController@store']);
        $router->patch('/{pageId}', ['as' => 'cms.update', 'uses' => 'Admin\FooterPage\FooterPageController@update']);
        $router->delete('/{pageId}', ['as' => 'cms.delete', 'uses' => 'Admin\FooterPage\FooterPageController@destroy']);
    }
);

/* Set custom field data for tenant specific */
$router->group(
    ['prefix' => 'metadata/users/custom_fields', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
    function ($router) {
        $router->get('/', ['as' => 'metadata.users.custom_fields',
        'middleware' => ['PaginationMiddleware'] ,'uses' => 'Admin\User\UserCustomFieldController@index']);
        $router->get('/{fieldId}', ['as' => 'metadata.users.custom_fields.show',
        'uses' => 'Admin\User\UserCustomFieldController@show']);
        $router->post('/', ['as' => 'metadata.users.custom_fields.store',
        'uses' => 'Admin\User\UserCustomFieldController@store']);
        $router->patch('/{fieldId}', ['as' => 'metadata.users.custom_fields.update',
        'uses' => 'Admin\User\UserCustomFieldController@update']);
        $router->delete('/{fieldId}', ['as' => 'metadata.users.custom_fields.delete',
        'uses' => 'Admin\User\UserCustomFieldController@destroy']);
    }
);

/* Set mission data for tenant specific */
$router->group(
    ['prefix' => 'missions', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
    function ($router) {
        $router->get('', ['as' => 'missions', 'middleware' => ['PaginationMiddleware'],
        'uses' => 'Admin\Mission\MissionController@index']);
        $router->get('/{missionId}', ['as' => 'missions.show', 'uses' => 'Admin\Mission\MissionController@show']);
        $router->post('/', ['as' => 'missions.store', 'uses' => 'Admin\Mission\MissionController@store']);
        $router->patch('/{missionId}', ['as' => 'missions.update', 'uses' => 'Admin\Mission\MissionController@update']);
        $router->delete('/{missionId}', ['as' => 'missions.delete',
        'uses' => 'Admin\Mission\MissionController@destroy']);
        $router->get('/{missionId}/applications', ['middleware' => ['PaginationMiddleware'],
         'uses' => 'Admin\Mission\MissionApplicationController@missionApplications']);
        $router->get(
            '/{missionId}/applications/{applicationId}',
            ['uses' => 'Admin\Mission\MissionApplicationController@missionApplication']
        );
        $router->patch(
            '/{missionId}/applications/{applicationId}',
            ['uses' => 'Admin\Mission\MissionApplicationController@updateApplication']
        );
    }
);

/* Set skill data for tenant user specific */
$router->group(
    ['prefix' => 'user/skills', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
    function ($router) {
        $router->get('/{userId}', ['uses' => 'Admin\User\UserController@userSkills']);
        $router->post('/{userId}', ['uses' => 'Admin\User\UserController@linkSkill']);
        $router->delete('/{userId}', ['uses' => 'Admin\User\UserController@unlinkSkill']);
    }
);

/*Admin style routes*/
$router->group(
    ['prefix' => 'style', 'middleware' => 'localization|auth.tenant.admin'],
    function ($router) {
        $router->post('/update-style', ['uses' => 'Admin\Tenant\TenantOptionsController@updateStyleSettings']);
        $router->get('/reset-style', ['uses' => 'Admin\Tenant\TenantOptionsController@resetStyleSettings']);
        $router->get('/download-style', ['uses' => 'Admin\Tenant\TenantOptionsController@downloadStyleFiles']);
        $router->post('/update-image', ['uses' => 'Admin\Tenant\TenantOptionsController@updateImage']);
        $router->get('/reset-asset-images', ['uses' => 'Admin\Tenant\TenantOptionsController@resetAssetsImages']);
    }
);

/* Admin setting routes */
$router->group(
    ['prefix' => 'settings', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
    function ($router) {
        $router->get('/', ['uses' => 'Admin\Tenant\TenantSettingsController@index']);
        $router->patch('/{settingId}', ['uses' => 'Admin\Tenant\TenantSettingsController@update']);
    }
);

/* Set mission theme data for tenant specific */
$router->group(
    ['prefix' => '/entities/themes', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
    function ($router) {
        $router->get('/', ['middleware' => ['PaginationMiddleware'],
        'uses' => 'Admin\MissionTheme\MissionThemeController@index']);
        $router->get('/{themeId}', ['uses' => 'Admin\MissionTheme\MissionThemeController@show']);
        $router->post('/', ['uses' => 'Admin\MissionTheme\MissionThemeController@store']);
        $router->patch('/{themeId}', ['uses' => 'Admin\MissionTheme\MissionThemeController@update']);
        $router->delete('/{themeId}', ['uses' => 'Admin\MissionTheme\MissionThemeController@destroy']);
    }
);

$router->group(
    ['prefix' => 'tenant-option', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
    function ($router) {
        $router->post('/', ['uses' => 'Admin\Tenant\TenantOptionsController@storeTenantOption']);
        $router->patch('/', ['uses' => 'Admin\Tenant\TenantOptionsController@updateTenantOption']);
    }
);

/* Set skills data for tenant specific */
$router->group(
    ['prefix' => '/entities/skills', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
    function ($router) {
        $router->get('/', ['middleware' => ['PaginationMiddleware'], 'uses' => 'Admin\Skill\SkillController@index']);
        $router->get('/{skillId}', ['uses' => 'Admin\Skill\SkillController@show']);
        $router->post('/', ['uses' => 'Admin\Skill\SkillController@store']);
        $router->patch('/{skillId}', ['uses' => 'Admin\Skill\SkillController@update']);
        $router->delete('/{skillId}', ['uses' => 'Admin\Skill\SkillController@destroy']);
    }
);
$router->get('/social-sharing/{fqdn}/{missionId}/{langId}', ['as' => 'social-sharing',
'uses' => 'App\Mission\MissionSocialSharingController@setMetaData']);

/* Set policy page data for tenant specific */
$router->group(
    ['prefix' => 'policy', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
    function ($router) {
        $router->get('/', ['as' => 'policy', 'middleware' => ['PaginationMiddleware'],
        'uses' => 'Admin\PolicyPage\PolicyPageController@index']);
        $router->get('/{pageId}', ['as' => 'policy.show', 'uses' => 'Admin\PolicyPage\PolicyPageController@show']);
        $router->post('/', ['as' => 'policy.store', 'uses' => 'Admin\PolicyPage\PolicyPageController@store']);
        $router->patch('/{pageId}', ['as' => 'policy.update',
        'uses' => 'Admin\PolicyPage\PolicyPageController@update']);
        $router->delete('/{pageId}', ['as' => 'policy.delete',
        'uses' => 'Admin\PolicyPage\PolicyPageController@destroy']);
    }
);

/*
|
|--------------------------------------------------------------------------
| Tenant User Routs
|--------------------------------------------------------------------------
|
| These are tenant user routes to manage their profile and other stuff
|
*/
/*$router->group(['middleware' => 'tenant.connection|jwt.auth'], function() use ($router) {
    $router->get('users', function() {
        $users = \App\User::all();
        return response()->json($users);
    });
});*/
