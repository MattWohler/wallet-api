<?php declare(strict_types=1);

namespace App\Http\Transformers;

use RuntimeException;

/**
 * @method array attributes($model)
 */
abstract class AbstractTransformer
{
    public const FORMAT_FULL_DATE = 'Y-m-d';
    public const FORMAT_DATE_TIME = 'Y-m-d\TH:i:sP';
    public const FORMAT_DATE_HOUR = 'Y-m-d H:i:s';

    public function __construct()
    {
        if (!method_exists($this, 'attributes')) {
            throw new RuntimeException('Transformers must implement method `attributes($model): array`');
        }
    }

    /**
     * @param  object|iterable|null  $model
     * @return array
     */
    public function transform($model): array
    {
        if ($model === null || (is_object($model) && isset($model->id) && $model->id === 0)) {
            return [];
        }

        if (is_iterable($model)) {
            $data = [];
            foreach ($model as $entity) {
                $result = $this->transform($entity);
                $data[] = $result['data'];
            }
        } else {
            $data = ['type' => $this->getModelType($model)];

            if (isset($model->id)) {
                $data['id'] = (string) $model->id;
            }

            $data['attributes'] = array_filter($this->attributes($model), static function ($value) {
                return $value !== null;
            });
        }

        return compact('data');
    }

    /**
     * @param  object  $model
     * @return string
     */
    private function getModelType($model): string
    {
        return $this->modelToResource(get_class($model));
    }

    private function modelToResource(string $model): string
    {
        $classParts = explode('\\', $model);
        return snake_case((string) array_pop($classParts), '-');
    }
}
