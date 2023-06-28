<?php

use Illuminate\Support\Facades\Session;

return new class extends clsListagem
{
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
        Session::put(key: [
            'campo1' => $_GET['campo1'] ?? Session::get(key: 'campo1'),
            'campo2' => $_GET['campo2'] ?? Session::get(key: 'campo2'),
            'ref_cod_instituicao' => $_GET['ref_cod_instituicao'] ?? Session::get(key: 'ref_cod_instituicao'),
            'ref_cod_escola' => $_GET['ref_cod_escola'] ?? Session::get(key: 'ref_cod_escola'),
            'ref_cod_servidor' => $_GET['ref_cod_servidor'] ?? Session::get(key: 'ref_cod_servidor'),
            'professor' => $_GET['professor'] ?? Session::get(key: 'professor'),
            'identificador' => $_GET['identificador'] ?? Session::get(key: 'identificador'),
        ]);

        if (!isset($_GET['tipo'])) {
            Session::forget(keys: [
                'setAllField1',
                'setAllField2',
                'tipo',
            ]);
        }

        $this->ref_cod_instituicao = Session::get(key: 'ref_cod_instituicao');
        $this->ref_cod_escola = Session::get(key: 'ref_cod_escola');
        $this->ref_cod_servidor = Session::get(key: 'ref_cod_servidor');
        $this->professor = Session::get(key: 'professor');
        $this->identificador = Session::get(key: 'identificador');

        if (isset($_GET['lst_matriculas']) && Session::has(key: 'lst_matriculas')) {
            $this->lst_matriculas = $_GET['lst_matriculas'] ?? Session::get(key: 'lst_matriculas');
        }

        Session::put(key: 'tipo', value: $_GET['tipo'] ?? Session::get(key: 'tipo'));

        $this->titulo = 'Servidores P&uacute;blicos - Listagem';
        // Passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = $val === '' ? null : $val;
        }
        if (isset($this->lst_matriculas)) {
            $this->lst_matriculas = urldecode(string: $this->lst_matriculas);
        }
        $this->addCabecalhos(coluna: [
            'Nome do Servidor',
            'Matrícula',
            'Instituição',
        ]);
        $this->campoTexto(nome: 'nome_servidor', campo: 'Nome Servidor', valor: $this->nome_servidor, tamanhovisivel: 30, tamanhomaximo: 255);
        $this->campoOculto(nome: 'tipo', valor: $_GET['tipo']);
        $obj_servidor = new clsPmieducarServidor();
        $obj_servidor->setOrderby(strNomeCampo: 'nome ASC');

        $lista_professor = false;

        if ($this->ref_cod_instituicao && $this->ref_cod_escola) {
            $lista_professor = $obj_servidor->lista_professor(cod_instituicao: $this->ref_cod_instituicao, cod_escola: $this->ref_cod_escola, str_nome_servidor: $this->nome_servidor);
        }

        // pega detalhes de foreign_keys
        $obj_ref_cod_instituicao = new clsPmieducarInstituicao(cod_instituicao: $lista_professor[0]['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $nm_instituicao = $det_ref_cod_instituicao['nm_instituicao'];

        // monta a lista
        if (is_array(value: $lista_professor) && count(value: $lista_professor)) {
            foreach ($lista_professor as $registro) {
                $campo1 = Session::get(key: 'campo1');
                $campo2 = Session::get(key: 'campo2');
                $setAll = '';
                if (Session::get(key: 'tipo')) {
                    if (is_string(value: $campo1) && is_string(value: $campo2)) {
                        $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}', '{$registro['nome']}'); addVal1('{$campo2}','{$registro['nome']}', '{$registro['cod_servidor']}'); $setAll fecha();\"";
                    } elseif (is_string(value: $campo1)) {
                        $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); $setAll fecha();\"";
                    }
                } else {
                    if (is_string(value: $campo1) && is_string(value: $campo2)) {
                        $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); addVal1('{$campo2}','{$registro['cod_servidor']}','{$registro['nome']}'); $setAll fecha();\"";
                    } elseif (is_string(value: $campo2)) {
                        $script = " onclick=\"addVal1('{$campo2}','{$registro['cod_servidor']}','{$registro['nome']}'); $setAll fecha();\"";
                    } elseif (is_string(value: $campo1)) {
                        $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); $setAll fecha();\"";
                    }
                }
                $this->addLinhas(linha: [
                    "<a href=\"javascript:void(0);\" $script>{$registro['nome']}</a>",
                    "<a href=\"javascript:void(0);\" $script>{$registro['matricula']}</a>",
                    "<a href=\"javascript:void(0);\" $script>{$nm_instituicao}</a>",
                ]);
            }
        }
        $this->largura = '100%';

        Session::save();
        Session::start();
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-pesquisa-professor-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Servidor';
        $this->processoAp = '0';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
