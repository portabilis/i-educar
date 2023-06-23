<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('public.educacenso_record60');
        $this->createView('public.educacenso_record60', '2022-07-26');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('public.educacenso_record60');
        $this->createView('public.educacenso_record60', '2022-05-26');
    }
};
