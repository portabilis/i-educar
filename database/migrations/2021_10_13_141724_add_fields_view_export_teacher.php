<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
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
        $this->dropView('public.exporter_teacher');
    }

    private function upViews()
    {
        $this->createView('public.exporter_teacher', '2021-10-13');
    }

    private function downViews()
    {
        $this->createView('public.exporter_teacher', '2020-04-07');
    }
};
