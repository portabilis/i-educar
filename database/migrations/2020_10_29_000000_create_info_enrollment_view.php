<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateInfoEnrollmentView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('public.info_enrollment');
        $this->createView('public.info_enrollment', '2020-10-29');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('public.info_enrollment');
    }
}
