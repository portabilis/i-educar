<?php

use Illuminate\Support\Facades\Session;

return new class extends clsListagem {
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;
    public $cod_servidor;
    public $ref_cod_funcao;
    public $ref_cod_instituicao;
    public $professor;
    public $ref_cod_escola;
    public $nome_servidor;
    public $ref_cod_servidor;
    public $identificador;
    public function Gerar()
    {
        Session::put([
          'campo1' => $_GET['campo1'] ?? Session::get('campo1'),
          'campo2' => $_GET['campo2'] ?? Session::get('campo2'),
          'ref_cod_instituicao' => $_GET['ref_cod_instituicao'] ?? Session::get('ref_cod_instituicao'),
          'ref_cod_escola' => $_GET['ref_cod_escola'] ?? Session::get('ref_cod_escola'),
          'ref_cod_servidor' => $_GET['ref_cod_servidor'] ?? Session::get('ref_cod_servidor'),
          'professor' => $_GET['professor'] ?? Session::get('professor'),
          'identificador' => $_GET['identificador'] ?? Session::get('identificador'),
      ]);

        if (!isset($_GET['tipo'])) {
            Session::forget([
            'setAllField1', 'setAllField2', 'tipo',
        ]);
        }

        $this->ref_cod_instituicao = Session::get('ref_cod_instituicao');
        $this->ref_cod_escola      = Session::get('ref_cod_escola');
        $this->ref_cod_servidor    = Session::get('ref_cod_servidor');
        $this->professor           = Session::get('professor');
        $this->identificador       = Session::get('identificador');

        if (isset($_GET['lst_matriculas']) && Session::has('lst_matriculas')) {
            $this->lst_matriculas = $_GET['lst_matriculas'] ?? Session::get('lst_matriculas');
        }

        Session::put('tipo', $_GET['tipo'] ?? Session::get('tipo'));

        $this->titulo = 'Servidores P&uacute;blicos - Listagem';
        // Passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = $val === '' ? null : $val;
        }
        if (isset($this->lst_matriculas)) {
            $this->lst_matriculas = urldecode($this->lst_matriculas);
        }
        $this->addCabecalhos([
      'Nome do Servidor',
      'Matr&iacute;cula',
      'Institui&ccedil;&atilde;o'
    ]);
        $this->campoTexto('nome_servidor', 'Nome Servidor', $this->nome_servidor, 30, 255, false);
        $this->campoOculto('tipo', $_GET['tipo']);
        $obj_servidor = new clsPmieducarServidor();
        $obj_servidor->setOrderby('nome ASC');

        $lista_professor = false;

        if ($this->ref_cod_instituicao && $this->ref_cod_escola) {
            $lista_professor = $obj_servidor->lista_professor($this->ref_cod_instituicao, $this->ref_cod_escola, $this->nome_servidor);
        }

        // pega detalhes de foreign_keys
        $obj_ref_cod_instituicao = new clsPmieducarInstituicao($lista_professor[0]['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $nm_instituicao = $det_ref_cod_instituicao['nm_instituicao'];

        // monta a lista
        if (is_array($lista_professor) && count($lista_professor)) {
            foreach ($lista_professor as $registro) {
                $campo1 = Session::get('campo1');
                $campo2 = Session::get('campo2');
                $setAll = '';
                if (Session::get('tipo')) {
                    if (is_string($campo1) && is_string($campo2)) {
                        $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}', '{$registro['nome']}'); addVal1('{$campo2}','{$registro['nome']}', '{$registro['cod_servidor']}'); $setAll fecha();\"";
                    } elseif (is_string($campo1)) {
                        $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); $setAll fecha();\"";
                    }
                } else {
                    if (is_string($campo1) && is_string($campo2)) {
                        $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); addVal1('{$campo2}','{$registro['cod_servidor']}','{$registro['nome']}'); $setAll fecha();\"";
                    } elseif (is_string($campo2)) {
                        $script = " onclick=\"addVal1('{$campo2}','{$registro['cod_servidor']}','{$registro['nome']}'); $setAll fecha();\"";
                    } elseif (is_string($campo1)) {
                        $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); $setAll fecha();\"";
                    }
                }
                $this->addLinhas([
          "<a href=\"javascript:void(0);\" $script>{$registro['nome']}</a>",
          "<a href=\"javascript:void(0);\" $script>{$registro['matricula']}</a>",
          "<a href=\"javascript:void(0);\" $script>{$nm_instituicao}</a>"
        ]);
            }
        }
        $this->largura = '100%';
        $obj_permissoes = new clsPermissoes();

        Session::save();
        Session::start();
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-pesquisa-professor-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Servidor';
        $this->processoAp = '0';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
