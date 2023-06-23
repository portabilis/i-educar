<?php

use App\Models\LegacyCalendarDayReason;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class extends clsCadastro
{
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

        $this->cod_calendario_dia_motivo = $_GET['cod_calendario_dia_motivo'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 576, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_calendario_dia_motivo_lst.php');

        if (is_numeric(value: $this->cod_calendario_dia_motivo)) {
            $registro = LegacyCalendarDayReason::find($this->cod_calendario_dia_motivo)->getAttributes();
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
            $obj_calendario_dia_motivo_det = LegacyCalendarDayReason::find($this->cod_calendario_dia_motivo);
            $this->ref_cod_escola = $obj_calendario_dia_motivo_det->school->getKey();
            $this->ref_cod_instituicao = $obj_calendario_dia_motivo_det->school->institution->getKey();
        }

        $this->inputsHelper()->dynamic(helperNames: ['instituicao', 'escola']);
        $this->campoTexto(nome: 'nm_motivo', campo: 'Motivo', valor: $this->nm_motivo, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoTexto(nome: 'sigla', campo: 'Sigla', valor: $this->sigla, tamanhovisivel: 15, tamanhomaximo: 15, obrigatorio: true);
        $this->campoMemo(nome: 'descricao', campo: 'Descricão', valor: $this->descricao, colunas: 60, linhas: 5, obrigatorio: false);

        $opcoes = ['' => 'Selecione', 'e' => 'extra', 'n' => 'não-letivo'];
        $this->campoLista(nome: 'tipo', campo: 'Tipo', valor: $opcoes, default: $this->tipo);
    }

    public function Novo()
    {
        $obj = new LegacyCalendarDayReason();
        $obj->ref_cod_escola = $this->ref_cod_escola;
        $obj->ref_usuario_cad = $this->pessoa_logada;
        $obj->sigla = $this->sigla;
        $obj->descricao = $this->descricao;
        $obj->tipo = $this->tipo;
        $obj->nm_motivo = $this->nm_motivo;

        if ($obj->save()) {
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
        $obj = LegacyCalendarDayReason::find($this->cod_calendario_dia_motivo);
        $obj->sigla = $this->sigla;
        $obj->descricao = $this->descricao;
        $obj->tipo = $this->tipo;
        $obj->nm_motivo = $this->nm_motivo;

        if ($obj->save()) {
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
        $obj = LegacyCalendarDayReason::find($this->cod_calendario_dia_motivo);
        if ($obj->delete()) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            throw new HttpResponseException(
                response: new RedirectResponse(url: 'educar_calendario_dia_motivo_lst.php')
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
