<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Covering indexes for the employee counts / lists on the dashboard and lists. tbl_employees rows are
 * wide (QR code SVG, password), so a count that has to read the rows scans the whole table.
 */
return new class extends Migration
{
    private $indexes = [
        'tbl_employees' => [
            ['employee_isactive', 'employee_status', 'employee_gender'],
            ['employee_isactive', 'employee_gender'],
            ['employee_isactive', 'employee_status', 'employee_doj'],
            ['employee_isactive', 'employee_doj', 'employee_department', 'employee_designation'],
            ['employee_isactive', 'employee_senior_manager', 'employee_junior_manager', 'employee_is_manager'],
        ],
        'tbl_personal_informations' => [['employeeid', 'dob']],
    ];

    public function up()
    {
        foreach ($this->indexes as $table => $sets) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            foreach ($sets as $columns) {
                $name = $this->indexName($table, $columns);
                if (!Schema::hasColumns($table, $columns) || $this->indexExists($table, $name)) {
                    continue;
                }
                $cols = implode(', ', array_map(fn($column) => "`$column`", $columns));
                DB::statement("ALTER TABLE `$table` ADD INDEX `$name` ($cols)");
            }
        }
    }

    public function down()
    {
        foreach ($this->indexes as $table => $sets) {
            foreach ($sets as $columns) {
                $name = $this->indexName($table, $columns);
                if (Schema::hasTable($table) && $this->indexExists($table, $name)) {
                    DB::statement("ALTER TABLE `$table` DROP INDEX `$name`");
                }
            }
        }
    }

    private function indexName($table, array $columns)
    {
        $name = 'cover_' . substr($table, 4) . '_' . implode('_', $columns);
        return strlen($name) > 64 ? substr($name, 0, 55) . '_' . substr(md5($name), 0, 8) : $name;
    }

    private function indexExists($table, $name)
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('index_name', $name)
            ->exists();
    }
};
