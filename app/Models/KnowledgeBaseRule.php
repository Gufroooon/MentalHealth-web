<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'trigger_conditions_json',
        'title',
        'reflection_prompt',
        'guided_question',
        'action_title',
        'action_suggestion',
        'action_suggestion_id',
        'priority',
    ];

    protected $casts = [
        'trigger_conditions_json' => 'array',
        'priority' => 'integer',
    ];
}
