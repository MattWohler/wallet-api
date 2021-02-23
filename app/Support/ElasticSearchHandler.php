<?php declare(strict_types=1);

namespace App\Support;

use Elastica\Client;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\ElasticSearchHandler as MonologElasticSearchHandler;

class ElasticSearchHandler extends MonologElasticSearchHandler
{
    public function __construct(Client $client, array $options = [], int $level = 100, bool $bubble = true)
    {
        parent::__construct($client, [], $level, $bubble);

        $this->options = $options;
        $this->options['ignore_error'] = true;
    }

    public function isHandling(array $record): bool
    {
        return parent::isHandling($record) && (bool) config('elastic-apm.active');
    }

    protected function getDefaultFormatter(): FormatterInterface
    {
        return new ElasticSearchFormatter($this->options);
    }
}
