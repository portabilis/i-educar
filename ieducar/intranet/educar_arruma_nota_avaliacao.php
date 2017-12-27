<?php

require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

$sql = "SELECT cod_nota_aluno, ref_cod_matricula, ref_cod_disciplina, nota FROM 
pmieducar.nota_aluno na, pmieducar.matricula m WHERE
na.ativo=1 and m.cod_matricula=na.ref_cod_matricula
and m.ativo=1 and m.ano=2007 and m.aprovado in (1,2,3) and
na.modulo >= 5
order by ref_cod_matricula,na.data_cadastro";
//die("a");
$db = new clsBanco();
$db->Consulta($sql);
$notas_exame = array();
while ($db->ProximoRegistro())
{
    list($cod_nota_aluno, $ref_cod_matricula, $ref_cod_disciplina, $nota) = $db->Tupla();
    $notas_exame[$ref_cod_matricula][$ref_cod_disciplina][] = $cod_nota_aluno;
}
die("ss");
$matriculas = array();
foreach ($notas_exame as $ref_cod_matricula => $nota)
{
    foreach ($nota as $nota_sozinha)
    {
        if (count($nota_sozinha) > 1) {
            echo $ref_cod_matricula."<br>";
//          $obj_notas_alunossss = new clsPmieducarNotaAluno($nota_sozinha[count($nota_sozinha)-1], null, null, null, null, null, null, 184580);
//          if($obj_notas_alunossss->excluir())
//          {
//              $aprovado=1;
//              $obj_matricula = new clsPmieducarMatricula($ref_cod_matricula);
//              $det_matricula = $obj_matricula->detalhe();
//              $ref_ref_cod_serie = $det_matricula["ref_ref_cod_serie"];
//              $ref_ref_cod_escola = $det_matricula["ref_ref_cod_escola"];
//              $obj_serie_disciplina = new clsPmieducarEscolaSerieDisciplina();
//              $lst_serie_disciplina = $obj_serie_disciplina->lista($ref_ref_cod_serie, $ref_ref_cod_escola);
//              if (is_array($lst_serie_disciplina) && is_numeric($ref_ref_cod_serie) && is_numeric($ref_ref_cod_escola))
//              {
//                  $soma_notas = array();
//                  $soma_faltas = array();
//                  $nota_media_aluno = array();
//                  foreach ($lst_serie_disciplina as $serie_disciplina)
//                  {
//                      /************************NOTAS********************/
//                      $obj_dispensa_disciplina = new clsPmieducarDispensaDisciplina();
//                      $lst_dispensa_disciplina = $obj_dispensa_disciplina->lista($cod_matricula, $ref_ref_cod_serie, $ref_ref_cod_escola, $serie_disciplina["ref_cod_disciplina"], null, null, null, null, null, null, null, 1);
//                      if (!is_array($lst_dispensa_disciplina))
//                      {
//                          $possui_nota_exame = false;
//                          $obj_nota_aluno = new clsPmieducarNotaAluno();
//                          $obj_nota_aluno->setOrderby("modulo ASC");
//                          $lst_nota_aluno = $obj_nota_aluno->lista(null, null, null, $ref_ref_cod_serie, $ref_ref_cod_escola, $serie_disciplina["ref_cod_disciplina"], $cod_matricula, null, null, null, null, null, null, 1);
//                          if (is_array($lst_nota_aluno) && !dbBool($media_especial))
//                          {
//                              foreach ($lst_nota_aluno as $key => $nota_aluno)
//                              {
//                                  if ($nota_aluno["nota"]) {
//                                      $soma_notas[$serie_disciplina["ref_cod_disciplina"]] += $nota_aluno["nota"] * 2;
//                                      $possui_nota_exame = true;
//                                  } else {
//                                      $obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores($nota_aluno["ref_ref_cod_tipo_avaliacao"], $nota_aluno["ref_sequencial"]);
//                                      $det_avaliacao_valores = $obj_avaliacao_valores->detalhe();
//                                      $soma_notas[$serie_disciplina["ref_cod_disciplina"]] += $det_avaliacao_valores["valor"];
//                                  }
//                              }
//                          }
//                          if ($possui_nota_exame)
//                          {
//                              $soma_notas[$serie_disciplina["ref_cod_disciplina"]] /= 6;
//                              if ($soma_notas[$serie_disciplina["ref_cod_disciplina"]] < 5.0) {
//                                  $aprovado=2;
//                              }
//                          }
//                          else
//                          {
//                              $soma_notas[$serie_disciplina["ref_cod_disciplina"]] /= 4;
//                          }
//                          /*********************ACABOU NOTAS*********************/
//                      }
//                      echo "<pre>"; print_r($soma_notas); die();
//                  }
//                  $obj_matricula_aux = new clsPmieducarMatricula($ref_cod_matricula, null, null, null, 184580, null, null, $aprovado);
//                  if(!$obj_matricula_aux->edita()) {
//                      die("nao editou aprovacao");
//                  }
//                  die("morreu");
//              }
//              die("aqui");
//          }
//          else 
//          {
//              die("nao exclui");
//          }
        }
    }
}
echo "acabo";

?>