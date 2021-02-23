<?php

namespace Tests\Fakes;

use Jenssegers\Model\Model;

/**
 * @property int $id
 * @property mixed $attribute_a
 * @property mixed $attribute_b
 */
class FakeModel extends Model
{
    protected $fillable = [
        'id',
        'attribute_a',
        'attribute_b',
    ];
}
