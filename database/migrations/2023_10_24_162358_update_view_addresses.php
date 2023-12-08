<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->createView('addresses', '2023-10-24');
    }

    public function down(): void
    {
        $this->createView('addresses', '2020-01-01');
    }
};
