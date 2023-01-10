<?php

$ref_cod_curso = $_POST['ref_cod_curso'];
if (is_numeric(value: $ref_cod_curso)) {
    $obj_curso = new clsPmieducarCurso(cod_curso: $ref_cod_curso);
    $det_curso = $obj_curso->detalhe();
    if (is_numeric(value: $det_curso['ref_cod_tipo_avaliacao'])) {
        echo $det_curso['padrao_ano_escolar'];
    } else {
        echo 1;
    }
}
