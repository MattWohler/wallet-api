<?php

namespace Tests;

use Illuminate\Http\JsonResponse;
use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;
use Spatie\Snapshots\Exceptions\CantBeSerialized;

class JsonResponseDriver implements Driver
{
    public function serialize($data): string
    {
        if (!$data instanceof JsonResponse) {
            throw new CantBeSerialized('Only Illuminate\Http\JsonResponse can be serialized to ResponseJson');
        }

        $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

        return json_encode($data->getData(), $options).PHP_EOL;
    }

    public function extension(): string
    {
        return 'json';
    }

    public function match($expected, $actual)
    {
        Assert::assertInstanceOf(JsonResponse::class, $actual);

        Assert::assertJsonStringEqualsJsonString(
            $expected,
            $this->serialize($actual),
            'Json response snapshot does not match'
        );
    }
}
