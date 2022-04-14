<?php

return new class extends clsDetalhe {
    public $titulo;

    public $cod_curso;
    public $ref_usuario_cad;
    public $ref_cod_tipo_regime;
    public $ref_cod_nivel_ensino;
    public $ref_cod_tipo_ensino;
    public $nm_curso;
    public $sgl_curso;
    public $qtd_etapas;
    public $carga_horaria;
    public $ato_poder_publico;
    public $habilitacao;
    public $objetivo_curso;
    public $publico_alvo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_usuario_exc;
    public $ref_cod_instituicao;
    public $padrao_ano_escolar;
    public $hora_falta;

    public function Gerar()
    {
        $this->titulo = 'Curso - Detalhe';

        $this->cod_curso = $_GET['cod_curso'];

        $tmp_obj = new clsPmieducarCurso($this->cod_curso);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect('educar_curso_lst.php');
        }

        $obj_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $obj_instituicao_det = $obj_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $obj_instituicao_det['nm_instituicao'];

        $obj_ref_cod_tipo_regime = new clsPmieducarTipoRegime($registro['ref_cod_tipo_regime']);
        $det_ref_cod_tipo_regime = $obj_ref_cod_tipo_regime->detalhe();
        $registro['ref_cod_tipo_regime'] = $det_ref_cod_tipo_regime['nm_tipo'];

        $obj_ref_cod_nivel_ensino = new clsPmieducarNivelEnsino($registro['ref_cod_nivel_ensino']);
        $det_ref_cod_nivel_ensino = $obj_ref_cod_nivel_ensino->detalhe();
        $registro['ref_cod_nivel_ensino'] = $det_ref_cod_nivel_ensino['nm_nivel'];

        $obj_ref_cod_tipo_ensino = new clsPmieducarTipoEnsino($registro['ref_cod_tipo_ensino']);
        $det_ref_cod_tipo_ensino = $obj_ref_cod_tipo_ensino->detalhe();
        $registro['ref_cod_tipo_ensino'] = $det_ref_cod_tipo_ensino['nm_tipo'];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe(['Institui&ccedil;&atilde;o', $registro['ref_cod_instituicao']]);
            }
        }

        if ($registro['ref_cod_tipo_regime']) {
            $this->addDetalhe(['Tipo Regime', $registro['ref_cod_tipo_regime']]);
        }

        if ($registro['ref_cod_nivel_ensino']) {
            $this->addDetalhe(['N&iacute;vel Ensino', $registro['ref_cod_nivel_ensino']]);
        }

        if ($registro['ref_cod_tipo_ensino']) {
            $this->addDetalhe(['Tipo Ensino', $registro['ref_cod_tipo_ensino']]);
        }

        if ($registro['nm_curso']) {
            $this->addDetalhe(['Curso', $registro['nm_curso']]);
        }

        if ($registro['sgl_curso']) {
            $this->addDetalhe(['Sigla Curso', $registro['sgl_curso']]);
        }

        if ($registro['qtd_etapas']) {
            $this->addDetalhe(['Quantidade Etapas', $registro['qtd_etapas']]);
        }

        if ($registro['hora_falta']) {
            $registro['hora_falta'] = number_format($registro['hora_falta'], 2, ',', '.');
            $this->addDetalhe(['Hora/Falta', $registro['hora_falta']]);
        }

        if ($registro['carga_horaria']) {
            $registro['carga_horaria'] = number_format($registro['carga_horaria'], 2, ',', '.');
            $this->addDetalhe(['Carga Hor&aacute;ria', $registro['carga_horaria']]);
        }

        if ($registro['ato_poder_publico']) {
            $this->addDetalhe(['Ato Poder P&uacute;blico', $registro['ato_poder_publico']]);
        }

        $obj = new clsPmieducarHabilitacaoCurso(null, $this->cod_curso);
        $lst = $obj->lista(null, $this->cod_curso);

        if ($lst) {
            $tabela = '<TABLE>
                 <TR align=center>
                     <TD bgcolor=#ccdce6><B>Nome</B></TD>
                 </TR>';
            $cont = 0;

            foreach ($lst as $valor) {
                if (($cont % 2) == 0) {
                    $color = ' bgcolor=#f5f9fd ';
                } else {
                    $color = ' bgcolor=#FFFFFF ';
                }

                $obj = new clsPmieducarHabilitacao($valor['ref_cod_habilitacao']);
                $obj_habilitacao = $obj->detalhe();
                $habilitacao = $obj_habilitacao['nm_tipo'];

                $tabela .= "<TR>
                  <TD {$color} align=left>{$habilitacao}</TD>
              </TR>";

                $cont++;
            }
            $tabela .= '</TABLE>';
        }

        if ($habilitacao) {
            $this->addDetalhe(['Habilita&ccedil;&atilde;o', $tabela]);
        }

        if ($registro['padrao_ano_escolar']) {
            if ($registro['padrao_ano_escolar'] == 0) {
                $registro['padrao_ano_escolar'] = 'n&atilde;o';
            } elseif ($registro['padrao_ano_escolar'] == 1) {
                $registro['padrao_ano_escolar'] = 'sim';
            }

            $this->addDetalhe(['Padr&atilde;o Ano Escolar', $registro['padrao_ano_escolar']]);
        }

        if ($registro['objetivo_curso']) {
            $this->addDetalhe(['Objetivo Curso', $registro['objetivo_curso']]);
        }

        if ($registro['publico_alvo']) {
            $this->addDetalhe(['P&uacute;blico Alvo', $registro['publico_alvo']]);
        }

        if ($obj_permissoes->permissao_cadastra(566, $this->pessoa_logada, 3)) {
            $this->url_novo = 'educar_curso_cad.php';
            $this->url_editar = "educar_curso_cad.php?cod_curso={$registro['cod_curso']}";
        }

        $this->url_cancelar = 'educar_curso_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do curso', [
        url('intranet/educar_index.php') => 'Escola',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Curso';
        $this->processoAp = '566';
    }
};
