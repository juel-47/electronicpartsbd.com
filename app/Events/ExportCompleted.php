<?php

namespace App\Events;

use App\Models\ExportJob;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExportCompleted implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $job;
    public $userId;

    public function __construct(ExportJob $exportJob)
    {
        $this->job = [
            'id' => $exportJob->id,
            'filename' => $exportJob->filename,
            'format' => strtoupper($exportJob->format),
            'status' => $exportJob->status,
            'file_size' => $exportJob->file_size ? round($exportJob->file_size / 1024, 2) . ' KB' : '-',
            'download_url' => $exportJob->status === 'completed' ? route('products.export.download_file', $exportJob->id) : '#',
            'created_at' => $exportJob->created_at->format('d M Y, h:i A'),
        ];
        $this->userId = $exportJob->user_id;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('exports.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'export.completed';
    }
}
