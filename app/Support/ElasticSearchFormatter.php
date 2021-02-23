<?php declare(strict_types=1);

namespace App\Support;

use Elastica\Document;
use Illuminate\Http\Request;
use Monolog\Formatter\ElasticaFormatter;

class ElasticSearchFormatter extends ElasticaFormatter
{
    /** @var array - elasticsearch field options */
    protected $options;

    public function __construct(array $options)
    {
        parent::__construct($options['index'], $options['type']);
        $this->options = $options;
    }

    public function format(array $record): Document
    {
        $data = $this->options;

        if (isset($record['message'])) {
            $data = $this->buildMessage($data, $record);
        }

        if (isset($record['level'])) {
            $data['log']['level'] = $record['level'];
        }

        if (isset($record['level_name'])) {
            $data['log']['level_name'] = $record['level_name'];
        }

        return $this->getDocument($data);
    }

    protected function buildMessage(array $data, array $record): array
    {
        $message = json_decode($record['message'], true);
        $request = app(Request::class);

        $data['message'] = array_get($message, 'method').' '.array_get($message, 'endpoint');
        $data['trace'] = [
            'id' => $request->header('X-Request-Id'),
            'parent_id' => $request->header('X-Correlation-Id')
        ];

        if (!empty($message['request'])) {
            $data['request'] = json_encode($message['request']);
        }

        if (!empty($message['response'])) {
            $data['response'] = json_encode($message['response']);
        }

        if (!empty($message['error'])) {
            $data['error'] = json_encode([
                'error' => $message['error'],
                'code' => $message['code'],
                'file' => $message['file'],
                'line' => $message['line'],
            ]);
        }

        return $data;
    }
}
