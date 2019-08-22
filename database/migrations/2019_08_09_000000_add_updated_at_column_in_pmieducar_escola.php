<?php

use App\Support\Database\UpdatedAtTrigger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpdatedAtColumnInPmieducarEscola extends Migration
{
    use UpdatedAtTrigger;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable()->default(DB::raw('now()'));
        });

        $this->createUpdatedAtTrigger('pmieducar.escola');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropUpdatedAtTrigger('pmieducar.escola');

        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->dropColumn('updated_at')->nullable();
        });
    }
}
