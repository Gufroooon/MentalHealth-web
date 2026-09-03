<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReflectionJournal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rule_id',
        'prompt_topic',
        'prompt_snapshot',
        'question_snapshot',
        'user_response',
        'mood_after',
    ];

    protected $casts = [
        'mood_after' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseRule::class, 'rule_id');
    }
}
