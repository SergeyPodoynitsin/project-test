<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\StatusEnum;

class Status extends Model
{
    use HasFactory;

    protected $table = "statuses";

    protected $fillable = [
        'task_id',
        'status'
    ];

    protected $casts = [
        'status' => StatusEnum::class
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

}
