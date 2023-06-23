<?php

use App\Support\Database\IncrementSequence;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    use IncrementSequence;

    public function up()
    {
        $this->incrementSequence('notification_type');
    }
};
