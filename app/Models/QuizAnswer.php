<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    public $timestamps = false;

    protected $fillable = ['completion_id', 'question_id', 'answer', 'points', 'is_correct', 'comment'];

    protected $casts = ['points' => 'decimal:2', 'is_correct' => 'boolean'];

    public function completion()
    {
        return $this->belongsTo(QuizCompletion::class, 'completion_id');
    }

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }
}
