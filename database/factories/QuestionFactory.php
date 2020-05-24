<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Question::class, function (Faker $faker) {
    return [
        'question' => $faker->text(),
        'locale' => $faker->locale
    ];
});
