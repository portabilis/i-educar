<?php

use App\Models\LegacyEducationLevel;
use App\Models\LegacyEducationType;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class extends clsCadastro {
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

        if (is_numeric($this->cod_curso)) {
            $obj = new clsPmieducarCurso($this->cod_curso);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
                $this->data_cadastro = dataFromPgToBr($this->data_cadastro);
                $this->data_exclusao = dataFromPgToBr($this->data_exclusao);

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(int_processo_ap: 0, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 0)) {
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
        $this->campoOculto(nome: 'cod_curso', valor: $this->cod_curso);

        // foreign keys
        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarInstituicao();
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['cod_instituicao']}"] = "{$registro['nm_instituicao']}";
            }
        }

        $this->campoLista(nome: 'ref_cod_instituicao', campo: 'Instituicão', valor: $opcoes, default: $this->ref_cod_instituicao);

        $opcoes = LegacyEducationType::query()
            ->where(column: 'ativo', operator: 1)
            ->orderBy(column: 'nm_tipo', direction: 'ASC')
            ->pluck(column: 'nm_tipo', key: 'cod_tipo_ensino')
            ->prepend(value: 'Selecione', key: '');

        $this->campoLista(nome: 'ref_cod_tipo_ensino', campo: 'Tipo Ensino', valor: $opcoes, default: $this->ref_cod_tipo_ensino);

        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarTipoAvaliacao();
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['cod_tipo_avaliacao']}"] = "{$registro['nm_tipo']}";
            }
        }

        $this->campoLista(nome: 'ref_cod_tipo_avaliacao', campo: 'Tipo Avaliacão', valor: $opcoes, default: $this->ref_cod_tipo_avaliacao);

        $opcoes = LegacyEducationLevel::query()
            ->where(column: 'ativo', operator: 1)
            ->orderBy(column: 'nm_nivel', direction: 'ASC')
            ->pluck(column: 'nm_nivel', key: 'cod_nivel_ensino')
            ->prepend(value: 'Selecione', key: '');

        $this->campoLista(nome: 'ref_cod_nivel_ensino', campo: 'Nivel Ensino', valor: $opcoes, default: $this->ref_cod_nivel_ensino);

        // text
        $this->campoTexto(nome: 'nm_curso', campo: 'Nome Curso', valor: $this->nm_curso, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoTexto(nome: 'sgl_curso', campo: 'Sgl Curso', valor: $this->sgl_curso, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoNumero(nome: 'qtd_etapas', campo: 'Qtd Etapas', valor: $this->qtd_etapas, tamanhovisivel: 15, tamanhomaximo: 255, obrigatorio: true);
        $this->campoMonetario(nome: 'frequencia_minima', campo: 'Frequencia Minima', valor: $this->frequencia_minima, tamanhovisivel: 15, tamanhomaximo: 255, obrigatorio: true);
        $this->campoMonetario(nome: 'media', campo: 'Media', valor: $this->media, tamanhovisivel: 15, tamanhomaximo: 255, obrigatorio: true);
        $this->campoMonetario(nome: 'media_exame', campo: 'Media Exame', valor: $this->media_exame, tamanhovisivel: 15, tamanhomaximo: 255);
        $this->campoNumero(nome: 'falta_ch_globalizada', campo: 'Falta Ch Globalizada', valor: $this->falta_ch_globalizada, tamanhovisivel: 15, tamanhomaximo: 255, obrigatorio: true);
        $this->campoMonetario(nome: 'carga_horaria', campo: 'Carga Horaria', valor: $this->carga_horaria, tamanhovisivel: 15, tamanhomaximo: 255, obrigatorio: true);
        $this->campoTexto(nome: 'ato_poder_publico', campo: 'Ato Poder Publico', valor: $this->ato_poder_publico, tamanhovisivel: 30, tamanhomaximo: 255);
        $this->campoNumero(nome: 'edicao_final', campo: 'Edicão Final', valor: $this->edicao_final, tamanhovisivel: 15, tamanhomaximo: 255, obrigatorio: true);
        $this->campoMemo(nome: 'objetivo_curso', campo: 'Objetivo Curso', valor: $this->objetivo_curso, colunas: 60, linhas: 10);
        $this->campoMemo(nome: 'publico_alvo', campo: 'Publico Alvo', valor: $this->publico_alvo, colunas: 60, linhas: 10);
        $this->campoNumero(nome: 'padrao_ano_escolar', campo: 'Padrão Ano Escolar', valor: $this->padrao_ano_escolar, tamanhovisivel: 15, tamanhomaximo: 255, obrigatorio: true);
        $this->campoMonetario(nome: 'hora_falta', campo: 'Hora Falta', valor: $this->hora_falta, tamanhovisivel: 15, tamanhomaximo: 255, obrigatorio: true);
    }

    public function Novo()
    {

        $obj = new clsPmieducarCurso(cod_curso: $this->cod_curso, ref_usuario_cad: $this->pessoa_logada, ref_cod_tipo_regime: $this->ref_cod_tipo_regime, ref_cod_nivel_ensino: $this->ref_cod_nivel_ensino, ref_cod_tipo_ensino: $this->ref_cod_tipo_ensino, ref_cod_tipo_avaliacao: $this->ref_cod_tipo_avaliacao, nm_curso: $this->nm_curso, sgl_curso: $this->sgl_curso, qtd_etapas: $this->qtd_etapas, frequencia_minima: $this->frequencia_minima, media: $this->media, media_exame: $this->media_exame, falta_ch_globalizada: $this->falta_ch_globalizada, carga_horaria: $this->carga_horaria, ato_poder_publico: $this->ato_poder_publico, edicao_final: $this->edicao_final, objetivo_curso: $this->objetivo_curso, publico_alvo: $this->publico_alvo, data_cadastro: $this->data_cadastro, data_exclusao: $this->data_exclusao, ativo: $this->ativo, ref_usuario_exc: $this->pessoa_logada, ref_cod_instituicao: $this->ref_cod_instituicao, padrao_ano_escolar: $this->padrao_ano_escolar, hora_falta: $this->hora_falta);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';

            throw new HttpResponseException(
                new RedirectResponse('educar_curso_lst.php')
            );
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {

        $obj = new clsPmieducarCurso(cod_curso: $this->cod_curso, ref_usuario_cad: $this->pessoa_logada, ref_cod_tipo_regime: $this->ref_cod_tipo_regime, ref_cod_nivel_ensino: $this->ref_cod_nivel_ensino, ref_cod_tipo_ensino: $this->ref_cod_tipo_ensino, ref_cod_tipo_avaliacao: $this->ref_cod_tipo_avaliacao, nm_curso: $this->nm_curso, sgl_curso: $this->sgl_curso, qtd_etapas: $this->qtd_etapas, frequencia_minima: $this->frequencia_minima, media: $this->media, media_exame: $this->media_exame, falta_ch_globalizada: $this->falta_ch_globalizada, carga_horaria: $this->carga_horaria, ato_poder_publico: $this->ato_poder_publico, edicao_final: $this->edicao_final, objetivo_curso: $this->objetivo_curso, publico_alvo: $this->publico_alvo, data_cadastro: $this->data_cadastro, data_exclusao: $this->data_exclusao, ativo: $this->ativo, ref_usuario_exc: $this->pessoa_logada, ref_cod_instituicao: $this->ref_cod_instituicao, padrao_ano_escolar: $this->padrao_ano_escolar, hora_falta: $this->hora_falta);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';

            throw new HttpResponseException(
                new RedirectResponse('educar_curso_lst.php')
            );
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarCurso(cod_curso: $this->cod_curso, ref_usuario_cad: $this->pessoa_logada, ref_cod_tipo_regime: $this->ref_cod_tipo_regime, ref_cod_nivel_ensino: $this->ref_cod_nivel_ensino, ref_cod_tipo_ensino: $this->ref_cod_tipo_ensino, ref_cod_tipo_avaliacao: $this->ref_cod_tipo_avaliacao, nm_curso: $this->nm_curso, sgl_curso: $this->sgl_curso, qtd_etapas: $this->qtd_etapas, frequencia_minima: $this->frequencia_minima, media: $this->media, media_exame: $this->media_exame, falta_ch_globalizada: $this->falta_ch_globalizada, carga_horaria: $this->carga_horaria, ato_poder_publico: $this->ato_poder_publico, edicao_final: $this->edicao_final, objetivo_curso: $this->objetivo_curso, publico_alvo: $this->publico_alvo, data_cadastro: $this->data_cadastro, data_exclusao: $this->data_exclusao, ativo: 0, ref_usuario_exc: $this->pessoa_logada, ref_cod_instituicao: $this->ref_cod_instituicao, padrao_ano_escolar: $this->padrao_ano_escolar, hora_falta: $this->hora_falta);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';

            throw new HttpResponseException(
                new RedirectResponse('educar_curso_lst.php')
            );
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Curso';
        $this->processoAp = '0';
    }
};
