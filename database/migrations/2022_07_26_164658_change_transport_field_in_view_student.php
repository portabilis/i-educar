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
        $this->dropView('students');
        $this->createView('students', '2022-07-26');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('students');
        $this->createView('students', '2022-07-25');
    }
};
