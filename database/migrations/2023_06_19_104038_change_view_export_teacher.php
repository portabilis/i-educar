<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    use AsView;

    public function up()
    {
        $this->dropView('public.exporter_teacher');
        $this->createView('public.exporter_teacher', '2023-06-19');
    }

    public function down()
    {
        $this->dropView('public.exporter_teacher');
        $this->createView('public.exporter_teacher', '2022-08-02');
    }
};
