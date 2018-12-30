DELETE FROM modules.regra_avaliacao WHERE id IN (1,2,3,4,5,6,7);
DELETE FROM pmieducar.serie WHERE regra_avaliacao_id IN (1,2,3,4,5,6,7);
DELETE FROM pmieducar.turma WHERE ref_ref_cod_serie >=1 AND ref_ref_cod_serie <=15 ;
