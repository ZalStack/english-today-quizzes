<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'description',
        'thumbnail',
        'category_id',
        'created_by',
        'duration',
        'total_questions',
        'status',
        'enroll_key',
        'start_date',
        'end_date',
        'show_score',
        'show_correct_answer',
        'show_wrong_answer',
        'show_explanation',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'show_score' => 'boolean',
        'show_correct_answer' => 'boolean',
        'show_wrong_answer' => 'boolean',
        'show_explanation' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(QuizCategory::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function attempts()
    {
        return $this->hasMany(UserQuizAttempt::class);
    }
}
