<?php

namespace App\Models;

use App\Models\Traits\SetLocale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;
    use SetLocale;

    protected $fillable = [
        'question'
    ];

    // Functions

    public function hasCorrectOption(): bool
    {
        return $this->getOptions()->where(['correct' => true])->exists();
    }

    // Getters

    public function getOptions()
    {
        return $this->options();
    }

    // Setters

    public function setQuestion(string $question)
    {
        $this->question = $question;
        return $this;
    }

    // Relationships

    public function options()
    {
        return $this->hasMany(Answer::class);
    }
}
