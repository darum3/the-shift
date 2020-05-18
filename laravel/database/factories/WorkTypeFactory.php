<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Eloquents\WorkType;
use Faker\Generator as Faker;

$factory->define(WorkType::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
