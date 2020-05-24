<?php

use App\Models\Question;
use Faker\Generator as Faker;

$factory->define(Question::class, function (Faker $faker) {
    return [
        'question' => $faker->text(),
        'locale' => $faker->locale
    ];
});
