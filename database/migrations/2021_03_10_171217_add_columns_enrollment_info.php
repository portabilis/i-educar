<?php

use Illuminate\Database\Migrations\Migration;

class AddColumnsEnrollmentInfo extends Migration
{
    use \App\Support\Database\AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('public.info_enrollment');
        $this->createView('public.info_enrollment', '2021-03-10');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('public.info_enrollment');
        $this->createView('public.info_enrollment', '2020-10-29');
    }
}
