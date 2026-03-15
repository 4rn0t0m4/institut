<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'result_template', 'require_login',
        'times_to_take', 'show_progress', 'auto_continue',
        'email_required', 'email_user', 'email_admin', 'admin_email', 'is_active',
    ];

    protected $casts = [
        'require_login' => 'boolean', 'show_progress' => 'boolean', 'auto_continue' => 'boolean',
        'email_required' => 'boolean', 'email_user' => 'boolean', 'email_admin' => 'boolean', 'is_active' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }

    public function results()
    {
        return $this->hasMany(QuizResult::class)->orderBy('points_min');
    }

    public function completions()
    {
        return $this->hasMany(QuizCompletion::class);
    }
}
