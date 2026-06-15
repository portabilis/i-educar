<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreatePeopleView extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->createView('persons');
    }

    public function down(): void
    {
        $this->dropView('persons');
    }
}
