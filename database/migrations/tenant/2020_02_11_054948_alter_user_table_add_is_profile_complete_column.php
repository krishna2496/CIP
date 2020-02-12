<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserTableAddIsProfileCompleteColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `user` ADD `is_profile_complete` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `cookie_agreement_date`");
            \DB::statement("ALTER TABLE `user` CHANGE `first_name` `first_name` VARCHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, CHANGE `last_name` `last_name` VARCHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, CHANGE `availability_id` `availability_id` BIGINT(20) UNSIGNED NULL, CHANGE `why_i_volunteer` `why_i_volunteer` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, CHANGE `employee_id` `employee_id` VARCHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, CHANGE `profile_text` `profile_text` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, CHANGE `status` `status` ENUM('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '1', CHANGE `is_profile_complete` `is_profile_complete` ENUM('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '0'");
            \DB::statement("ALTER TABLE `user` CHANGE `language_id` `language_id` INT(10) UNSIGNED NULL");
            \DB::statement("ALTER TABLE `user` CHANGE `city_id` `city_id` BIGINT(20) UNSIGNED NULL, CHANGE `country_id` `country_id` BIGINT(20) UNSIGNED NULL");
            \DB::statement("ALTER TABLE `user` CHANGE `timezone_id` `timezone_id` BIGINT(20) UNSIGNED NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `user` ADD `is_profile_complete` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `cookie_agreement_date`");
            \DB::statement("ALTER TABLE `user` CHANGE `first_name` `first_name` VARCHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, CHANGE `last_name` `last_name` VARCHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, CHANGE `availability_id` `availability_id` BIGINT(20) UNSIGNED NULL, CHANGE `why_i_volunteer` `why_i_volunteer` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, CHANGE `employee_id` `employee_id` VARCHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, CHANGE `profile_text` `profile_text` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, CHANGE `status` `status` ENUM('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '1', CHANGE `is_profile_complete` `is_profile_complete` ENUM('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '0'");
            \DB::statement("ALTER TABLE `user` CHANGE `language_id` `language_id` INT(10) UNSIGNED NULL");
            \DB::statement("ALTER TABLE `user` CHANGE `city_id` `city_id` BIGINT(20) UNSIGNED NULL, CHANGE `country_id` `country_id` BIGINT(20) UNSIGNED NULL");
            \DB::statement("ALTER TABLE `user` CHANGE `timezone_id` `timezone_id` BIGINT(20) UNSIGNED NULL");
        });
    }
}
