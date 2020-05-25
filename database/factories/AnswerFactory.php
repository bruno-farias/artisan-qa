<?php

use App\Models\Answer;
use Faker\Generator as Faker;

$factory->define(Answer::class, function (Faker $faker) {
    return [
        'question_id' => factory(\App\Models\Question::class)->create()->id,
        'option' => $faker->text(),
        'correct' => $faker->boolean,
        'locale' => $faker->locale,
    ];
});
