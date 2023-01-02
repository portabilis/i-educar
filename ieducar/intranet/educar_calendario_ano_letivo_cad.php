<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class extends clsCadastro {
    public $pessoa_logada;
    public $cod_calendario_ano_letivo;
    public $ref_cod_escola;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ano;
    public $data_cadastra;
    public $data_exclusao;
    public $ativo;
    public $inicio_ano_letivo;
    public $termino_ano_letivo;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_calendario_ano_letivo=$_GET['cod_calendario_ano_letivo'];
        $this->ref_cod_escola=$_GET['ref_cod_escola'];
        $this->ref_cod_instituicao=$_GET['ref_cod_instituicao'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 620, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_calendario_ano_letivo_lst.php');

        if (is_numeric(value: $this->cod_calendario_ano_letivo)) {
            $obj = new clsPmieducarCalendarioAnoLetivo(cod_calendario_ano_letivo: $this->cod_calendario_ano_letivo);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $obj_escola = new clsPmieducarEscola(cod_escola: $this->ref_cod_escola);
                $obj_escola->detalhe();

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(int_processo_ap: 620, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_calendario_ano_letivo_det.php?cod_calendario_ano_letivo={$registro['cod_calendario_ano_letivo']}" : 'educar_calendario_ano_letivo_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' calendário do ano letivo', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto(nome: 'cod_calendario_ano_letivo', valor: $this->cod_calendario_ano_letivo);

        if ($_GET) {
            $this->ref_cod_escola=$_GET['ref_cod_escola'];
            $this->ref_cod_instituicao=$_GET['ref_cod_instituicao'];
        }
        $this->inputsHelper()->dynamic(helperNames: ['instituicao', 'escola']);

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_calendario_ano_letivo_det.php?cod_calendario_ano_letivo={$registro['cod_calendario_ano_letivo']}" : 'educar_calendario_ano_letivo_lst.php';

        $ano_array = [ '' => 'Selecione um ano' ];
        if ($this->ref_cod_escola) {
            $obj_anos = new clsPmieducarEscolaAnoLetivo();
            $lista_ano = $obj_anos->lista(int_ref_cod_escola: $this->ref_cod_escola, int_ano: null, int_ref_usuario_cad: null, int_ref_usuario_exc: null, int_andamento: 2, date_data_cadastro_ini: null, date_data_cadastro_fim: null, date_data_exclusao_ini: null, date_data_exclusao_fim: null, int_ativo: 1);
            if ($lista_ano) {
                foreach ($lista_ano as $ano) {
                    $ano_array["{$ano['ano']}"] = $ano['ano'];
                }
            }
        } else {
            $ano_array = [ '' => 'Selecione uma escola' ];
        }

        $this->campoLista(nome: 'ano', campo: 'Ano', valor: $ano_array, default: $this->ano, acao: '', duplo: false);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 620, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_calendario_ano_letivo_lst.php');

        $obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
        $data_inicio = $obj_ano_letivo_modulo->menorData(ref_ano: $this->ano, ref_ref_cod_escola: $this->ref_cod_escola);
        $data_fim = $obj_ano_letivo_modulo->maiorData(ref_ano: $this->ano, ref_ref_cod_escola: $this->ref_cod_escola);

        if ($data_inicio && $data_fim) {
            $obj_calend_ano_letivo = new clsPmieducarCalendarioAnoLetivo();
            $lst_calend_ano_letivo = $obj_calend_ano_letivo->lista(int_cod_calendario_ano_letivo: null, int_ref_cod_escola: $this->ref_cod_escola, int_ref_usuario_exc: null, int_ref_usuario_cad: null, int_ano: $this->ano);
            if ($lst_calend_ano_letivo) {
                $det_calend_ano_letivo = array_shift(array: $lst_calend_ano_letivo);

                $obj_calend_ano_letivo = new clsPmieducarCalendarioAnoLetivo(cod_calendario_ano_letivo: $det_calend_ano_letivo['cod_calendario_ano_letivo'], ref_cod_escola: $this->ref_cod_escola, ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: null, ano: $this->ano, data_cadastra: null, data_exclusao: null, ativo: 1/*, $data_inicio,$data_fim*/);
                if ($obj_calend_ano_letivo->edita()) {
                    $this->mensagem .= 'Edição efetuada com sucesso.<br>';
                    throw new HttpResponseException(
                        response: new RedirectResponse(url: 'educar_calendario_ano_letivo_lst.php')
                    );
                }

                $this->mensagem = 'Edição não realizada.<br>';

                return false;
            } else {
                $obj_calend_ano_letivo = new clsPmieducarCalendarioAnoLetivo(cod_calendario_ano_letivo: null, ref_cod_escola: $this->ref_cod_escola, ref_usuario_exc: null, ref_usuario_cad: $this->pessoa_logada, ano: $this->ano, data_cadastra: null, data_exclusao: null, ativo: 1);
                if ($obj_calend_ano_letivo->cadastra()) {
                    $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
                    throw new HttpResponseException(
                        response: new RedirectResponse(url: "educar_calendario_ano_letivo_lst.php?ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ano={$this->ano}")
                    );
                }

                $this->mensagem = 'Cadastro não realizado.<br>';

                return false;
            }
        }

        echo '<script> alert( \'Não foi possível definir as datas de início e fim do ano letivo.\' ) </script>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 620, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_calendario_ano_letivo_lst.php');

        $obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
        $data_inicio = $obj_ano_letivo_modulo->menorData(ref_ano: $this->ano, ref_ref_cod_escola: $this->ref_cod_escola);
        $data_fim = $obj_ano_letivo_modulo->maiorData(ref_ano: $this->ano, ref_ref_cod_escola: $this->ref_cod_escola);

        if ($data_inicio && $data_fim) {
            $obj_calend_ano_letivo = new clsPmieducarCalendarioAnoLetivo(cod_calendario_ano_letivo: $this->cod_calendario_ano_letivo, ref_cod_escola: $this->ref_cod_escola, ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: null, ano: $this->ano, data_cadastra: null, data_exclusao: null, ativo: 1/*, $data_inicio,$data_fim*/);
            if ($obj_calend_ano_letivo->edita()) {
                $this->mensagem .= 'Edição efetuada com sucesso.<br>';
                throw new HttpResponseException(
                    response: new RedirectResponse(url: 'educar_calendario_ano_letivo_lst.php')
                );
            }

            $this->mensagem = 'Edição não realizada.<br>';

            return false;
        }

        echo '<script> alert( \'Não foi possível definir as datas de início e fim do ano letivo.\' ) </script>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(int_processo_ap: 620, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_calendario_ano_letivo_lst.php');

        $obj = new clsPmieducarCalendarioAnoLetivo(cod_calendario_ano_letivo: $this->cod_calendario_ano_letivo, ref_cod_escola: $this->ref_cod_escola, ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: $this->pessoa_logada, ano: $this->ano, data_cadastra: $this->data_cadastra, data_exclusao: $this->data_exclusao, ativo: 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            throw new HttpResponseException(
                response: new RedirectResponse(url: 'educar_calendario_ano_letivo_lst.php')
            );
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ .'/scripts/extra/educar-calendario-ano-letivo.js');
    }

    public function Formular()
    {
        $this->title = 'Calendario Ano Letivo';
        $this->processoAp = '620';
    }
};
