<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once ("include/pmieducar/clsPmieducarEscolaUsuario.inc.php");

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Hist&oacute;rico Escolar" );
        $this->processoAp = "578";
    }
}

class indice extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    var $ref_cod_aluno;
    var $sequencial;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ano;
    var $carga_horaria;
    var $dias_letivos;
    var $escola;
    var $escola_cidade;
    var $escola_uf;
    var $observacao;
    var $aprovado;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    var $ref_cod_instituicao;
    var $nm_serie;
    var $origem;
    var $extra_curricular;
    var $ref_cod_matricula;
    var $frequencia;

    function Gerar()
    {
        $this->titulo = "Hist&oacute;rico Escolar - Detalhe";


        $this->sequencial=$_GET["sequencial"];
        $this->ref_cod_aluno=$_GET["ref_cod_aluno"];

        $tmp_obj = new clsPmieducarHistoricoEscolar( $this->ref_cod_aluno, $this->sequencial );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect("educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
        }

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista( $registro["ref_cod_aluno"],null,null,null,null,null,null,null,null,null,1 );
        if ( is_array($lst_aluno) )
        {
            $det_aluno = array_shift($lst_aluno);
            $nm_aluno = $det_aluno["nome_aluno"];
        }

        if( $nm_aluno )
        {
            $this->addDetalhe( array( "Aluno", "{$nm_aluno}") );
        }

        if($registro["extra_curricular"])
        {
            if( $registro["escola"] )
            {
                $this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["escola"]}") );
            }
            if( $registro["escola_cidade"] )
            {
                $this->addDetalhe( array( "Cidade da Institui&ccedil;&atilde;o", "{$registro["escola_cidade"]}") );
            }
            if( $registro["escola_uf"] )
            {
                $this->addDetalhe( array( "Estado da Institui&ccedil;&atilde;o", "{$registro["escola_uf"]}") );
            }
            if( $registro["nm_serie"] )
            {
                $this->addDetalhe( array( "Série", "{$registro["nm_serie"]}") );
            }
        }
        else
        {
            if( $registro["escola"] )
            {
                $this->addDetalhe( array( "Escola", "{$registro["escola"]}") );
            }
            if( $registro["escola_cidade"] )
            {
                $this->addDetalhe( array( "Cidade da Escola", "{$registro["escola_cidade"]}") );
            }
            if( $registro["escola_uf"] )
            {
                $this->addDetalhe( array( "Estado da Escola", "{$registro["escola_uf"]}") );
            }
            if( $registro["nm_serie"] )
            {
                $this->addDetalhe( array( "S&eacute;rie", "{$registro["nm_serie"]}") );
            }
        }

        if( $registro["nm_curso"] )
        {
            $this->addDetalhe( array( "Curso", "{$registro["nm_curso"]}") );
        }

        if( $registro["ano"] )
        {
            $this->addDetalhe( array( "Ano", "{$registro["ano"]}") );
        }
        if( $registro["carga_horaria"] )
        {
            $registro["carga_horaria"] = str_replace(".",",",$registro["carga_horaria"]);

            $this->addDetalhe( array( "Carga Hor&aacute;ria", "{$registro["carga_horaria"]}") );
        }

        $this->addDetalhe( array( "Faltas globalizadas", is_numeric($registro["faltas_globalizadas"]) ? 'Sim' : 'Não'));

        if( $registro["dias_letivos"] )
        {
            $this->addDetalhe( array( "Dias Letivos", "{$registro["dias_letivos"]}") );
        }
        if( $registro["frequencia"] )
        {
            $this->addDetalhe( array( "Frequência", "{$registro["frequencia"]}") );
        }
        if( $registro["extra_curricular"] )
        {
            $this->addDetalhe( array( "Extra-Curricular", "Sim") );
        }
        else
        {
            $this->addDetalhe( array( "Extra-Curricular", "N&atilde;o") );
        }

    if( $registro["aceleracao"] )
        {
            $this->addDetalhe( array( "Aceleração", "Sim") );
        }
        else
        {
            $this->addDetalhe( array( "Aceleração", "N&atilde;o") );
        }
        if( $registro["origem"] )
        {
            $this->addDetalhe( array( "Origem", "Externo") );
        }
        else
        {
            $this->addDetalhe( array( "Origem", "Interno") );
        }
        if( $registro["observacao"] )
        {
            $this->addDetalhe( array( "Observa&ccedil;&atilde;o", "{$registro["observacao"]}") );
        }
        if( $registro["aprovado"] )
        {
            if ($registro["aprovado"] == 1)
            {
                $registro["aprovado"] = "Aprovado";
            }
            elseif ($registro["aprovado"] == 2)
            {
                $registro["aprovado"] = "Reprovado";
            }
            elseif ($registro["aprovado"] == 3)
            {
                $registro["aprovado"] = "Cursando";
            }
            elseif ($registro["aprovado"] == 4)
            {
                $registro["aprovado"] = "Transferido";
            }
            elseif ($registro["aprovado"] == 5)
            {
                $registro["aprovado"] = "Reclassificado";
            }
            elseif ($registro['aprovado'] == 6) {
                $registro["aprovado"] = "Abandono";
            }
            elseif ($registro['aprovado'] == 12) {
                $registro["aprovado"] = "Aprovado com dependência";
            }
            elseif ($registro['aprovado'] == 13) {
                $registro["aprovado"] = "Aprovado pelo conselho";
            }
            elseif ($registro['aprovado'] == 14) {
                $registro["aprovado"] = "Reprovado por faltas";
            }

            $this->addDetalhe( array( "Situa&ccedil;&atilde;o", "{$registro["aprovado"]}") );
        }

            if( $registro["registro"] )
            {
                $this->addDetalhe( array( "Registro (arquivo)", "{$registro["registro"]}") );
            }

            if( $registro["livro"] )
            {
                $this->addDetalhe( array( "Livro", "{$registro["livro"]}") );
            }

            if( $registro["folha"] )
            {
                $this->addDetalhe( array( "Folha", "{$registro["folha"]}") );
            }

        $obj = new clsPmieducarHistoricoDisciplinas();
        $obj->setOrderby("nm_disciplina ASC");
        $lst = $obj->lista( null,$this->ref_cod_aluno,$this->sequencial );
        $qtd_disciplinas = count($lst);
        if ($lst)
        {
            $tabela = "<table>
                           <tr align='center'>
                               <td bgcolor=#ccdce6><b>Nome</b></td>
                               <td bgcolor=#ccdce6><b>Nota</b></td>
                               <td bgcolor=#ccdce6><b>Faltas</b></td>
                               <td bgcolor=#ccdce6><b>C.H</b></td>
                           </tr>";
            $cont = 0;
            $prim_disciplina = false;
            foreach ( $lst AS $valor )
            {
                if ( ($cont % 2) == 0 )
                {
                    $color = " bgcolor='#f5f9fd' ";
                }
                else
                {
                    $color = " bgcolor='#FFFFFF' ";
                }

                $valor["nm_disciplina"] = urldecode($valor["nm_disciplina"]);

                $tabela .= "<tr>
                                <td {$color} align='left'>{$valor["nm_disciplina"]}</td>
                                <td {$color} align='center'>{$valor["nota"]}</td>";

                if (is_numeric($registro["faltas_globalizadas"]) && !$prim_disciplina)
                    $tabela .= "<td rowspan='{$qtd_disciplinas}' {$color} align='center'>{$registro["faltas_globalizadas"]}</td>";
                else if ( !is_numeric($registro["faltas_globalizadas"]) )
                    $tabela .= "<td {$color} align='center'>{$valor["faltas"]}</td>";

                $tabela .= "<td {$color} align='center'>{$valor["carga_horaria_disciplina"]}</td>";
                $tabela .= "</tr>";

                $registro["faltas_globalizadas"];

                $cont++;
                $prim_disciplina = true;
            }
            $tabela .= "</table>";
        }
        if( $tabela )
        {
            $this->addDetalhe( array( "Disciplina", "{$tabela}") );
        }

        $obj_permissoes = new clsPermissoes();
        $this->obj_permissao = new clsPermissoes();
        $this->nivel_usuario = $this->obj_permissao->nivel_acesso($this->pessoa_logada);
        //$year = date('Y');
        $db = new clsBanco();

        $restringir_historico_escolar = $db->CampoUnico("SELECT restringir_historico_escolar
                                                           FROM pmieducar.instituicao
                                                          WHERE cod_instituicao = (SELECT ref_cod_instituicao
                                                                                     FROM pmieducar.usuario
                                                                                    WHERE cod_usuario = $this->pessoa_logada)");
        if($restringir_historico_escolar){
            $ref_cod_escola = $db->CampoUnico("SELECT ref_cod_escola
                                             FROM pmieducar.historico_escolar
                                            WHERE ref_cod_aluno = $this->ref_cod_aluno
                                              AND sequencial = $this->sequencial");
            //Verifica se a escola foi digitada manualmente no histórico
            if($ref_cod_escola == ''){

                $escolasUsuario = new clsPmieducarEscolaUsuario();
                $escolasUsuario = $escolasUsuario->lista($this->pessoa_logada);

                foreach ($escolasUsuario as $escola) {
                    $idEscolasUsuario[] = $escola['ref_cod_escola'];
                }

                $escola_ultima_matricula = $db->CampoUnico("SELECT ref_ref_cod_escola
                                                              FROM pmieducar.matricula
                                                             WHERE ref_cod_aluno = $this->ref_cod_aluno
                                                          ORDER BY cod_matricula DESC
                                                             LIMIT 1");

                $possuiVinculoComEscolaUltimaMatricula = in_array($escola_ultima_matricula, $idEscolasUsuario);

                if(($possuiVinculoComEscolaUltimaMatricula) || $this->nivel_usuario == 1 || $this->nivel_usuario == 2){
                    if ($registro['origem']) $this->url_editar = "educar_historico_escolar_cad.php?ref_cod_aluno={$registro["ref_cod_aluno"]}&sequencial={$registro["sequencial"]}";
                }
            }
            else{
                $escola_usuario_historico = $db->CampoUnico("SELECT historico_escolar.escola
                                                                                                           FROM pmieducar.historico_escolar
                                                                                                        WHERE historico_escolar.ref_cod_aluno = $this->ref_cod_aluno
                                                                                                          AND historico_escolar.sequencial = $this->sequencial
                                                                                                            AND historico_escolar.escola IN (SELECT relatorio.get_nome_escola(escola_usuario.ref_cod_escola) AS escola_usuario
                                                                                                                                                                                  FROM pmieducar.usuario
                                                                                                                                                                                     INNER JOIN pmieducar.escola_usuario ON (escola_usuario.ref_cod_usuario = usuario.cod_usuario)
                                                                                                                                                                                 WHERE usuario.cod_usuario = $this->pessoa_logada)");
                if($escola_usuario_historico != '' || $this->nivel_usuario == 1 || $this->nivel_usuario == 2){
                    if ($registro['origem']) $this->url_editar = "educar_historico_escolar_cad.php?ref_cod_aluno={$registro["ref_cod_aluno"]}&sequencial={$registro["sequencial"]}";
                }
            }

            if(($escola_usuario == $escola_ultima_matricula || $this->nivel_usuario == 1 || $this->nivel_usuario == 2)){
                $escola_usuario_historico = $db->CampoUnico("SELECT historico_escolar.escola
                                                                                                           FROM pmieducar.historico_escolar
                                                                                                        WHERE historico_escolar.ref_cod_aluno = $this->ref_cod_aluno
                                                                                                          AND historico_escolar.sequencial = $this->sequencial
                                                                                                            AND historico_escolar.escola IN (SELECT relatorio.get_nome_escola(escola_usuario.ref_cod_escola) AS escola_usuario
                                                                                                                                                                                  FROM pmieducar.usuario
                                                                                                                                                                                     INNER JOIN pmieducar.escola_usuario ON (escola_usuario.ref_cod_usuario = usuario.cod_usuario)
                                                                                                                                                                                 WHERE usuario.cod_usuario = $this->pessoa_logada)");
                if($escola_usuario_historico != '' || $this->nivel_usuario == 1 || $this->nivel_usuario == 2){
                    $this->addBotao('Copiar Histórico',"educar_historico_escolar_cad.php?ref_cod_aluno={$registro["ref_cod_aluno"]}&sequencial={$registro["sequencial"]}&copia=true");
                }
            }
        }
        else{
            $this->addBotao('Copiar Histórico',"educar_historico_escolar_cad.php?ref_cod_aluno={$registro["ref_cod_aluno"]}&sequencial={$registro["sequencial"]}&copia=true");
            if ($registro['origem']) $this->url_editar = "educar_historico_escolar_cad.php?ref_cod_aluno={$registro["ref_cod_aluno"]}&sequencial={$registro["sequencial"]}";
        }

        $this->url_cancelar = "educar_historico_escolar_lst.php?ref_cod_aluno={$registro["ref_cod_aluno"]}";
        $this->largura = "100%";

        $this->breadcrumb('Atualização de históricos escolares', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
