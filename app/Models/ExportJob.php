<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportJob extends Model
{
    protected $table = 'export_jobs';

    protected $fillable = [
        'user_id', 'filename', 'format', 'filters',
        'status', 'file_path', 'file_size', 'error_message',
    ];

    protected $casts = [
        'filters' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }
}
