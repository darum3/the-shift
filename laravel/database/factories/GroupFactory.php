<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Eloquents\Group;
use Faker\Generator as Faker;

$factory->define(Group::class, function (Faker $faker) {
    return [
        'name' => $faker->companySuffix,
    ];
});
