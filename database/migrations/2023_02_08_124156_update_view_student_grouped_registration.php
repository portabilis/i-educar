<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    use AsView;

    public function up()
    {
        $this->dropView('public.exporter_student_grouped_registration');
        $this->createView('public.exporter_student_grouped_registration', '2023-02-08');
    }

    public function down()
    {
        $this->dropView('public.exporter_student_grouped_registration');
        $this->createView('public.exporter_student_grouped_registration', '2022-06-30');
    }
};
