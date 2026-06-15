<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsView extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->createView('students');
    }

    public function down(): void
    {
        $this->dropView('students');
    }
}
