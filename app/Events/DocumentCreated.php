<?php
namespace App\Events;

use App\Models\Student;
use App\Models\User;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentCreated
{
    use Dispatchable, SerializesModels;

    public $student;
    public $user;
    public $name;
    public $path;
    public $type;
    public $userId;

    public function __construct(Student $student, User $user, $name, $path, $type, $userId = null)
    {
        $this->student = $student;
        $this->user = $user;
        $this->userId  = $userId;
        $this->name = $name;
        $this->path = $path;
        $this->type = $type; 
    }
}
