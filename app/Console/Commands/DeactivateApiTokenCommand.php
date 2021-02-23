<?php declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Repositories\Contracts\ApiTokenRepository;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeactivateApiTokenCommand extends Command
{
    /** @var string */
    protected $signature = 'token:deactivate {name} {target}';

    /** @var string */
    protected $description = 'Deactivate api token';

    public function handle(ApiTokenRepository $repository): void
    {
        $this->line('<info>Deactivating token for requested parameters</info>');

        try {
            $token = $repository->findByNameAndTarget(
                (string) $this->argument('name'),
                (string) $this->argument('target')
            );

            $repository->deactivate($token);
            $this->line('<info>Token deactivated</info>');
        } catch (ModelNotFoundException $e) {
            $this->line('<error>Token not found</error>');
        } catch (Exception $e) {
            $this->line("\n<error>{$e->getMessage()}</error>\n");
        }
    }
}
