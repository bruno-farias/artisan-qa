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

    // Setters

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

    // Relationships

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
