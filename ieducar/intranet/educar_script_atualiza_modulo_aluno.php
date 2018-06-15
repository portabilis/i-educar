<?php
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

/**
 * busca escolas
 */
//die('arquivo bloqueado');
//$cod_escola = 58;
//$cod_escola = 45;
$cod_escola = 41;
//$cod_escola = 56;
//$cod_escola = 36;
//$cod_escola = 25;
//$cod_escola = 69;
//$cod_escola = 53;
//$cod_escola = 62;
//$cod_escola = 57;
//$cod_escola = 47;

$obj_escola = new clsPmieducarEscola();
$lst_escola = $obj_escola->lista($cod_escola,null,null,1,null,null,null,null,null,null,1);

$db = new clsBanco();

if($lst_escola)
{
    foreach ($lst_escola as $escola)
    {
        /**
         * busca cursos
         */
        $obj_curso = new clsPmieducarEscolaCurso();
        $lst_curso = $obj_curso->lista($escola['cod_escola'],$cod_curso,null,null,null,null,null,null,1);

        if($lst_curso)
        {

            foreach ($lst_curso as $curso)
            {
                /**
                 * busca series
                 */
                $obj_serie = new clsPmieducarEscolaSerie();
                $lst_serie = $obj_serie->lista($escola['cod_escola'],$cod_serie,null,null,null,null,null,null,null,null,null,null,1,null,null,null,null,1,$curso['ref_cod_curso']);

                if ($lst_serie)
                {
                    foreach ($lst_serie as $serie)
                    {
                        /**
                         * busca total de disciplinas da serie
                         */
                        $qtd_disciplinas = $db->CampoUnico("SELECT COUNT(0) FROM pmieducar.escola_serie_disciplina WHERE ref_ref_cod_serie = '{$serie['ref_cod_serie']}' AND ref_ref_cod_escola = '{$escola['cod_escola']}' AND ativo = 1");

                        $obj_matricula = new clsPmieducarMatricula();
                        $lst_matricula = $obj_matricula->lista(null,null,$escola['cod_escola'],$serie['ref_cod_serie'],null,null,null,3,null,null,null,null,1,2007,$curso['ref_cod_curso'],1,1,1,null,null,null);

                        if($lst_matricula)
                        {
                            foreach ($lst_matricula as $matricula)
                            {
                                // verifica se essa eh a ultima nota desse modulo. Se for passa o aluno pro proximo modulo
                                $qtd_dispensas = (int) $db->CampoUnico("SELECT COUNT(0) AS dispensas FROM pmieducar.dispensa_disciplina WHERE ref_cod_matricula = '{$matricula['cod_matricula']}' AND ativo = 1");
                                $qtd_notas = (int)$db->CampoUnico("SELECT COUNT(0) AS notas FROM pmieducar.nota_aluno WHERE ref_cod_matricula = '{$matricula['cod_matricula']}' AND ativo = 1 AND modulo = '1'");

                                /**
                                 * verifica se total de notas + dispensas eh igual ao total de disciplinas da serie
                                 */
                                if( $qtd_dispensas + $qtd_notas >= $qtd_disciplinas )
                                {
                                    $objMatricula = new clsPmieducarMatricula($matricula['cod_matricula'],null,null,null,21317);
                                    $det_matricula = $objMatricula->detalhe();
                                    $max_modulo_nota = (int)$db->CampoUnico("SELECT max(modulo) FROM pmieducar.nota_aluno WHERE ref_cod_matricula = '{$matricula['cod_matricula']}' AND ativo = 1");
                                    /**
                                     * so avança o modulo
                                     * caso ele seja igual ao da maior nota
                                     * e que seja a ultima disciplina
                                     */
                                    if($det_matricula['modulo'] <= $max_modulo_nota)
                                    {
                                        $objMatricula->avancaModulo();

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

echo 'Atualização realizada com sucesso';
?>
