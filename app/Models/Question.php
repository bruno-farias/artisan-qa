<?php

namespace App\Models;

use App\Models\Traits\SetLocale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class  Question extends Model
{
    use SoftDeletes;
    use SetLocale;

    protected $fillable = [
        'question',
        'locale'
    ];

    // Functions

    public function hasCorrectOption(): bool
    {
        return $this->options()->where('correct', '=', true)->exists();
    }

    // Getters/Setters

    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question)
    {
        $this->question = $question;
        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    // Relationships

    public function options()
    {
        return $this->hasMany(Answer::class);
    }
}
