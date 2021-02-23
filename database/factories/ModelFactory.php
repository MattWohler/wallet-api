<?php

use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(\App\Models\Transaction::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->numberBetween(),
        'wallet_transaction_id' => $faker->numberBetween(),
        'old_balance' => $faker->randomFloat(),
        'new_balance' => $faker->randomFloat(),
        'account' => $faker->uuid,
        'provider_transaction_id' => $faker->numberBetween(),
        'round_id' => $faker->numberBetween(),
        'amount' => $faker->numberBetween(-5000),
        'type' => $faker->randomElement(['bet', 'bet result', 'bet refund', 'negative bet']),
        'provider_id' => $faker->numberBetween(),
        'provider_game_id' => $faker->numberBetween(),
        'payload' => $faker->text(),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ];
});

$factory->define(\App\Models\Wallet\Balance::class, function (Faker\Generator $faker) {
    return [
        'account' => $faker->uuid,
        'amount' => $faker->randomFloat(),
        'currency' => $faker->currencyCode,
    ];
});

$factory->define(\App\Models\Wallet\Figure::class, function (Faker\Generator $faker) {
    return [
        'account' => $faker->uuid,
        'startDate' => Carbon::parse($faker->dateTimeBetween('-1 week')->format('Y-m-d')),
        'endDate' => Carbon::parse($faker->date('Y-m-d')),
        'loseAmount' => $faker->randomFloat(4, 1000, 2000),
        'winAmount' => $faker->randomFloat(4, 2000, 4000),
        'currency' => $faker->currencyCode,
    ];
});

$factory->define(\App\Models\Wallet\Transaction::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->numberBetween(),
        'type' => $faker->word,
        'account' => $faker->uuid,
        'amount' => $faker->randomFloat(),
        'previousBalance' => $faker->randomFloat(),
        'currentBalance' => $faker->randomFloat(),
        'currency' => $faker->currencyCode,
    ];
});

$factory->define(\App\Models\Wallet\Player::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->randomDigit,
        'account' => $faker->uuid,
        'title' => $faker->title,
        'firstName' => $faker->firstName,
        'lastName' => $faker->lastName,
        'brand' => $faker->word,
        'brandId' => $faker->randomDigit,
        'balance' => $faker->randomFloat(),
        'currency' => $faker->currencyCode,
        'country' => $faker->countryCode,
        'enableCasino' => $faker->boolean,
        'enableCards' => $faker->boolean,
        'enableHorses' => $faker->boolean,
        'enableSports' => $faker->boolean,
        'isTestAccount' => $faker->boolean
    ];
});

$factory->define(\App\Models\ApiToken::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'target' => $faker->word,
        'token' => app('encrypter')->encrypt([bin2hex(random_bytes(16))]),
        'scopes' => ['authenticate-account'],
        'is_active' => true
    ];
});

$factory->define(\Illuminate\Auth\GenericUser::class, function (Faker\Generator $faker) {
    $token = base64_encode($faker->name);

    return [
        'id' => $token,
        'token' => $token
    ];
});
