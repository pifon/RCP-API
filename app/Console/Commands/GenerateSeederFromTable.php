<?php

/*
 *
 * Generate (may overwrite) seeder from existing table data
 * usage:
 *    php artisan make:seeder-from-table users UserSeeder
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GenerateSeederFromTable extends Command
{
    protected $signature = 'make:seeder-from-table {table} {seederName}';

    protected $description = 'Generate a seeder from existing table data';

    public function handle()
    {
        $table = $this->argument('table');
        $seederName = $this->argument('seederName');

        $data = DB::table($table)->get()->map(function ($item) {
            return (array) $item;
        })->toArray();

        if (empty($data)) {
            $this->error("Table {$table} is empty!");

            return 1;
        }

        $content = "<?php\n\nuse Illuminate\Database\Seeder;\nuse Illuminate\Support\Facades\DB;\n\n";
        $content .= "class {$seederName} extends Seeder\n{\n";
        $content .= "    public function run()\n    {\n";
        $content .= "        DB::table('{$table}')->truncate();\n\n";
        $content .= "        DB::table('{$table}')->insert([\n";

        foreach ($data as $row) {
            $rowStr = var_export($row, true);
            $content .= "            {$rowStr},\n";
        }

        $content .= "        ]);\n    }\n}\n";

        $path = database_path('seeders/'.$seederName.'.php');
        File::put($path, $content);

        $this->info("Seeder {$seederName} generated successfully at {$path}");
    }
}
