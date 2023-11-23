<?php

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $cod_servidor_alocacao;

    public $ref_ref_cod_instituicao;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_cod_escola;

    public $ref_cod_servidor;

    public $dia_semana;

    public $hora_inicial;

    public $hora_final;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $todos;

    public $alocacao_array = [];

    public $professor;

    public function Inicializar()
    {
        $retorno = 'Novo';
        $this->ref_cod_servidor = $_GET['ref_cod_servidor'];
        $this->ref_ref_cod_instituicao = $_GET['ref_cod_instituicao'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 635,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 3,
            str_pagina_redirecionar: 'educar_servidor_lst.php'
        );

        if (is_numeric(value: $this->ref_cod_servidor) && is_numeric(value: $this->ref_ref_cod_instituicao)) {
            $retorno = 'Novo';

            $obj_servidor = new clsPmieducarServidor(
                cod_servidor: $this->ref_cod_servidor,
                ref_cod_instituicao: $this->ref_ref_cod_instituicao
            );
            $det_servidor = $obj_servidor->detalhe();

            // Nenhum servidor com o código de servidor e instituição
            if (!$det_servidor) {
                $this->simpleRedirect(url: 'educar_servidor_lst.php');
            }

            $this->professor = $obj_servidor->isProfessor() == true ? 'true' : 'false';

            $obj = new clsPmieducarServidorAlocacao();
            $lista = $obj->lista(
                int_ref_ref_cod_instituicao: $this->ref_ref_cod_instituicao,
                int_ref_cod_servidor: $this->ref_cod_servidor,
                ano: 1
            );

            if ($lista) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($lista as $val) {
                    $temp = [];
                    $temp['carga_horaria'] = $val['carga_horaria'];
                    $temp['periodo'] = $val['periodo'];
                    $temp['ref_cod_escola'] = $val['ref_cod_escola'];

                    $this->alocacao_array[] = $temp;
                }

                $retorno = 'Novo';
            }

            $this->carga_horaria = $det_servidor['carga_horaria'];
        } else {
            $this->simpleRedirect(url: 'educar_servidor_lst.php');
        }

        $this->url_cancelar = sprintf(
            'educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor,
            $this->ref_ref_cod_instituicao
        );
        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb(currentPage: 'Registro de substituição do servidor', breadcrumbs: [
            url(path: 'intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $obj_inst = new clsPmieducarInstituicao(cod_instituicao: $this->ref_ref_cod_instituicao);
        $inst_det = $obj_inst->detalhe();

        $this->campoRotulo(nome: 'nm_instituicao', campo: 'Instituição', valor: $inst_det['nm_instituicao']);
        $this->campoOculto(nome: 'ref_ref_cod_instituicao', valor: $this->ref_ref_cod_instituicao);

        $objTemp = new clsPmieducarServidor(cod_servidor: $this->ref_cod_servidor);
        $det = $objTemp->detalhe();
        if ($det) {
            foreach ($det as $key => $registro) {
                $this->$key = $registro;
            }
        }

        if ($this->ref_cod_servidor) {
            $objPessoa = new clsPessoa_(int_idpes: $this->ref_cod_servidor);
            $detalhePessoa = $objPessoa->detalhe();
            $nm_servidor = $detalhePessoa['nome'];
        }

        $this->campoRotulo(nome: 'nm_servidor', campo: 'Servidor', valor: $nm_servidor);

        $this->campoOculto(nome: 'ref_cod_servidor', valor: $this->ref_cod_servidor);
        $this->campoOculto(nome: 'professor', valor: $this->professor);

        $url = sprintf(
            'educar_pesquisa_servidor_lst.php?campo1=ref_cod_servidor_todos_&campo2=ref_cod_servidor_todos&ref_cod_instituicao=%d&ref_cod_servidor=%d&professor=%d',
            $this->ref_ref_cod_instituicao,
            $this->ref_cod_servidor,
            $this->professor
        );

        $img = sprintf(
            '<img border="0" onclick="pesquisa_valores_popless(\'%s\', \'nome\')" src="imagens/lupa.png">',
            $url
        );

        $this->campoTextoInv(
            nome: 'ref_cod_servidor_todos_',
            campo: 'Substituir por:',
            valor: '',
            tamanhovisivel: 30,
            tamanhomaximo: 255,
            obrigatorio: true,
            descricao2: $img,
            evento: ''
        );
        $this->campoOculto(nome: 'ref_cod_servidor_todos', valor: '');

        $this->campoOculto(nome: 'alocacao_array', valor: serialize(value: $this->alocacao_array));
        $this->acao_enviar = 'acao2()';
    }

    public function Novo()
    {
        $professor = isset($_POST['professor']) ? strtolower(string: $_POST['professor']) : 'FALSE';
        $substituto = isset($_POST['ref_cod_servidor_todos']) ? $_POST['ref_cod_servidor_todos'] : null;

        $permissoes = new clsPermissoes();
        $permissoes->permissao_cadastra(
            int_processo_ap: 635,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 3,
            str_pagina_redirecionar: 'educar_servidor_alocacao_lst.php'
        );

        $this->alocacao_array = [];
        if ($_POST['alocacao_array']) {
            $this->alocacao_array = unserialize(data: urldecode(string: $_POST['alocacao_array']));
        }

        if ($this->alocacao_array) {
            // Substitui todas as alocações
            foreach ($this->alocacao_array as $alocacao) {
                $obj = new clsPmieducarServidorAlocacao(
                    cod_servidor_alocacao: null,
                    ref_ref_cod_instituicao: $this->ref_ref_cod_instituicao,
                    ref_usuario_exc: $this->pessoa_logada,
                    ref_usuario_cad: $this->pessoa_logada,
                    ref_cod_escola: $alocacao['ref_cod_escola'],
                    ref_cod_servidor: $this->ref_cod_servidor,
                    carga_horaria: $alocacao['carga_horaria'],
                    periodo: $alocacao['periodo']
                );

                $return = $obj->lista(
                    int_ref_ref_cod_instituicao: $this->ref_ref_cod_instituicao,
                    int_ref_cod_escola: $alocacao['ref_cod_escola'],
                    int_ref_cod_servidor: $this->ref_cod_servidor,
                    int_ativo: 1,
                    int_carga_horaria: $alocacao['carga_horaria']
                );

                if ($return !== false) {
                    $substituiu = $obj->substituir_servidor(int_ref_cod_servidor_substituto: $substituto);
                    if (!$substituiu) {
                        $this->mensagem = 'Substituicao não realizado.<br>';

                        return false;
                    }
                }
            }

            // Substituição do servidor no quadro de horários (caso seja professor)
            if ($professor == 'true') {
                $quadroHorarios = new clsPmieducarQuadroHorarioHorarios(
                    ref_cod_instituicao_servidor: $this->ref_ref_cod_instituicao,
                    ref_servidor: $this->ref_cod_servidor,
                    ativo: 1,
                );
                $quadroHorarios->substituir_servidor(int_ref_cod_servidor_substituto: $substituto);
            }
        }

        $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
        $destination = 'educar_servidor_det.php?cod_servidor=%s&ref_cod_instituicao=%s';
        $destination = sprintf($destination, $this->ref_cod_servidor, $this->ref_ref_cod_instituicao);
        $this->simpleRedirect(url: $destination);
    }

    public function Editar()
    {
        return false;
    }

    public function Excluir()
    {
        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-servidor-substituicao-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Servidores - Servidor Substituição';
        $this->processoAp = 635;
    }
};
