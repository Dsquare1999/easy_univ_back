<?php
namespace App\Events;

use App\Models\Cycle;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CycleRemoved
{
    use Dispatchable, SerializesModels;

    public $cycle;
    public $userId;

    public function __construct(Cycle $cycle,   $userId = null)
    {
        $this->cycle = $cycle;
        $this->userId  = $userId;
    }
}
