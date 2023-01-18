<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class AddRaceViewExportStudent extends Migration
{
    use AsView;

    public function up()
    {
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student');

        $this->createView('public.exporter_student', '2022-04-26');
        $this->createView('public.exporter_social_assistance', '2020-05-07');
    }

    public function down()
    {
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student');

        $this->createView('public.exporter_student', '2021-07-19');
        $this->createView('public.exporter_social_assistance', '2020-05-07');
    }
}
