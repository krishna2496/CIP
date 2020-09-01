<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueIndexToTenantActivatedSetting extends Migration
{
    const TENANT_ACTIVATED_SETTING_TABLE = 'tenant_activated_setting';
    const TENANT_SETTING_FOREIGN_TABLE = 'tenant_setting';
    const TENANT_UNIQUE_COLUMN = 'tenant_setting_id';

    public function up()
    {
        DB::table(self::TENANT_ACTIVATED_SETTING_TABLE)
            ->whereNotNull('deleted_at')
            ->delete();
        Schema::table(self::TENANT_ACTIVATED_SETTING_TABLE,
            function (Blueprint $table) {
                $table->unique(self::TENANT_UNIQUE_COLUMN);
            });
    }

    public function down()
    {
        Schema::table(self::TENANT_ACTIVATED_SETTING_TABLE,
            function (Blueprint $table) {
                $this->undoForeignKeys($table);
                $table->dropUnique($this->getUniqueIndexName(self::TENANT_UNIQUE_COLUMN));
                $this->redoForeignKeys($table);
            });
    }

    private function redoForeignKeys(Blueprint $table)
    {
        $table
            ->foreign(self::TENANT_UNIQUE_COLUMN)
            ->references(self::TENANT_UNIQUE_COLUMN)
            ->on(self::TENANT_SETTING_FOREIGN_TABLE)
            ->onDelete('CASCADE')
            ->onUpdate('CASCADE');
    }

    private function undoForeignKeys(Blueprint $table)
    {
        $table->dropForeign($this->getForeignKeyName(self::TENANT_UNIQUE_COLUMN));
    }

    private function getForeignKeyName(string $columnName): string
    {
        return sprintf('%s_%s_foreign', self::TENANT_ACTIVATED_SETTING_TABLE, $columnName);
    }

    private function getUniqueIndexName(string $columnName): string
    {
        return sprintf('%s_%s_unique', self::TENANT_ACTIVATED_SETTING_TABLE, $columnName);
    }
}
