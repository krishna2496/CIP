<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDonationIpWhitelistOnActivityLogTypeEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_log', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `activity_log` CHANGE `type` `type` ENUM('AUTH','USERS','MISSION','COMMENT','MESSAGE','USERS_CUSTOM_FIELD','USER_PROFILE','USER_PROFILE_IMAGE','NEWS_CATEGORY','NEWS','VOLUNTEERING_TIMESHEET','VOLUNTEERING_TIMESHEET_DOCUMENT','SLIDER','STYLE_IMAGE','STYLE','TENANT_OPTION','TENANT_SETTINGS','FOOTER_PAGE','POLICY_PAGE','MISSION_THEME','SKILL','USER_SKILL','USER_COOKIE_AGREEMENT','GOAL_TIMESHEET','TIME_TIMESHEET','TIME_MISSION_TIMESHEET','GOAL_MISSION_TIMESHEET','STORY','MISSION_COMMENTS','STORY_IMAGE','STORY_VISITOR','NOTIFICATION_SETTING','NOTIFICATION','AVAILABILITY','MISSION_DOCUMENT','MISSION_MEDIA','COUNTRY','CITY','TENANT_LANGUAGE','MISSION_CUSTOM_TAB','CUSTOM_MENU','STATE','ORGANIZATION','MISSION_TAB', 'DONATION_IP_WHITELIST') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_log', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `activity_log` CHANGE `type` `type` ENUM('AUTH','USERS','MISSION','COMMENT','MESSAGE','USERS_CUSTOM_FIELD','USER_PROFILE','USER_PROFILE_IMAGE','NEWS_CATEGORY','NEWS','VOLUNTEERING_TIMESHEET','VOLUNTEERING_TIMESHEET_DOCUMENT','SLIDER','STYLE_IMAGE','STYLE','TENANT_OPTION','TENANT_SETTINGS','FOOTER_PAGE','POLICY_PAGE','MISSION_THEME','SKILL','USER_SKILL','USER_COOKIE_AGREEMENT','GOAL_TIMESHEET','TIME_TIMESHEET','TIME_MISSION_TIMESHEET','GOAL_MISSION_TIMESHEET','STORY','MISSION_COMMENTS','STORY_IMAGE','STORY_VISITOR','NOTIFICATION_SETTING','NOTIFICATION','AVAILABILITY','MISSION_DOCUMENT','MISSION_MEDIA','COUNTRY','CITY','TENANT_LANGUAGE','MISSION_CUSTOM_TAB','CUSTOM_MENU','STATE','ORGANIZATION','MISSION_TAB') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL");
        });
    }
}
