<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateIndividualsView extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->down();

        $this->createView('individuals');
    }

    public function down(): void
    {
        $this->dropView('individuals');
    }
}
