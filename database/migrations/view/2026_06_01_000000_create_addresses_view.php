<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesView extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->createView('addresses');
    }

    public function down(): void
    {
        $this->dropView('addresses');
    }
}
