<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizChoice extends Model
{
    public $timestamps = false;

    protected $fillable = ['question_id', 'label', 'image', 'points', 'is_correct', 'goto', 'sort_order'];

    protected $casts = ['points' => 'decimal:2', 'is_correct' => 'boolean'];

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }
}
