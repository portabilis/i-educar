<?php

use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME cod_distribuicao_uniforme TO id;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME ref_cod_aluno TO student_id;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME ano TO year;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions ALTER COLUMN year TYPE smallint;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME kit_completo TO complete_kit;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME agasalho_qtd TO coat_pants_qty;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME camiseta_curta_qtd TO shirt_short_qty;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME camiseta_longa_qtd TO shirt_long_qty;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME meias_qtd TO socks_qty;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME bermudas_tectels_qtd TO shorts_tactel_qty;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME bermudas_coton_qtd TO shorts_coton_qty;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME tenis_qtd TO sneakers_qty;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME data TO distribution_date;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME agasalho_tm TO coat_pants_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME camiseta_curta_tm TO shirt_short_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME camiseta_longa_tm TO shirt_long_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME meias_tm TO socks_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME bermudas_tectels_tm TO shorts_tactel_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME bermudas_coton_tm TO shorts_coton_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME tenis_tm TO sneakers_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME ref_cod_escola TO school_id;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME camiseta_infantil_qtd TO kids_shirt_qty;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME camiseta_infantil_tm TO kids_shirt_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME calca_jeans_qtd TO pants_jeans_qty;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions ALTER COLUMN pants_jeans_qty TYPE smallint;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME calca_jeans_tm TO pants_jeans_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions ALTER COLUMN pants_jeans_tm TYPE character varying(20) COLLATE pg_catalog."default";');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME saia_qtd TO skirt_qty;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME saia_tm TO skirt_tm;');
    }

    public function down()
    {
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME id TO cod_distribuicao_uniforme;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME student_id TO ref_cod_aluno;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME year TO ano;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions ALTER COLUMN ano TYPE integer;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME complete_kit TO kit_completo;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME coat_pants_qty TO agasalho_qtd;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME shirt_short_qty TO camiseta_curta_qtd;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME shirt_long_qty TO camiseta_longa_qtd;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME socks_qty TO meias_qtd;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME shorts_tactel_qty TO bermudas_tectels_qtd;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME shorts_coton_qty TO bermudas_coton_qtd;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME sneakers_qty TO tenis_qtd;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME distribution_date TO data;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME coat_pants_tm TO agasalho_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME shirt_short_tm TO camiseta_curta_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME shirt_long_tm TO camiseta_longa_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME socks_tm TO meias_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME shorts_tactel_tm TO bermudas_tectels_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME shorts_coton_tm TO bermudas_coton_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME sneakers_tm TO tenis_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME school_id TO ref_cod_escola;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME kids_shirt_qty TO camiseta_infantil_qtd;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME kids_shirt_tm TO camiseta_infantil_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME pants_jeans_qty TO calca_jeans_qtd;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions ALTER COLUMN calca_jeans_qtd TYPE integer;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME pants_jeans_tm TO calca_jeans_tm;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions ALTER COLUMN calca_jeans_tm TYPE character varying(191) COLLATE pg_catalog."default";');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME skirt_qty TO saia_qtd;');
        DB::unprepared('ALTER TABLE IF EXISTS public.uniform_distributions RENAME skirt_tm TO saia_tm;');
    }
};
