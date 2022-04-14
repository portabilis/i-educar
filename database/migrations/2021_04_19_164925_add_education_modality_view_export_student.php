<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class AddEducationModalityViewExportStudent extends Migration
{
    use AsView;

    public function up()
    {
        $this->dropViews();
        $this->upViews();
    }

    public function down()
    {
        $this->dropViews();
        $this->downViews();
    }

    private function dropViews()
    {
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student');
    }

    private function upViews()
    {
        $this->createView('public.exporter_student', '2021-04-19');
        $this->createView('public.exporter_social_assistance', '2020-05-07');
    }

    private function downViews()
    {
        $this->createView('public.exporter_student', '2021-03-17');
        $this->createView('public.exporter_social_assistance', '2020-05-07');
    }
}
