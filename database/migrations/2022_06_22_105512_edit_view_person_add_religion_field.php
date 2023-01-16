<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use AsView;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('public.exporter_teacher');
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student');
        $this->dropView('public.exporter_person');

        $this->createView('public.exporter_person', '2022-06-15');
        $this->createView('public.exporter_student', '2022-06-15');
        $this->createView('public.exporter_social_assistance', '2020-05-07');
        $this->createView('public.exporter_teacher', '2021-10-13');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('public.exporter_teacher');
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student');
        $this->dropView('public.exporter_person');

        $this->createView('public.exporter_person', '2022-04-14');
        $this->createView('public.exporter_student', '2022-04-28');
        $this->createView('public.exporter_social_assistance', '2020-05-07');
        $this->createView('public.exporter_teacher', '2021-10-13');
    }
};
