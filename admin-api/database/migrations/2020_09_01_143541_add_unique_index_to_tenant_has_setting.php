<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueIndexToTenantHasSetting extends Migration
{
    const TENANT_HAS_SETTING_TABLE = 'tenant_has_setting';
    const TENANT_UNIQUE_COLUMNS = ['tenant_id', 'tenant_setting_id'];

    public function up()
    {
        DB::table(self::TENANT_HAS_SETTING_TABLE)
            ->whereNotNull('deleted_at')
            ->delete();
        Schema::table(self::TENANT_HAS_SETTING_TABLE,
            function (Blueprint $table) {
                $table->unique(self::TENANT_UNIQUE_COLUMNS);
            });
    }

    public function down()
    {
        Schema::table(self::TENANT_HAS_SETTING_TABLE,
            function (Blueprint $table) {
                $this->undoForeignKeys($table);
                $table->dropUnique(self::TENANT_UNIQUE_COLUMNS);
                $this->redoForeignKeys($table);
            });
    }

    private function redoForeignKeys(Blueprint $table)
    {
        $table
            ->foreign('tenant_id')
            ->references('tenant_id')
            ->on('tenant')
            ->onDelete('CASCADE')
            ->onUpdate('CASCADE');
        $table
            ->foreign('tenant_setting_id')
            ->references('tenant_setting_id')
            ->on('tenant_setting')
            ->onDelete('CASCADE')
            ->onUpdate('CASCADE');
    }

    private function undoForeignKeys(Blueprint $table)
    {
        $table->dropForeign($this->getForeignKeyName('tenant_id'));
        $table->dropForeign($this->getForeignKeyName('tenant_setting_id'));
    }

    private function getForeignKeyName(string $columnName): string
    {
        return sprintf('%s_%s_foreign', self::TENANT_HAS_SETTING_TABLE, $columnName);
    }
}
