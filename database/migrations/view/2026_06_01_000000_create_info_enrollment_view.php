<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateInfoEnrollmentView extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->down();

        $this->createView('public.info_enrollment');
    }

    public function down(): void
    {
        $this->dropView('public.info_enrollment');
    }
}
