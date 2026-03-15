<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizResult extends Model
{
    public $timestamps = false;

    protected $fillable = ['quiz_id', 'title', 'description', 'points_min', 'points_max', 'redirect_url', 'image', 'sort_order'];

    protected $casts = ['points_min' => 'decimal:2', 'points_max' => 'decimal:2'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
