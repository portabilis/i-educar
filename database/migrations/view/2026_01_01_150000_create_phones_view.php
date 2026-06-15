<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreatePhonesView extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->createView('phones');
    }

    public function down(): void
    {
        $this->dropView('phones');
    }
}
