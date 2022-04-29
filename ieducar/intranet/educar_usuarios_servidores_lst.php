<?php

use App\Models\LegacyIndividual;

return new class extends clsListagem {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public $nm_servidor;

    public function Gerar()
    {
        $this->titulo = 'Usuários dos servidores - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->campoOculto("pessoaLogada", $this->pessoa_logada);

        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'anoLetivo'], ['required' => false]);
        $this->campoTexto('nm_servidor', 'Nome do servidor', $this->nm_servidor, 52, 255, false);
        $this->campoTexto('matricula_servidor', 'Matrícula', $this->matricula_servidor, 52, 255, false);
        $this->inputsHelper()->dynamic(['funcaoServidor'], ['required' => false]);
        $this->inputsHelper()->dynamic(['escolaridade'], ['required' => false]);
        $this->campoCheck('servidor_com_usuario', 'Incluir servidores com usuário', isset($_GET['servidor_com_usuario']));

        // Paginador
        $this->limite = 100;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        if (!$this->ref_idesco && $_GET['idesco']) {
            $this->ref_idesco = $_GET['idesco'];
        }

        $this->addCabecalhos([
            "<input type='checkbox' id='servidor_usuario_checkbox[]' style='margin-left: 0px;'></input>",
            'Nome do servidor',
            'Matricula',
            'CPF',
            'Ação',
        ]);

        $obj_servidor = new clsPmieducarServidor();
        $obj_servidor->setOrderby('cod_servidor ASC');
        $obj_servidor->setLimite($this->limite, $this->offset);

        $lista = $obj_servidor->lista2(
            $this->ref_cod_instituicao,
            $this->ref_cod_escola,
            $this->ano_letivo,
            $this->nm_servidor,
            $this->matricula_servidor,
            $this->funcao_servidor,
            $this->ref_idesco,
            isset($_GET['servidor_com_usuario']),
            date('Y')
        );
        $total = count($lista);

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $cpf = $registro['cpf'];
                $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
                $cpf = sprintf('%d%d%d.%d%d%d.%d%d%d-%d%d', ...str_split($cpf));
                
                $lista_busca = [];

                $lista_busca[] = "<input type='checkbox' id='servidor_usuario_checkbox[{$registro['cod_servidor']}]' name='servidor_usuario_checkbox[]'></input>";

                $lista_busca[] = "<span>{$registro['nome']}</span>";

                if ($registro['matricula']) {
                    $lista_busca[] = "<span id='servidor_usuario_matricula[{$registro['cod_servidor']}]' name='servidor_usuario_matricula[]'>{$registro['matricula']}</span>";
                } else {
                    $lista_busca[] = "<span id='servidor_usuario_matricula[{$registro['cod_servidor']}]' name='servidor_usuario_matricula[]'>—</span>";
                }

                $lista_busca[] = "<span>{$cpf}</span>";

                if ($registro['matricula']) {
                    if ($registro['ativo'] === 1) {
                        $lista_busca[] = 
                        "
                            <button
                                id='servidor_usuario_btn[{$registro['cod_servidor']}]'
                                name='servidor_usuario_btn[]'
                                style='width: 80px;'
                                class='btn btn-danger'
                                onclick='(function(e){iniciaDesativacaoUsuarioServidor(e, {$registro['cod_servidor']})})(event)'
                            >
                                Desativar
                            </button>
                        ";
                    } else {
                        $lista_busca[] = 
                        "
                            <button
                                id='servidor_usuario_btn[{$registro['cod_servidor']}]'
                                name='servidor_usuario_btn[]'
                                style='width: 80px;'
                                class='btn btn-info'
                                onclick='(function(e){iniciaAtivacaoUsuarioServidor(e, {$registro['cod_servidor']})})(event)'
                            >
                                Ativar
                            </button>
                        ";
                    }
                } else {
                    $lista_busca[] =
                    "
                        <button
                            id='servidor_usuario_btn[{$registro['cod_servidor']}]'
                            name='servidor_usuario_btn[]'
                            style='width: 80px;'
                            class='btn btn-success'
                            onclick='(function(e){iniciaCadastroUsuarioServidor(e, {$registro['cod_servidor']})})(event)'
                        >
                            Gerar senha
                        </button>
                    ";
                }

                $this->addLinhas($lista_busca);
            }
        }

        $this->addPaginador2('educar_usuarios_servidores_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();

        $this->largura = '100%';

        $this->breadcrumb('Listagem dos usuários dos servidores', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        // CASO ALTERE O NOME DOS BOTÕES, DEVE CORRIGIR A LÓGICA EM SERVIDORUSUARIO.JS
        $this->array_botao[] = ['name' => 'Gerar senha(s)', 'css-extra' => 'botoes-selecao-usuarios-servidores'];
        $this->array_botao[] = ['name' => 'Ativar selecionado(s)', 'css-extra' => 'botoes-selecao-usuarios-servidores'];
        $this->array_botao[] = ['name' => 'Desativar selecionado(s)', 'css-extra' => 'botoes-selecao-usuarios-servidores'];
    }

    public function __construct () {
        parent::__construct();
        $this->loadAssets();
    }

    public function loadAssets () {
        $scripts = [
            '/modules/Cadastro/Assets/Javascripts/ServidorUsuario.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    public function Formular()
    {
        $this->title = 'Usuários dos servidores - Listagem';
        $this->processoAp = '58';
    }
};
