<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishDocs extends Command
{
    protected $signature = 'docs:publish';
    protected $description = 'Copy app/Documentation/swagger.yaml → storage/api-docs/api-docs.yaml';

    public function handle(): int
    {
        $src  = base_path('app/Documentation/swagger.yaml');
        $dest = storage_path('api-docs/api-docs.yaml');

        if (! File::exists($src)) {
            $this->error("Source file not found: {$src}");
            return self::FAILURE;
        }

        File::ensureDirectoryExists(dirname($dest));
        File::copy($src, $dest);

        $this->info("Published → {$dest}");
        return self::SUCCESS;
    }
}
