<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $ref_cod_aluno;
    public $sequencial;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ano;
    public $carga_horaria;
    public $dias_letivos;
    public $escola;
    public $escola_cidade;
    public $escola_uf;
    public $observacao;
    public $aprovado;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public $ref_cod_instituicao;
    public $nm_serie;
    public $origem;
    public $extra_curricular;
    public $ref_cod_matricula;
    public $frequencia;

    public function Gerar()
    {
        $this->titulo = 'Hist&oacute;rico Escolar - Detalhe';

        $this->sequencial=$_GET['sequencial'];
        $this->ref_cod_aluno=$_GET['ref_cod_aluno'];

        $tmp_obj = new clsPmieducarHistoricoEscolar($this->ref_cod_aluno, $this->sequencial);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect("educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
        }

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista($registro['ref_cod_aluno'], null, null, null, null, null, null, null, null, null, 1);
        if (is_array($lst_aluno)) {
            $det_aluno = array_shift($lst_aluno);
            $nm_aluno = $det_aluno['nome_aluno'];
        }

        if ($nm_aluno) {
            $this->addDetalhe([ 'Aluno', "{$nm_aluno}"]);
        }

        if ($registro['extra_curricular']) {
            if ($registro['escola']) {
                $this->addDetalhe([ 'Institui&ccedil;&atilde;o', "{$registro['escola']}"]);
            }
            if ($registro['escola_cidade']) {
                $this->addDetalhe([ 'Cidade da Institui&ccedil;&atilde;o', "{$registro['escola_cidade']}"]);
            }
            if ($registro['escola_uf']) {
                $this->addDetalhe([ 'Estado da Institui&ccedil;&atilde;o', "{$registro['escola_uf']}"]);
            }
            if ($registro['nm_serie']) {
                $this->addDetalhe([ 'Série', "{$registro['nm_serie']}"]);
            }
        } else {
            if ($registro['escola']) {
                $this->addDetalhe([ 'Escola', "{$registro['escola']}"]);
            }
            if ($registro['escola_cidade']) {
                $this->addDetalhe([ 'Cidade da Escola', "{$registro['escola_cidade']}"]);
            }
            if ($registro['escola_uf']) {
                $this->addDetalhe([ 'Estado da Escola', "{$registro['escola_uf']}"]);
            }
            if ($registro['nm_serie']) {
                $this->addDetalhe([ 'S&eacute;rie', "{$registro['nm_serie']}"]);
            }
        }

        if ($registro['nm_curso']) {
            $this->addDetalhe([ 'Curso', "{$registro['nm_curso']}"]);
        }

        if ($registro['ano']) {
            $this->addDetalhe([ 'Ano', "{$registro['ano']}"]);
        }
        if ($registro['carga_horaria']) {
            $registro['carga_horaria'] = str_replace('.', ',', $registro['carga_horaria']);

            $this->addDetalhe([ 'Carga Hor&aacute;ria', "{$registro['carga_horaria']}"]);
        }

        $this->addDetalhe([ 'Faltas globalizadas', is_numeric($registro['faltas_globalizadas']) ? 'Sim' : 'Não']);

        if ($registro['dias_letivos']) {
            $this->addDetalhe([ 'Dias Letivos', "{$registro['dias_letivos']}"]);
        }
        if ($registro['frequencia']) {
            $this->addDetalhe([ 'Frequência', "{$registro['frequencia']}"]);
        }
        if ($registro['extra_curricular']) {
            $this->addDetalhe([ 'Extra-Curricular', 'Sim']);
        } else {
            $this->addDetalhe([ 'Extra-Curricular', 'N&atilde;o']);
        }

        if ($registro['aceleracao']) {
            $this->addDetalhe([ 'Aceleração', 'Sim']);
        } else {
            $this->addDetalhe([ 'Aceleração', 'N&atilde;o']);
        }
        if ($registro['origem']) {
            $this->addDetalhe([ 'Origem', 'Externo']);
        } else {
            $this->addDetalhe([ 'Origem', 'Interno']);
        }
        if ($registro['observacao']) {
            $this->addDetalhe([ 'Observa&ccedil;&atilde;o', "{$registro['observacao']}"]);
        }
        if ($registro['aprovado']) {
            if ($registro['aprovado'] == 1) {
                $registro['aprovado'] = 'Aprovado';
            } elseif ($registro['aprovado'] == 2) {
                $registro['aprovado'] = 'Reprovado';
            } elseif ($registro['aprovado'] == 3) {
                $registro['aprovado'] = 'Cursando';
            } elseif ($registro['aprovado'] == 4) {
                $registro['aprovado'] = 'Transferido';
            } elseif ($registro['aprovado'] == 5) {
                $registro['aprovado'] = 'Reclassificado';
            } elseif ($registro['aprovado'] == 6) {
                $registro['aprovado'] = 'Abandono';
            } elseif ($registro['aprovado'] == 12) {
                $registro['aprovado'] = 'Aprovado com dependência';
            } elseif ($registro['aprovado'] == 13) {
                $registro['aprovado'] = 'Aprovado pelo conselho';
            } elseif ($registro['aprovado'] == 14) {
                $registro['aprovado'] = 'Reprovado por faltas';
            }

            $this->addDetalhe([ 'Situa&ccedil;&atilde;o', "{$registro['aprovado']}"]);
        }

        if ($registro['registro']) {
            $this->addDetalhe([ 'Registro (arquivo)', "{$registro['registro']}"]);
        }

        if ($registro['livro']) {
            $this->addDetalhe([ 'Livro', "{$registro['livro']}"]);
        }

        if ($registro['folha']) {
            $this->addDetalhe([ 'Folha', "{$registro['folha']}"]);
        }

        $obj = new clsPmieducarHistoricoDisciplinas();
        $obj->setOrderby('nm_disciplina ASC');
        $lst = $obj->lista(null, $this->ref_cod_aluno, $this->sequencial);
        $qtd_disciplinas = count($lst);
        if ($lst) {
            $tabela = '<table>
                           <tr align=\'center\'>
                               <td bgcolor=#ccdce6><b>Nome</b></td>
                               <td bgcolor=#ccdce6><b>Nota</b></td>
                               <td bgcolor=#ccdce6><b>Faltas</b></td>
                               <td bgcolor=#ccdce6><b>C.H</b></td>
                           </tr>';
            $cont = 0;
            $prim_disciplina = false;
            foreach ($lst as $valor) {
                if (($cont % 2) == 0) {
                    $color = ' bgcolor=\'#f5f9fd\' ';
                } else {
                    $color = ' bgcolor=\'#FFFFFF\' ';
                }

                $valor['nm_disciplina'] = urldecode($valor['nm_disciplina']);

                $tabela .= "<tr>
                                <td {$color} align='left'>{$valor['nm_disciplina']}</td>
                                <td {$color} align='center'>{$valor['nota']}</td>";

                if (is_numeric($registro['faltas_globalizadas']) && !$prim_disciplina) {
                    $tabela .= "<td rowspan='{$qtd_disciplinas}' {$color} align='center'>{$registro['faltas_globalizadas']}</td>";
                } elseif (!is_numeric($registro['faltas_globalizadas'])) {
                    $tabela .= "<td {$color} align='center'>{$valor['faltas']}</td>";
                }

                $tabela .= "<td {$color} align='center'>{$valor['carga_horaria_disciplina']}</td>";
                $tabela .= '</tr>';

                $registro['faltas_globalizadas'];

                $cont++;
                $prim_disciplina = true;
            }
            $tabela .= '</table>';
        }
        if ($tabela) {
            $this->addDetalhe([ 'Disciplina', "{$tabela}"]);
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
        if ($restringir_historico_escolar) {
            $ref_cod_escola = $db->CampoUnico("SELECT ref_cod_escola
                                             FROM pmieducar.historico_escolar
                                            WHERE ref_cod_aluno = $this->ref_cod_aluno
                                              AND sequencial = $this->sequencial");
            //Verifica se a escola foi digitada manualmente no histórico
            if ($ref_cod_escola == '') {
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

                if (($possuiVinculoComEscolaUltimaMatricula) || $this->nivel_usuario == 1 || $this->nivel_usuario == 2) {
                    if ($registro['origem']) {
                        $this->url_editar = "educar_historico_escolar_cad.php?ref_cod_aluno={$registro['ref_cod_aluno']}&sequencial={$registro['sequencial']}";
                    }
                }
            } else {
                $escola_usuario_historico = $db->CampoUnico("SELECT historico_escolar.escola
                                                                                                           FROM pmieducar.historico_escolar
                                                                                                        WHERE historico_escolar.ref_cod_aluno = $this->ref_cod_aluno
                                                                                                          AND historico_escolar.sequencial = $this->sequencial
                                                                                                            AND historico_escolar.escola IN (SELECT relatorio.get_nome_escola(escola_usuario.ref_cod_escola) AS escola_usuario
                                                                                                                                                                                  FROM pmieducar.usuario
                                                                                                                                                                                     INNER JOIN pmieducar.escola_usuario ON (escola_usuario.ref_cod_usuario = usuario.cod_usuario)
                                                                                                                                                                                 WHERE usuario.cod_usuario = $this->pessoa_logada)");
                if ($escola_usuario_historico != '' || $this->nivel_usuario == 1 || $this->nivel_usuario == 2) {
                    if ($registro['origem']) {
                        $this->url_editar = "educar_historico_escolar_cad.php?ref_cod_aluno={$registro['ref_cod_aluno']}&sequencial={$registro['sequencial']}";
                    }
                }
            }

            if (($escola_usuario == $escola_ultima_matricula || $this->nivel_usuario == 1 || $this->nivel_usuario == 2)) {
                $escola_usuario_historico = $db->CampoUnico("SELECT historico_escolar.escola
                                                                                                           FROM pmieducar.historico_escolar
                                                                                                        WHERE historico_escolar.ref_cod_aluno = $this->ref_cod_aluno
                                                                                                          AND historico_escolar.sequencial = $this->sequencial
                                                                                                            AND historico_escolar.escola IN (SELECT relatorio.get_nome_escola(escola_usuario.ref_cod_escola) AS escola_usuario
                                                                                                                                                                                  FROM pmieducar.usuario
                                                                                                                                                                                     INNER JOIN pmieducar.escola_usuario ON (escola_usuario.ref_cod_usuario = usuario.cod_usuario)
                                                                                                                                                                                 WHERE usuario.cod_usuario = $this->pessoa_logada)");
                if ($escola_usuario_historico != '' || $this->nivel_usuario == 1 || $this->nivel_usuario == 2) {
                    $this->addBotao('Copiar Histórico', "educar_historico_escolar_cad.php?ref_cod_aluno={$registro['ref_cod_aluno']}&sequencial={$registro['sequencial']}&copia=true");
                }
            }
        } else {
            $this->addBotao('Copiar Histórico', "educar_historico_escolar_cad.php?ref_cod_aluno={$registro['ref_cod_aluno']}&sequencial={$registro['sequencial']}&copia=true");
            if ($registro['origem']) {
                $this->url_editar = "educar_historico_escolar_cad.php?ref_cod_aluno={$registro['ref_cod_aluno']}&sequencial={$registro['sequencial']}";
            }
        }

        $this->url_cancelar = "educar_historico_escolar_lst.php?ref_cod_aluno={$registro['ref_cod_aluno']}";
        $this->largura = '100%';

        $this->breadcrumb('Atualização de históricos escolares', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Hist&oacute;rico Escolar';
        $this->processoAp = '578';
    }
};
