<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateRegistrationsView extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->createView('registrations');
    }

    public function down(): void
    {
        $this->dropView('registrations');
    }
}
