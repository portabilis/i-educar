<?php

$ref_cod_curso = $_POST['ref_cod_curso'];
if (is_numeric($ref_cod_curso)) {
    $obj_curso = new clsPmieducarCurso($ref_cod_curso);
    $det_curso = $obj_curso->detalhe();
    if (is_numeric($det_curso['ref_cod_tipo_avaliacao'])) {
        echo $det_curso['padrao_ano_escolar'];
    } else {
        echo 1;
    }
}
