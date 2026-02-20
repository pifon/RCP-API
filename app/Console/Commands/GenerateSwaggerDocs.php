<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Overrides the l5-swagger:generate command.
 *
 * This project maintains its OpenAPI spec as a hand-written YAML file
 * (app/Documentation/swagger.yaml) rather than PHP annotations, so
 * the default swagger-php scanner has nothing to find.  We simply
 * delegate to docs:publish which copies the YAML into storage.
 */
class GenerateSwaggerDocs extends Command
{
    protected $signature = 'l5-swagger:generate {documentation?}';

    protected $description = 'Publish hand-written OpenAPI YAML (overrides default l5-swagger generator)';

    public function handle(): int
    {
        return $this->call('docs:publish');
    }
}
