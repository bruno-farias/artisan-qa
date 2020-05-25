<?php

namespace App\Models;

use App\Models\Traits\SetLocale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use SoftDeletes;
    use SetLocale;

    protected $fillable = [
        'option',
        'correct',
        'locale'
    ];

    protected $casts = [
        'correct' => 'boolean'
    ];

    // Getters/Setters

    public function getOption(): string
    {
        return $this->option;
    }

    public function setOption(string $option)
    {
        $this->option = $option;
        return $this;
    }

    public function setCorrect(bool $correct)
    {
        $this->correct = $correct;
        return $this;
    }

    public function getCorrect(): bool
    {
        return $this->correct;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    // Relationships

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }
}
