<?php

namespace Djoudi\LaravelH5p\Eloquents;

use Illuminate\Database\Eloquent\Model;

class H5pResult extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'content_id',
        'subcontent_id',
        'user_id',
        'score',
        'max_score',
        'opened',
        'finished',
        'time',
        'description',
        'correct_responses_pattern',
        'response',
        'additionals'
    ];
}
