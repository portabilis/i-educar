<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use AsView;

    public function up()
    {
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student');
        $this->createView('public.exporter_student', '2024-05-06');
        $this->createView('public.exporter_social_assistance', '2020-05-07');

        $this->dropView('public.exporter_student_grouped_registration');
        $this->createView('public.exporter_student_grouped_registration', '2024-05-06');
    }

    public function down()
    {
        $this->dropView('public.exporter_student_grouped_registration');
        $this->createView('public.exporter_student_grouped_registration', '2024-04-25');

        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student');
        $this->createView('public.exporter_student', '2024-04-25');
        $this->createView('public.exporter_social_assistance', '2020-05-07');

    }
};
