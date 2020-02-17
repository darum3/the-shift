<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Eloquents\Contract;
use Faker\Generator as Faker;

$factory->define(Contract::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
    ];
});
