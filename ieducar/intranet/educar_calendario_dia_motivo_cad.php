<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class extends clsCadastro {
    public $pessoa_logada;
    public $cod_calendario_dia_motivo;
    public $ref_cod_escola;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $sigla;
    public $descricao;
    public $tipo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $nm_motivo;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_calendario_dia_motivo=$_GET['cod_calendario_dia_motivo'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 576, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_calendario_dia_motivo_lst.php');

        if (is_numeric(value: $this->cod_calendario_dia_motivo)) {
            $obj = new clsPmieducarCalendarioDiaMotivo(cod_calendario_dia_motivo: $this->cod_calendario_dia_motivo);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(int_processo_ap: 576, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7);
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo={$registro['cod_calendario_dia_motivo']}" : 'educar_calendario_dia_motivo_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' motivo de dias do calendário', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto(nome: 'cod_calendario_dia_motivo', valor: $this->cod_calendario_dia_motivo);

        if ($this->cod_calendario_dia_motivo) {
            $obj_calendario_dia_motivo = new clsPmieducarCalendarioDiaMotivo(cod_calendario_dia_motivo: $this->cod_calendario_dia_motivo);
            $obj_calendario_dia_motivo_det = $obj_calendario_dia_motivo->detalhe();
            $this->ref_cod_escola = $obj_calendario_dia_motivo_det['ref_cod_escola'];
            $obj_ref_cod_escola = new clsPmieducarEscola(cod_escola: $this->ref_cod_escola);
            $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
            $this->ref_cod_instituicao = $det_ref_cod_escola['ref_cod_instituicao'];
        }

        $this->inputsHelper()->dynamic(helperNames: ['instituicao','escola']);
        $this->campoTexto(nome: 'nm_motivo', campo: 'Motivo', valor: $this->nm_motivo, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoTexto(nome: 'sigla', campo: 'Sigla', valor: $this->sigla, tamanhovisivel: 15, tamanhomaximo: 15, obrigatorio: true);
        $this->campoMemo(nome: 'descricao', campo: 'Descricão', valor: $this->descricao, colunas: 60, linhas: 5, obrigatorio: false);

        $opcoes = [ '' => 'Selecione', 'e' => 'extra', 'n' => 'não-letivo' ];
        $this->campoLista(nome: 'tipo', campo: 'Tipo', valor: $opcoes, default: $this->tipo);
    }

    public function Novo()
    {
        $obj = new clsPmieducarCalendarioDiaMotivo(ref_cod_escola: $this->ref_cod_escola, ref_usuario_cad: $this->pessoa_logada, sigla: $this->sigla, descricao: $this->descricao, tipo: $this->tipo, ativo: 1, nm_motivo: $this->nm_motivo);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            throw new HttpResponseException(
                response: new RedirectResponse(url: 'educar_calendario_dia_motivo_lst.php')
            );
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj = new clsPmieducarCalendarioDiaMotivo(cod_calendario_dia_motivo: $this->cod_calendario_dia_motivo, ref_cod_escola: $this->ref_cod_escola, ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: null, sigla: $this->sigla, descricao: $this->descricao, tipo: $this->tipo, data_cadastro: null, data_exclusao: null, ativo: 1, nm_motivo: $this->nm_motivo);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';

            throw new HttpResponseException(
                response: new RedirectResponse(url: 'educar_calendario_dia_motivo_lst.php')
            );
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarCalendarioDiaMotivo(cod_calendario_dia_motivo: $this->cod_calendario_dia_motivo, ref_usuario_exc: $this->pessoa_logada, ativo: 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            throw new HttpResponseException(
                response: new RedirectResponse(url: 'educar_calendario_dia_motivo_lst')
            );
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Calendário Dia Motivo';
        $this->processoAp = '576';
    }
};
