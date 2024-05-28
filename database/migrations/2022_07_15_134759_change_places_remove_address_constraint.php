<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE public.places ALTER COLUMN city_id DROP NOT NULL;');
        DB::statement('ALTER TABLE public.places ALTER COLUMN address DROP NOT NULL;');
        DB::statement('ALTER TABLE public.places ALTER COLUMN neighborhood DROP NOT NULL;');
        DB::statement('ALTER TABLE public.places ALTER COLUMN postal_code DROP NOT NULL;');
    }

    public function down()
    {
        DB::statement('ALTER TABLE IF EXISTS public.places ALTER COLUMN city_id SET NOT NULL;');
        DB::statement('ALTER TABLE IF EXISTS public.places ALTER COLUMN address SET NOT NULL;');
        DB::statement('ALTER TABLE IF EXISTS public.places ALTER COLUMN neighborhood SET NOT NULL;');
        DB::statement('ALTER TABLE IF EXISTS public.places ALTER COLUMN postal_code SET NOT NULL;');
    }
};
