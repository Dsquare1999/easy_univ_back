<?php
namespace App\Events;

use App\Models\Student;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentCreated
{
    use Dispatchable, SerializesModels;

    public $student;
    public $userId;

    public function __construct(Student $student,   $userId = null)
    {
        $this->student = $student;
        $this->userId  = $userId;
    }
}
