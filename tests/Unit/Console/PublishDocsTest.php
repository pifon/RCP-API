<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use Tests\TestCase;

class PublishDocsTest extends TestCase
{
    public function test_command_copies_yaml_to_storage(): void
    {
        $dest = storage_path('api-docs/api-docs.yaml');

        if (file_exists($dest)) {
            unlink($dest);
        }

        $this->artisan('docs:publish')
            ->expectsOutput('Published → '.$dest)
            ->assertExitCode(0);

        $this->assertFileExists($dest);
        $this->assertFileEquals(base_path('app/Documentation/swagger.yaml'), $dest);
    }

    public function test_command_fails_when_source_missing(): void
    {
        $src = base_path('app/Documentation/swagger.yaml');
        $backup = $src.'.bak';

        rename($src, $backup);

        try {
            $this->artisan('docs:publish')
                ->assertExitCode(1);
        } finally {
            rename($backup, $src);
        }
    }
}
