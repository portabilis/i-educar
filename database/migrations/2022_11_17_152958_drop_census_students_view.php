<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->dropView('census_students');
    }
};
