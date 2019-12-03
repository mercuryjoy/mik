<?php

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

$factory->define(App\Admin::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Distributor::class, function (Faker\Generator $faker) {
    return [
        'name' => '经销商' . $faker->word,
        'level' => $faker->randomElement([1, 2]),
    ];
});

$factory->define(App\Shop::class, function (Faker\Generator $faker) {
    return [
        'name' => '终端' . $faker->word,
        'level' => $faker->randomElement(['A', 'B', 'C', 'D']),
        'address' => $faker->address,
    ];
});

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'gender' => $faker->randomElement(['male', 'female']),
        'telephone' => $faker->phoneNumber,
        'password' => bcrypt(str_random(10)),
        'status' => $faker->randomElement(['pending', 'normal', 'pending']),
        'money_balance' => $faker->randomNumber(4),
        'point_balance' => $faker->randomNumber(3) * 10,
    ];
});

$factory->define(App\FundingPoolLog::class, function (Faker\Generator $faker) {
    $type = $faker->randomElement(['deposit', 'user_withdraw']);

    return [
        'type' => $type,
        'amount' => $faker->randomNumber(3) * ($type == 'deposit' ? 1 : -1),
        'comment' => $faker->sentence(),
    ];
});
