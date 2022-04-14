<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_curso;
    public $ref_usuario_cad;
    public $ref_cod_tipo_regime;
    public $ref_cod_nivel_ensino;
    public $ref_cod_tipo_ensino;
    public $ref_cod_tipo_avaliacao;
    public $nm_curso;
    public $sgl_curso;
    public $qtd_etapas;
    public $frequencia_minima;
    public $media;
    public $media_exame;
    public $falta_ch_globalizada;
    public $carga_horaria;
    public $ato_poder_publico;
    public $edicao_final;
    public $objetivo_curso;
    public $publico_alvo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_usuario_exc;
    public $ref_cod_instituicao;
    public $padrao_ano_escolar;
    public $hora_falta;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_curso=$_GET['cod_curso'];

        /*$obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 0, $this->pessoa_logada, 0,  "educar_curso_lst.php" );
        */
        if (is_numeric($this->cod_curso)) {
            $obj = new clsPmieducarCurso($this->cod_curso);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
                $this->data_cadastro = dataFromPgToBr($this->data_cadastro);
                $this->data_exclusao = dataFromPgToBr($this->data_exclusao);

                //$obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(0, $this->pessoa_logada, 0)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_curso_det.php?cod_curso={$registro['cod_curso']}" : 'educar_curso_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_curso', $this->cod_curso);

        // foreign keys
        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarInstituicao();
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['cod_instituicao']}"] = "{$registro['nm_instituicao']}";
            }
        }

        $this->campoLista('ref_cod_instituicao', 'Instituic&atilde;o', $opcoes, $this->ref_cod_instituicao);

        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarTipoEnsino();
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['cod_tipo_ensino']}"] = "{$registro['nm_tipo']}";
            }
        }

        $this->campoLista('ref_cod_tipo_ensino', 'Tipo Ensino', $opcoes, $this->ref_cod_tipo_ensino);

        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarTipoAvaliacao();
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['cod_tipo_avaliacao']}"] = "{$registro['nm_tipo']}";
            }
        }

        $this->campoLista('ref_cod_tipo_avaliacao', 'Tipo Avaliac&atilde;o', $opcoes, $this->ref_cod_tipo_avaliacao);

        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarNivelEnsino();
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['cod_nivel_ensino']}"] = "{$registro['nm_nivel']}";
            }
        }

        $this->campoLista('ref_cod_nivel_ensino', 'Nivel Ensino', $opcoes, $this->ref_cod_nivel_ensino);

        // text
        $this->campoTexto('nm_curso', 'Nome Curso', $this->nm_curso, 30, 255, true);
        $this->campoTexto('sgl_curso', 'Sgl Curso', $this->sgl_curso, 30, 255, true);
        $this->campoNumero('qtd_etapas', 'Qtd Etapas', $this->qtd_etapas, 15, 255, true);
        $this->campoMonetario('frequencia_minima', 'Frequencia Minima', $this->frequencia_minima, 15, 255, true);
        $this->campoMonetario('media', 'Media', $this->media, 15, 255, true);
        $this->campoMonetario('media_exame', 'Media Exame', $this->media_exame, 15, 255, false);
        $this->campoNumero('falta_ch_globalizada', 'Falta Ch Globalizada', $this->falta_ch_globalizada, 15, 255, true);
        $this->campoMonetario('carga_horaria', 'Carga Horaria', $this->carga_horaria, 15, 255, true);
        $this->campoTexto('ato_poder_publico', 'Ato Poder Publico', $this->ato_poder_publico, 30, 255, false);
        $this->campoNumero('edicao_final', 'Edic&atilde;o Final', $this->edicao_final, 15, 255, true);
        $this->campoMemo('objetivo_curso', 'Objetivo Curso', $this->objetivo_curso, 60, 10, false);
        $this->campoMemo('publico_alvo', 'Publico Alvo', $this->publico_alvo, 60, 10, false);
        $this->campoNumero('padrao_ano_escolar', 'Padr&atilde;o Ano Escolar', $this->padrao_ano_escolar, 15, 255, true);
        $this->campoMonetario('hora_falta', 'Hora Falta', $this->hora_falta, 15, 255, true);

        // data

        // time

        // bool
    }

    public function Novo()
    {

        /*$obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 0, $this->pessoa_logada, 0,  "educar_curso_lst.php" );
*/

        $obj = new clsPmieducarCurso($this->cod_curso, $this->pessoa_logada, $this->ref_cod_tipo_regime, $this->ref_cod_nivel_ensino, $this->ref_cod_tipo_ensino, $this->ref_cod_tipo_avaliacao, $this->nm_curso, $this->sgl_curso, $this->qtd_etapas, $this->frequencia_minima, $this->media, $this->media_exame, $this->falta_ch_globalizada, $this->carga_horaria, $this->ato_poder_publico, $this->edicao_final, $this->objetivo_curso, $this->publico_alvo, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->pessoa_logada, $this->ref_cod_instituicao, $this->padrao_ano_escolar, $this->hora_falta);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';

            throw new HttpResponseException(
                new RedirectResponse('educar_curso_lst.php')
            );
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {

        /*$obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 0, $this->pessoa_logada, 0,  "educar_curso_lst.php" );
*/

        $obj = new clsPmieducarCurso($this->cod_curso, $this->pessoa_logada, $this->ref_cod_tipo_regime, $this->ref_cod_nivel_ensino, $this->ref_cod_tipo_ensino, $this->ref_cod_tipo_avaliacao, $this->nm_curso, $this->sgl_curso, $this->qtd_etapas, $this->frequencia_minima, $this->media, $this->media_exame, $this->falta_ch_globalizada, $this->carga_horaria, $this->ato_poder_publico, $this->edicao_final, $this->objetivo_curso, $this->publico_alvo, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->pessoa_logada, $this->ref_cod_instituicao, $this->padrao_ano_escolar, $this->hora_falta);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';

            throw new HttpResponseException(
                new RedirectResponse('educar_curso_lst.php')
            );
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarCurso($this->cod_curso, $this->pessoa_logada, $this->ref_cod_tipo_regime, $this->ref_cod_nivel_ensino, $this->ref_cod_tipo_ensino, $this->ref_cod_tipo_avaliacao, $this->nm_curso, $this->sgl_curso, $this->qtd_etapas, $this->frequencia_minima, $this->media, $this->media_exame, $this->falta_ch_globalizada, $this->carga_horaria, $this->ato_poder_publico, $this->edicao_final, $this->objetivo_curso, $this->publico_alvo, $this->data_cadastro, $this->data_exclusao, 0, $this->pessoa_logada, $this->ref_cod_instituicao, $this->padrao_ano_escolar, $this->hora_falta);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';

            throw new HttpResponseException(
                new RedirectResponse('educar_curso_lst.php')
            );
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Curso';
        $this->processoAp = '0';
    }
};
