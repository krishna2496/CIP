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
    $router->post('/app/login', ['as' => 'login', 'middleware' => 'tenant.connection',
        'uses' => 'App\Auth\AuthController@authenticate']);

    /* Forgot password routing */
    $router->post('/app/request-password-reset', ['middleware' => 'tenant.connection|JsonApiMiddleware',
        'uses' => 'App\Auth\AuthController@requestPasswordReset']);

    /* Password reset routing */
    $router->post('/reset-password/{token}', ['as' => 'password.reset',
        'uses' => 'App\Auth\AuthController@reset_password']);

    /* reset password  */
    $router->put('/app/password-reset', ['middleware' => 'tenant.connection',
        'uses' => 'App\Auth\AuthController@passwordReset']);

    /* CMS footer pages  */
    $router->get('/app/cms/listing', ['as' => 'app.cms.listing', 'middleware' => 'tenant.connection',
        'uses' => 'App\FooterPage\FooterPageController@index']);
    $router->get('/app/cms/detail', ['as' => 'app.cms.detail', 'middleware' => 'tenant.connection',
        'uses' => 'App\FooterPage\FooterPageController@cmsList']);
    $router->get('/app/cms/{slug}', ['as' => 'app.cms.show', 'middleware' => 'tenant.connection',
        'uses' => 'App\FooterPage\FooterPageController@show']);

    /* Get custom css url  */
    $router->get('/app/custom-css', ['as' => 'custom_css', 'middleware' => 'tenant.connection',
        'uses' => 'App\Tenant\TenantOptionController@getCustomCss']);

    /* Get mission listing  */
    $router->get('/app/missions/', ['as' => 'app.missions',
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete|PaginationMiddleware',
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
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete|JsonApiMiddleware',
        'uses' => 'App\Mission\MissionController@missionFavourite']);

    /* Mission Invite  */
    $router->post('/app/mission/invite', ['as' => 'app.missions.invite',
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Mission\MissionInviteController@missionInvite']);

    /* Fetch tenant option */
    $router->post('/app/tenant-option', ['as' => 'app.tenant-option',
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete|JsonApiMiddleware',
        'uses' => 'App\Tenant\TenantOptionController@fetchTenantOptionValue']);

    /* Fetch tenant settings */
    $router->get('/app/tenant-settings', ['as' => 'app.tenant-settings',
        'middleware' => 'tenant.connection',
        'uses' => 'App\Tenant\TenantActivatedSettingController@index']);

    /* Apply to a mission */
    $router->post(
        'app/mission/application',
        ['middleware' => 'tenant.connection|jwt.auth|user.profile.complete|JsonApiMiddleware',
            'uses' => 'App\Mission\MissionApplicationController@missionApplication']
    );

    /* Store mission ratings */
    $router->post(
        'app/mission/rating',
        ['middleware' => 'tenant.connection|jwt.auth|user.profile.complete|JsonApiMiddleware',
            'uses' => 'App\Mission\MissionRatingController@store']
    );

    /* Fetch user */
    $router->get('/app/user', ['as' => 'app.user',
        'middleware' => 'tenant.connection|jwt.auth|PaginationMiddleware',
        'uses' => 'App\User\UserController@index']);

    /* Fetch search-user */
    $router->get('/app/search-user', ['as' => 'app.user',
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete|PaginationMiddleware',
        'uses' => 'App\User\UserController@index']);

    /* Fetch dashboard data for users */
    $router->get('/app/dashboard', ['as' => 'app.user',
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\User\DashboardController@index']);

    /* Get mission detail  */
    $router->get('/app/mission/{missionId}', [
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Mission\MissionController@getMissionDetail']);

    /* Fetch recent volunteers */
    $router->get('/app/mission/{missionId}/volunteers', [
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete|PaginationMiddleware',
        'uses' => 'App\Mission\MissionApplicationController@getVolunteers']);

    /* Get mission related listing  */
    $router->get('/app/related-missions/{missionId}', ['as' => 'app.related-missions',
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Mission\MissionController@getRelatedMissions']);

    /* Get mission media listing  */
    $router->get('/app/mission-media/{missionId}', ['as' => 'app.mission-media',
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Mission\MissionMediaController@getMissionMedia']);

    /* Get mission comments  */
    $router->get('/app/mission/{missionId}/comments', [
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Mission\MissionCommentController@getComments']);

    /* Store mission comment */
    $router->post('/app/mission/comment', [
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete|JsonApiMiddleware',
        'uses' => 'App\Mission\MissionCommentController@store']);

    /* Get user details */
    $router->get('/app/user-detail', ['middleware' => 'tenant.connection|jwt.auth',
        'uses' => 'App\User\UserController@show']);

    /* Get city by country id */
    $router->get('/app/city/{countryId}', ['middleware' => 'tenant.connection|jwt.auth',
        'uses' => 'App\City\CityController@fetchCity']);

    /* Get timezone list */
    $router->get('/app/timezone', ['middleware' => 'tenant.connection|jwt.auth',
        'uses' => 'App\Timezone\TimezoneController@index']);

    /* Get skill list */
    $router->get('/app/skill', ['middleware' => 'tenant.connection|jwt.auth',
        'uses' => 'App\Skill\SkillController@index']);

    /* Get country list */
    $router->get('/app/country', ['middleware' => 'tenant.connection|jwt.auth',
        'uses' => 'App\Country\CountryController@index']);

    /* Get user mission */
    $router->get('/app/user/missions', [
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete|JsonApiMiddleware',
        'uses' => 'App\Mission\MissionController@getUserMissions']);
});

/* Policy pages  */
$router->get('/app/policy/listing', ['as' => 'policy.listing',
    'middleware' => 'localization|tenant.connection|jwt.auth',
    'uses' => 'App\PolicyPage\PolicyPageController@index']);
$router->get('/app/policy/{slug}', ['as' => 'policy.show',
    'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
    'uses' => 'App\PolicyPage\PolicyPageController@show']);

/* Update user details */
$router->patch('/app/user', [
    'middleware' => 'localization|tenant.connection|jwt.auth|JsonApiMiddleware',
    'uses' => 'App\User\UserController@update']);

/* Password change routing */
$router->patch('/app/change-password', ['as' => 'password.change',
    'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
    'uses' => 'App\Auth\AuthController@changePassword']);

/* Create user skill */
$router->post('/app/user/skills', ['as' => 'user.skills',
    'middleware' => 'tenant.connection|localization|jwt.auth',
    'uses' => 'App\User\UserController@linkSkill']);

/* Fetch Language json file */
$router->get('language/{lang}', ['as' => 'language',
'uses' => 'App\Language\LanguageController@fetchLanguageFile']);

/* Upload profile image */
$router->patch('/app/user/upload-profile-image', ['as' => 'upload.profile.image',
    'middleware' => 'localization|tenant.connection|jwt.auth',
    'uses' => 'App\User\UserController@uploadProfileImage']);

/* Fetch pending goal requests */
$router->get('/app/timesheet/goal-requests', ['as' => 'app.timesheet.goal-requests',
    'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete|PaginationMiddleware',
    'uses' => 'App\Timesheet\TimesheetController@getPendingGoalRequests']);

/* Export pending goal requests */
$router->get('/app/timesheet/goal-requests/export', ['as' => 'app.timesheet.goal-requests.export',
    'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
    'uses' => 'App\Timesheet\TimesheetController@exportPendingGoalRequests']);

/* Store timesheet data */
$router->post('/app/timesheet', ['as' => 'app.timesheet',
    'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
    'uses' => 'App\Timesheet\TimesheetController@store']);

/* Submit timesheet data */
$router->post('/app/timesheet/submit', ['as' => 'app.timesheet.submit',
    'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
    'uses' => 'App\Timesheet\TimesheetController@submitTimesheet']);

/* Fetch pending time requests */
$router->get('/app/timesheet/time-requests', ['as' => 'app.timesheet.time-requests',
    'middleware' => 'tenant.connection|jwt.auth|user.profile.complete|PaginationMiddleware',
    'uses' => 'App\Timesheet\TimesheetController@getPendingTimeRequests']);

/* Export pending time requests */
$router->get('/app/timesheet/time-requests/export', ['as' => 'app.timesheet.time-requests.export',
    'middleware' => 'tenant.connection|jwt.auth|user.profile.complete',
    'uses' => 'App\Timesheet\TimesheetController@exportPendingTimeRequests']);

/* Get timesheet data */
$router->get('/app/timesheet', ['as' => 'app.timesheet',
    'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
    'uses' => 'App\Timesheet\TimesheetController@index']);

/* Get timesheet data */
$router->get('/app/timesheet/{timesheetId}', ['as' => 'app.timesheet.show',
    'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
    'uses' => 'App\Timesheet\TimesheetController@show']);

/* Delete timesheet document data */
$router->delete('/app/timesheet/{timesheetId}/document/{documentId}', ['as' => 'app.timesheet.destroy',
    'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
    'uses' => 'App\Timesheet\TimesheetController@destroy']);

$router->group(['middleware' => 'localization'], function ($router) {

    /* Get volunteering history for theme */
    $router->get('/app/volunteer/history/theme', ['as' => 'app.volunteer.history.theme',
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\VolunteerHistory\VolunteerHistoryController@themeHistory']);

    /* Get volunteering history for skill */
    $router->get('/app/volunteer/history/skill', ['as' => 'app.volunteer.history.skill',
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\VolunteerHistory\VolunteerHistoryController@skillHistory']);

    /* Get volunteering  history for time missions */
    $router->get('/app/volunteer/history/time-mission', ['as' => 'app.volunteer.history.time-mission',
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete|PaginationMiddleware',
        'uses' => 'App\VolunteerHistory\VolunteerHistoryController@timeMissionHistory']);

    /* Export volunteering  history for time missions */
    $router->get('/app/volunteer/history/time-mission/export', ['as' => 'app.volunteer.history.time-mission.export',
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\VolunteerHistory\VolunteerHistoryController@exportTimeMissionHistory']);

    /* Get volunteering  history for goal missions */
    $router->get('/app/volunteer/history/goal-mission', ['as' => 'app.volunteer.history.goal-mission',
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete|PaginationMiddleware',
        'uses' => 'App\VolunteerHistory\VolunteerHistoryController@goalMissionHistory']);

    /* Export volunteering  history for goal missions */
    $router->get('/app/volunteer/history/goal-mission/export', ['as' => 'app.volunteer.history.goal-mission.export',
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\VolunteerHistory\VolunteerHistoryController@exportGoalMissionHistory']);

    /* News listing */
    $router->get('/app/news', ['as' => 'app.news.list',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete|PaginationMiddleware',
        'uses' => 'App\News\NewsController@index']);

    /* Fetch news details*/
    $router->get('/app/news/{newsId}', ['as' => 'app.news.show',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\News\NewsController@show']);

    /* Store story detail */
    $router->post('/app/story', ['as' => 'app.story.store',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Story\StoryController@store']);

    /* Delete story details */
    $router->delete('/app/story/{storyId}', ['as' => 'app.story.destroy',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Story\StoryController@destroy']);

    /* all users published story listing */
    $router->get('/app/story/list', ['as' => 'app.story.publishedStories',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete|PaginationMiddleware',
        'uses' => 'App\Story\StoryController@publishedStories']);
        
    /* Export all Story Data */
    $router->get('/app/story/export', ['as' => 'app.story.export',
         'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
         'uses' => 'App\Story\StoryController@exportStories']);
        
    /* Copy declined story */
    $router->get('/app/story/{story_id}/copy', ['as' => 'app.story.copystory',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Story\StoryController@copyStory']);

    /* Get User's story Listing */
    $router->get('/app/story/my-stories', ['as' => 'app.story.userstories',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete|PaginationMiddleware',
        'uses' => 'App\Story\StoryController@getUserStories']);

    /* Update story details */
    $router->patch('/app/story/{storyId}', ['as' => 'app.story.update',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Story\StoryController@update']);
        
    /* Fetch story details */
    $router->get('/app/story/{storyId}', ['as' => 'app.story.show',
     'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
     'uses' => 'App\Story\StoryController@show']);

    /* Submit story detail */
    $router->post('/app/story/{storyId}/submit', ['as' => 'app.story.submit',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Story\StoryController@submitStory']);

    /* Delete story image */
    $router->delete('/app/story/{storyId}/image/{imageId}', ['as' => 'app.story.removeStoryImage',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Story\StoryController@deleteStoryImage']);

    /* Mission Invite  */
    $router->post('/app/story/invite', ['as' => 'app.story.invite',
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Story\StoryInviteController@storyInvite']);
    /* Update story details */
    $router->patch('/app/story/{storyId}', ['as' => 'app.story.update',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Story\StoryController@update']);

    /* store story visitor details */
    $router->get('/app/story/{storyId}/views', ['as' => 'app.storyvisitor.store',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\StoryVisitor\StoryVisitorController@store']);

    /* Delete user mission comments */
    $router->delete('/app/dashboard/comments/{commentId}', ['as' => 'app.dashboard.comment.destroy',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Mission\MissionCommentController@destroy']);
        
    /* Export user mission comments */
    $router->get('/app/dashboard/comments/export', [
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Mission\MissionCommentController@exportComments']);

    /* Get user mission comments */
    $router->get('/app/dashboard/comments', [
        'middleware' => 'tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Mission\MissionCommentController@getUserMissionComments']);

    /* Fetch edit story details */
    $router->get('/app/edit/story/{storyId}', ['as' => 'app.edit.story',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Story\StoryController@editStory']);

    /* accept cookie agreement date*/
    $router->post('/app/accept-cookie-agreement', ['as' => 'app.cookie-agreement.accept',
        'middleware' => 'localization|tenant.connection|jwt.auth',
        'uses' => 'App\User\UserController@saveCookieAgreement']);

    /* Store or update user notification settings */
    $router->post('/app/user-notification-settings/update', ['as' => 'app.user-notification-settings.update',
        'middleware' => 'localization|tenant.connection|jwt.auth|JsonApiMiddleware',
        'uses' => 'App\Notification\NotificationTypeController@storeOrUpdate']);

    /* send message to admin*/
    $router->post('/app/message/send', ['as' => 'app.message.send',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete|JsonApiMiddleware',
        'uses' => 'App\Message\MessageController@sendMessage']);
            
    /* Get User's message Listing*/
    $router->get('/app/messages', ['as' => 'app.message.list',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete|PaginationMiddleware',
        'uses' => 'App\Message\MessageController@getUserMessages']);

    /* Delete Message details */
    $router->delete('/app/message/{messageId}', ['as' => 'app.message.destroy',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Message\MessageController@destroy']);

    /* Fetch notification settings */
    $router->get('/app/notification-settings', ['as' => 'app.notification-settings',
        'middleware' => 'localization|tenant.connection|jwt.auth',
          'uses' => 'App\Notification\NotificationTypeController@index']);

    /* Store or update user notification settings */
    $router->post('/app/user-notification-settings/update', ['as' => 'app.user-notification-settings.update',
        'middleware' => 'localization|tenant.connection|jwt.auth|JsonApiMiddleware',
        'uses' => 'App\Notification\NotificationTypeController@storeOrUpdate']);

    /* Read Unread User notification */
    $router->post('/app/notification/read-unread/{notificationId}', ['as' => 'app.user-notification.read-unread',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Notification\NotificationController@readUnreadNotification']);

    /* Clear User notification */
    $router->delete('/app/notifications/clear', ['as' => 'app.user-notifications.clear',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Notification\NotificationController@clearAllNotifications']);
        
    /* Fetch notification settings */
    $router->get('/app/notifications', ['as' => 'app.notifications',
        'middleware' => 'localization|tenant.connection|jwt.auth',
        'uses' => 'App\Notification\NotificationController@index']);

    /* Read message send by admin */
    $router->post('/app/message/read/{messageId}', ['as' => 'app.message.read',
        'middleware' => 'localization|tenant.connection|jwt.auth|user.profile.complete',
        'uses' => 'App\Message\MessageController@readMessage']);
});




/*
|
|--------------------------------------------------------------------------
| Tenant Admin Routes
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

    /* Store slider data for tenant specific */
    $router->post('/slider', ['as' => 'slider.store',
        'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware',
        'uses' => 'Admin\Slider\SliderController@store']);

    /* Get slider */
    $router->get('/slider', ['as' => 'slider', 'middleware' => 'localization|auth.tenant.admin',
        'uses' => 'Admin\Slider\SliderController@index']);

    /* Update slider data for tenant specific */
    $router->patch('/slider/{sliderId}', ['as' => 'slider.update',
        'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware',
        'uses' => 'Admin\Slider\SliderController@update']);

    /* Delete slider data for tenant specific */
    $router->delete('/slider/{sliderId}', ['as' => 'slider.delete', 'middleware' => 'localization|auth.tenant.admin',
        'uses' => 'Admin\Slider\SliderController@destroy']);

    /* Set Footer Page data for tenant specific */
    $router->group(
        ['prefix' => 'cms', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
        function ($router) {
            $router->get('/', ['as' => 'cms', 'middleware' => ['PaginationMiddleware'],
                'uses' => 'Admin\FooterPage\FooterPageController@index']);
            $router->get('/{pageId}', ['as' => 'cms.show', 'uses' => 'Admin\FooterPage\FooterPageController@show']);
            $router->post('/', ['as' => 'cms.store', 'uses' => 'Admin\FooterPage\FooterPageController@store']);
            $router->patch('/{pageId}', ['as' => 'cms.update',
                'uses' => 'Admin\FooterPage\FooterPageController@update']);
            $router->delete('/{pageId}', ['as' => 'cms.delete',
                'uses' => 'Admin\FooterPage\FooterPageController@destroy']);
        }
    );

    /* Set custom field data for tenant specific */
    $router->group(
        ['prefix' => 'metadata/users/custom_fields',
            'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
        function ($router) {
            $router->get('/', ['as' => 'metadata.users.custom_fields',
                'middleware' => ['PaginationMiddleware'], 'uses' => 'Admin\User\UserCustomFieldController@index']);
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
            $router->patch('/{missionId}', ['as' => 'missions.update',
                'uses' => 'Admin\Mission\MissionController@update']);
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
            $router->delete('/media/{mediaId}', ['as' => 'missions.media.delete',
               'uses' => 'Admin\Mission\MissionController@removeMissionMedia']);
            $router->delete('/document/{documentId}', ['as' => 'missions.document.delete',
               'uses' => 'Admin\Mission\MissionController@removeMissionDocument']);
        }
    );

    /* Set skill data for tenant user specific */
    $router->group(
        ['prefix' => 'users', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
        function ($router) {
            $router->get('/{userId}/skills', ['uses' => 'Admin\User\UserController@userSkills']);
            $router->post('/{userId}/skills', ['uses' => 'Admin\User\UserController@linkSkill']);
            $router->delete('/{userId}/skills', ['uses' => 'Admin\User\UserController@unlinkSkill']);
        }
    );

    /*Admin style routes*/
    $router->group(
        ['prefix' => 'style', 'middleware' => 'localization|auth.tenant.admin'],
        function ($router) {
            $router->post('/update-style', ['uses' => 'Admin\Tenant\TenantOptionsController@updateStyleSettings']);
            $router->get('/reset-style', ['uses' => 'Admin\Tenant\TenantOptionsController@resetStyleSettings']);
            $router->get('/download-style', ['uses' => 'Admin\Tenant\TenantOptionsController@downloadStyleFiles']);
            $router->patch('/update-image', ['uses' => 'Admin\Tenant\TenantOptionsController@updateImage']);
            $router->get('/reset-asset-images', ['uses' => 'Admin\Tenant\TenantOptionsController@resetAssetsImages']);
        }
    );

    /* Admin setting routes */
    $router->group(
        ['prefix' => 'tenant-settings', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
        function ($router) {
            $router->get('/', ['uses' => 'Admin\Tenant\TenantSettingsController@index']);
            $router->patch('/{settingId}', ['uses' => 'Admin\Tenant\TenantSettingsController@update']);
            $router->post('/', ['uses' => 'Admin\Tenant\TenantActivatedSettingController@store']);
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
            $router->get('/', ['middleware' => ['PaginationMiddleware'],
                'uses' => 'Admin\Skill\SkillController@index']);
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

    /* Set mission data for tenant specific */
    $router->group(
        ['prefix' => 'missions', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
        function ($router) {
            $router->get('', ['as' => 'missions', 'middleware' => ['PaginationMiddleware'],
                'uses' => 'Admin\Mission\MissionController@index']);
            $router->get('/{missionId}', ['as' => 'missions.show', 'uses' => 'Admin\Mission\MissionController@show']);
            $router->post('/', ['as' => 'missions.store', 'uses' => 'Admin\Mission\MissionController@store']);
            $router->patch('/{missionId}', ['as' => 'missions.update',
                'uses' => 'Admin\Mission\MissionController@update']);
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
            $router->get(
                '/{missionId}/comments',
                [
                    'as' => 'missions.comments',
                    'uses' => 'Admin\Mission\MissionCommentController@index',
                ]
            );
            $router->get(
                '/{missionId}/comments/{commentId}',
                [
                    'as' => 'missions.comments.detail',
                    'uses' => 'Admin\Mission\MissionCommentController@show',
                ]
            );
            $router->patch(
                '/{missionId}/comments/{commentId}',
                [
                    'as' => 'missions.comments.update',
                    'uses' => 'Admin\Mission\MissionCommentController@update',
                ]
            );
            $router->delete(
                '/{missionId}/comments/{commentId}',
                [
                    'as' => 'missions.comments.delete',
                    'uses' => 'Admin\Mission\MissionCommentController@destroy',
                ]
            );
        }
    );

    /* Timesheet management */
    $router->group(
        ['prefix' => 'timesheet', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
        function ($router) {
            $router->get('/{userId}', ['as' => 'user.timesheet', 'middleware' => ['PaginationMiddleware'],
                'uses' => 'Admin\Timesheet\TimesheetController@index']);
            $router->patch('/{timesheetId}', ['as' => 'update.user.timesheet.status',
                'uses' => 'Admin\Timesheet\TimesheetController@update']);
        }
    );

    /* Get countries list */
    $router->group(
        ['prefix' => 'entities/countries', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
        function ($router) {
            $router->get('/', ['uses' => 'Admin\Country\CountryController@index']);
            $router->post('/', ['uses' => 'Admin\Country\CountryController@store']);
            $router->patch('/{countryId}', ['uses' => 'Admin\Country\CountryController@update']);
            $router->delete('/{countryId}', ['uses' => 'Admin\Country\CountryController@destroy']);
            $router->get('/{countryId}/states', ['uses' => 'Admin\State\StateController@fetchState',
            'middleware' => ['PaginationMiddleware']]);
        }
    );
    
    /* Get cities by country id */
    $router->group(
        ['prefix' => 'entities/cities', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
        function ($router) {
            $router->get('/', ['uses' => 'Admin\City\CityController@index']);
            $router->get('/{countryId}', ['uses' => 'Admin\City\CityController@fetchCity',
            'middleware' => ['PaginationMiddleware']]);
            $router->post('/', ['uses' => 'Admin\City\CityController@store']);
            $router->patch('/{cityId}', ['uses' => 'Admin\City\CityController@update']);
            $router->delete('/{cityId}', ['uses' => 'Admin\City\CityController@destroy']);
        }
    );

    
    /* News category management */
    $router->group(
        ['prefix' => '/news/category', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
        function ($router) {
            $router->get('/', ['middleware' => ['PaginationMiddleware'],
                'uses' => 'Admin\NewsCategory\NewsCategoryController@index']);
            $router->get('/{newsCategoryId}', ['uses' => 'Admin\NewsCategory\NewsCategoryController@show']);
            $router->post('/', ['uses' => 'Admin\NewsCategory\NewsCategoryController@store']);
            $router->patch('/{newsCategoryId}', ['uses' => 'Admin\NewsCategory\NewsCategoryController@update']);
            $router->delete('/{newsCategoryId}', ['uses' => 'Admin\NewsCategory\NewsCategoryController@destroy']);
        }
    );

    /* News management */
    $router->group(
        ['prefix' => '/news', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
        function ($router) {
            $router->get('/', ['middleware' => ['PaginationMiddleware'],
                'uses' => 'Admin\News\NewsController@index']);
            $router->get('/{newsId}', ['uses' => 'Admin\News\NewsController@show']);
            $router->post('/', ['uses' => 'Admin\News\NewsController@store']);
            $router->patch('/{newsId}', ['uses' => 'Admin\News\NewsController@update']);
            $router->delete('/{newsId}', ['uses' => 'Admin\News\NewsController@destroy']);
        }
    );

    /* Set story data for tenant specific */
    $router->group(
        ['middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
        function ($router) {
            /* Get user stories */
            $router->get('/user/{userId}/stories', ['middleware' => ['PaginationMiddleware'],
                'uses' => 'Admin\Story\StoryController@index']);
            $router->patch('/stories/{storyId}', ['as' => 'update.story.status',
                'uses' => 'Admin\Story\StoryController@update']);
        }
    );

    /* message management */
    $router->group(
        ['prefix' => '/message', 'middleware' => 'localization|auth.tenant.admin'],
        function ($router) {
            $router->post('/send', ['as' => 'message.send','middleware' => ['JsonApiMiddleware'],
            'uses' => 'Admin\Message\MessageController@sendMessage']);
            
            $router->delete('/{messageId}', ['as' => 'message.destroy',
                'uses' => 'Admin\Message\MessageController@destroy']);

            $router->get('/list', ['as' => 'message.list', 'middleware' => 'PaginationMiddleware',
                'uses' => 'Admin\Message\MessageController@getUserMessages']);

            $router->post('/read/{messageId}', ['as' => 'message.read',
                'uses' => 'Admin\Message\MessageController@readMessage']);
        }
    );

    /* Get Activity Logs */
    $router->group(
        ['middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
        function ($router) {
            /* Get user activity logs */
            $router->get('/logs', ['middleware' => ['PaginationMiddleware'],
                'uses' => 'Admin\ActivityLog\ActivityLogController@index']);
        }
    );

    /* Availability management */
    $router->group(
        ['middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
        function ($router) {
            /* Get availability */
            $router->get('/entities/availability', ['middleware' => ['PaginationMiddleware'],
                'uses' => 'Admin\Availability\AvailabilityController@index']);

            /* Store availability */
            $router->post('/entities/availability', ['as' => 'availability.store',
                'uses' => 'Admin\Availability\AvailabilityController@store']);
            
            $router->delete('/entities/availability/{availabilityId}', ['as' => 'availability.destroy',
                'uses' => 'Admin\Availability\AvailabilityController@destroy']);
            
            $router->patch('/entities/availability/{availabilityId}', ['as' => 'availability.update',
                'uses' => 'Admin\Availability\AvailabilityController@update']);
                
            $router->get(
                '/entities/availability/{availabilityId}',
                ['uses' => 'Admin\Availability\AvailabilityController@show']
            );
        }
    );

    /* Language file management */
    $router->group(
        ['middleware' => 'localization|auth.tenant.admin'],
        function ($router) {
            /* Get language file */
            $router->get(
                '/language-file',
                ['as' => 'languagefile.fetch', 'uses' => 'Admin\Language\LanguageController@fetchLanguageFile']
            );

            /* Upload language file */
            $router->post('/language-file', ['as' => 'languagefile.upload',
            'uses' => 'Admin\Language\LanguageController@uploadLanguageFile']);
        }
    );

    /* State management */
    $router->group(
        ['prefix' => 'entities/states', 'middleware' => 'localization|auth.tenant.admin|JsonApiMiddleware'],
        function ($router) {
            $router->get('/', ['uses' => 'Admin\State\StateController@index']);
            $router->get('/{stateId}', ['uses' => 'Admin\State\StateController@show',
            'middleware' => ['PaginationMiddleware']]);
            $router->post('/', ['uses' => 'Admin\State\StateController@store']);
            $router->patch('/{stateId}', ['uses' => 'Admin\State\StateController@update']);
            $router->delete('/{stateId}', ['uses' => 'Admin\State\StateController@destroy']);
        }
    );
