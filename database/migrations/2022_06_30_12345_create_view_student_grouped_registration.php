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
        $this->down();
        $this->createView('public.exporter_student_grouped_registration', '2022-06-30');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('public.exporter_student_grouped_registration');
    }
};
