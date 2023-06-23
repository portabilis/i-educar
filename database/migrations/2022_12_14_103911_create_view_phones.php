<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    use AsView;

    public function up()
    {
        if (!Schema::hasTable('phones')) {
            $this->createView('phones');
        }
    }
};
