<?php

return new class extends clsListagem {
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;

    public $ref_cod_servidor;
    public $ref_cod_funcao;
    public $carga_horaria;
    public $data_cadastro;
    public $data_exclusao;
    public $ref_cod_escola;
    public $ref_cod_instituicao;
    public $ano_letivo;

    public function Gerar()
    {
        $this->titulo = 'Alocação servidor - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $tmp_obj = new clsPmieducarServidor($this->ref_cod_servidor, null, null, null, null, null, null, $this->ref_cod_instituicao);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect('educar_servidor_lst.php');
        }

        $this->addCabecalhos([
            'Escola',
            'Função',
            'Ano',
            'Período',
            'Carga horária',
            'Data admissão',
            'Data saída',
            'Vínculo'
        ]);

        $fisica = new clsPessoaFisica($this->ref_cod_servidor);
        $fisica = $fisica->detalhe();

        $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);
        $this->campoRotulo('nm_servidor', 'Servidor', $fisica['nome']);

        $this->inputsHelper()->dynamic('instituicao', ['required' => false, 'show-select' => true, 'value' => $this->ref_cod_instituicao]);
        $this->inputsHelper()->dynamic('escola', ['required' => false, 'show-select' => true, 'value' => $this->ref_cod_escola]);
        $this->inputsHelper()->dynamic('anoLetivo', ['required' => false, 'show-select' => true, 'value' => $this->ano_letivo]);

        $parametros = new clsParametrosPesquisas();
        $parametros->setSubmit(0);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET['pagina_' . $this->nome]) ?
            $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

        $obj_servidor_alocacao = new clsPmieducarServidorAlocacao();

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $obj_servidor_alocacao->codUsuario = $this->pessoa_logada;
        }

        $obj_servidor_alocacao->setOrderby('ano ASC, data_saida, data_admissao');
        $obj_servidor_alocacao->setLimite($this->limite, $this->offset);

        $lista = $obj_servidor_alocacao->lista(
            null,
            $this->ref_cod_instituicao,
            null,
            null,
            $this->ref_cod_escola,
            $this->ref_cod_servidor,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $this->ano_letivo,
            $this->data_admissao,
            $this->hora_inicial,
            $this->hora_final,
            $this->hora_atividade,
            $this->horas_excedentes,
            $this->data_saida
        );
        $total = $obj_servidor_alocacao->_total;

        // UrlHelper
        $url = CoreExt_View_Helper_UrlHelper::getInstance();

        // Monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $path = 'educar_servidor_alocacao_det.php';
                $options = [
                    'query' => [
                        'cod_servidor_alocacao' => $registro['cod_servidor_alocacao'],
                    ]];

                //Escola
                $escola = new clsPmieducarEscola($registro['ref_cod_escola']);
                $escola = $escola->detalhe();

                //Periodo
                $periodo = [
                    1  => 'Matutino',
                    2  => 'Vespertino',
                    3  => 'Noturno'
                ];

                //Função
                $funcaoServidor = new clsPmieducarServidorFuncao(null, null, null, null, $registro['ref_cod_servidor_funcao']);
                $funcaoServidor = $funcaoServidor->detalhe();

                $funcao = new clsPmieducarFuncao($funcaoServidor['ref_cod_funcao']);
                $funcao = $funcao->detalhe();

                //Vinculo
                $funcionarioVinculo = new clsPortalFuncionario();
                $funcionarioVinculo = $funcionarioVinculo->getNomeVinculo($registro['ref_cod_funcionario_vinculo']);

                $this->addLinhas([
                    $url->l($escola['nome'], $path, $options),
                    $url->l($funcao['nm_funcao'], $path, $options),
                    $url->l($registro['ano'], $path, $options),
                    $url->l($periodo[$registro['periodo']], $path, $options),
                    $url->l($horas =  substr($registro['carga_horaria'], 0, - 3), $path, $options),
                    $url->l(Portabilis_Date_Utils::pgSQLToBr($registro['data_admissao']), $path, $options),
                    $url->l(Portabilis_Date_Utils::pgSQLToBr($registro['data_saida']), $path, $options),
                    $url->l($funcionarioVinculo, $path, $options),
                ]);
            }
        }

        $this->addPaginador2('educar_servidor_alocacao_lst.php', $total, $_GET, $this->nome, $this->limite);

        $obj_permissoes = new clsPermissoes();

        $this->array_botao = [];
        $this->array_botao_url = [];
        if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
            $this->array_botao_url[]= "educar_servidor_alocacao_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";
            $this->array_botao[] = [
                'name' => 'Novo',
                'css-extra' => 'btn-green'
            ];
        }

        $this->array_botao[] = 'Voltar';
        $this->array_botao_url[] = "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";

        $this->largura = '100%';

        $this->breadcrumb('Registro de alocações do servidor', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores - Servidor';
        $this->processoAp = 635;
    }
};
