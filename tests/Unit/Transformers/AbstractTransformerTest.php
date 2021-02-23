<?php

namespace Tests\Unit\Transformers;

use App\Http\Transformers\AbstractTransformer;
use Tests\Fakes\FakeModel;
use Tests\TestCase;

class AbstractTransformerTest extends TestCase
{
    public function test_transformer_can_convert_one_model()
    {
        $model = new FakeModel([
            'attribute_a' => $this->faker->word,
            'attribute_b' => $this->faker->word,
            'id' => $this->faker->numberBetween()
        ]);

        $data = $this->getTransformer()->transform($model);

        $this->assertEquals([
            'data' => [
                'type' => 'fake-model',
                'id' => (string) $model->id,
                'attributes' => [
                    'attribute_a' => $model->attribute_a,
                    'attribute_b' => $model->attribute_b,
                ]
            ]
        ], $data);
    }

    public function test_transformer_can_convert_list_of_models()
    {
        $model1 = new FakeModel(['attribute_a' => $this->faker->word, 'attribute_b' => $this->faker->word, 'id' => 1]);
        $model2 = new FakeModel(['attribute_a' => $this->faker->word, 'attribute_b' => $this->faker->word, 'id' => 2]);

        $data = $this->getTransformer()->transform(collect([$model1, $model2]));

        $this->assertEquals([
            'data' => [
                [
                    'type' => 'fake-model',
                    'id' => '1',
                    'attributes' => [
                        'attribute_a' => $model1->attribute_a,
                        'attribute_b' => $model1->attribute_b,
                    ]
                ],
                [
                    'type' => 'fake-model',
                    'id' => '2',
                    'attributes' => [
                        'attribute_a' => $model2->attribute_a,
                        'attribute_b' => $model2->attribute_b,
                    ]
                ],
            ]
        ], $data);
    }

    protected function getTransformer(): AbstractTransformer
    {
        return new class() extends AbstractTransformer
        {
            public function attributes($model): array
            {
                return [
                    'attribute_a' => $model->attribute_a,
                    'attribute_b' => $model->attribute_b,
                ];
            }
        };
    }
}
