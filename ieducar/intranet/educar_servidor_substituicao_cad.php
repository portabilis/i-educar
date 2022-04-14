<?php

return new class extends clsCadastro {
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
        $this->ref_cod_servidor        = $_GET['ref_cod_servidor'];
        $this->ref_ref_cod_instituicao = $_GET['ref_cod_instituicao'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            635,
            $this->pessoa_logada,
            3,
            'educar_servidor_lst.php'
        );

        if (is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_ref_cod_instituicao)) {
            $retorno = 'Novo';

            $obj_servidor = new clsPmieducarServidor(
                $this->ref_cod_servidor,
                null,
                null,
                null,
                null,
                null,
                null,
                $this->ref_ref_cod_instituicao
            );
            $det_servidor = $obj_servidor->detalhe();

            // Nenhum servidor com o código de servidor e instituição
            if (!$det_servidor) {
                $this->simpleRedirect('educar_servidor_lst.php');
            }

            $this->professor = $obj_servidor->isProfessor() == true ? 'true' : 'false';

            $obj = new clsPmieducarServidorAlocacao();
            $lista  = $obj->lista(
                null,
                $this->ref_ref_cod_instituicao,
                null,
                null,
                null,
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
                1
            );

            if ($lista) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($lista as $campo => $val) {
                    $temp = [];
                    $temp['carga_horaria']  = $val['carga_horaria'];
                    $temp['periodo']        = $val['periodo'];
                    $temp['ref_cod_escola'] = $val['ref_cod_escola'];

                    $this->alocacao_array[] = $temp;
                }

                $retorno = 'Novo';
            }

            $this->carga_horaria = $det_servidor['carga_horaria'];
        } else {
            $this->simpleRedirect('educar_servidor_lst.php');
        }

        $this->url_cancelar = sprintf(
            'educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor,
            $this->ref_ref_cod_instituicao
        );
        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb('Registro de substituição do servidor', [
        url('intranet/educar_servidores_index.php') => 'Servidores',
    ]);

        return $retorno;
    }

    public function Gerar()
    {
        $obj_inst = new clsPmieducarInstituicao($this->ref_ref_cod_instituicao);
        $inst_det = $obj_inst->detalhe();

        $this->campoRotulo('nm_instituicao', 'Instituição', $inst_det['nm_instituicao']);
        $this->campoOculto('ref_ref_cod_instituicao', $this->ref_ref_cod_instituicao);

        $opcoes = ['' => 'Selecione'];
        $objTemp = new clsPmieducarServidor($this->ref_cod_servidor);
        $det = $objTemp->detalhe();
        if ($det) {
            foreach ($det as $key => $registro) {
                $this->$key =  $registro;
            }
        }

        if ($this->ref_cod_servidor) {
            $objPessoa     = new clsPessoa_($this->ref_cod_servidor);
            $detalhePessoa = $objPessoa->detalhe();
            $nm_servidor = $detalhePessoa['nome'];
        }

        $this->campoRotulo('nm_servidor', 'Servidor', $nm_servidor);

        $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);
        $this->campoOculto('professor', $this->professor);

        $url = sprintf(
            'educar_pesquisa_servidor_lst.php?campo1=ref_cod_servidor_todos_&campo2=ref_cod_servidor_todos&ref_cod_instituicao=%d&ref_cod_servidor=%d&tipo=livre&professor=%d',
            $this->ref_ref_cod_instituicao,
            $this->ref_cod_servidor,
            $this->professor
        );

        $img = sprintf(
            '<img border="0" onclick="pesquisa_valores_popless(\'%s\', \'nome\')" src="imagens/lupaT.png">',
            $url
        );

        $this->campoTextoInv(
            'ref_cod_servidor_todos_',
            'Substituir por:',
            '',
            30,
            255,
            true,
            false,
            false,
            '',
            $img,
            '',
            '',
            ''
        );
        $this->campoOculto('ref_cod_servidor_todos', '');

        $this->campoOculto('alocacao_array', serialize($this->alocacao_array));
        $this->acao_enviar = 'acao2()';
    }

    public function Novo()
    {
        $professor  = isset($_POST['professor']) ? strtolower($_POST['professor']) : 'FALSE';
        $substituto = isset($_POST['ref_cod_servidor_todos']) ? $_POST['ref_cod_servidor_todos'] : null;

        $permissoes = new clsPermissoes();
        $permissoes->permissao_cadastra(
            635,
            $this->pessoa_logada,
            3,
            'educar_servidor_alocacao_lst.php'
        );

        $this->alocacao_array = [];
        if ($_POST['alocacao_array']) {
            $this->alocacao_array = unserialize(urldecode($_POST['alocacao_array']));
        }

        if ($this->alocacao_array) {
            // Substitui todas as alocações
            foreach ($this->alocacao_array as $key => $alocacao) {
                $obj = new clsPmieducarServidorAlocacao(
                    null,
                    $this->ref_ref_cod_instituicao,
                    $this->pessoa_logada,
                    $this->pessoa_logada,
                    $alocacao['ref_cod_escola'],
                    $this->ref_cod_servidor,
                    null,
                    null,
                    null,
                    $alocacao['carga_horaria'],
                    $alocacao['periodo']
                );

                $return = $obj->lista(
                    null,
                    $this->ref_ref_cod_instituicao,
                    null,
                    null,
                    $alocacao['ref_cod_escola'],
                    $this->ref_cod_servidor,
                    null,
                    null,
                    null,
                    null,
                    1,
                    $alocacao['carga_horaria']
                );

                if (false !== $return) {
                    $substituiu = $obj->substituir_servidor($substituto);
                    if (!$substituiu) {
                        $this->mensagem = 'Substituicao n&atilde;o realizado.<br>';

                        return false;
                    }
                }
            }

            // Substituição do servidor no quadro de horários (caso seja professor)
            if ('true' == $professor) {
                $quadroHorarios = new clsPmieducarQuadroHorarioHorarios(
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    $this->ref_ref_cod_instituicao,
                    null,
                    $this->ref_cod_servidor,
                    null,
                    null,
                    null,
                    null,
                    1,
                    null,
                    null
                );
                $quadroHorarios->substituir_servidor($substituto);
            }
        }

        $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
        $destination = 'educar_servidor_det.php?cod_servidor=%s&ref_cod_instituicao=%s';
        $destination = sprintf($destination, $this->ref_cod_servidor, $this->ref_ref_cod_instituicao);
        $this->simpleRedirect($destination);
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
        return file_get_contents(__DIR__ . '/scripts/extra/educar-servidor-substituicao-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Servidores - Servidor Substituição';
        $this->processoAp = 635;
    }
};
