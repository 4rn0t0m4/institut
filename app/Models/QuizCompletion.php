<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizCompletion extends Model
{
    protected $fillable = ['quiz_id', 'result_id', 'user_id', 'score', 'email', 'ip', 'source_url', 'snapshot'];

    protected $casts = ['score' => 'decimal:2', 'snapshot' => 'array'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function result()
    {
        return $this->belongsTo(QuizResult::class, 'result_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class, 'completion_id');
    }
}
