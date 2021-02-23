<?php declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Repositories\Contracts\ApiTokenRepository;
use Exception;
use Illuminate\Console\Command;

class GenerateApiTokenCommand extends Command
{
    /** @var string */
    protected $signature = 'token:generate {name} {target} {scopes* : The route names accessible with this token}';

    /** @var string */
    protected $description = 'Generate api token';

    public function handle(ApiTokenRepository $repository): void
    {
        $this->line('<info>Generating token for requested parameters</info>');

        try {
            $generated = $repository->create(
                (string) $this->argument('name'),
                (string) $this->argument('target'),
                (array) $this->argument('scopes')
            );

            $this->line('<info>Token generated</info>');
            $this->line("\n<info>{$generated->token}</info>\n");
            $this->line('<info>Please save token as its unique for name and target</info>');
        } catch (Exception $e) {
            $message = $e->getMessage();
            $message = str_contains($message, 'Integrity constraint violation')
                ? 'Duplicate token generation attempt for parameters'
                : $message;

            $this->line("\n<error>{$message}</error>\n");
        }
    }
}
