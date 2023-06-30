<?php

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $ref_cod_instituicao;

    public $ref_cod_escola;

    public $ano;

    public $mes;

    public $nm_escola;

    public $nm_instituicao;

    public $ref_cod_curso;

    public $sequencial;

    public $pdf;

    public $pagina_atual = 1;

    public $total_paginas = 1;

    public $page_y = 125;

    public $cursos = [];

    public $array_disciplinas = [];

    public $get_link;

    public $ref_cod_modulo;

    /**
     * @var string[]
     */
    public $total_dias_uteis;

    public $meses_do_ano = [
        '1' => 'JANEIRO',
        '2' => 'FEVEREIRO',
        '3' => 'MARÇO',
        '4' => 'ABRIL',
        '5' => 'MAIO',
        '6' => 'JUNHO',
        '7' => 'JULHO',
        '8' => 'AGOSTO',
        '9' => 'SETEMBRO',
        '10' => 'OUTUBRO',
        '11' => 'NOVEMBRO',
        '12' => 'DEZEMBRO',
    ];

    public function Inicializar()
    {
        $retorno = 'Novo';

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->nivel_acesso(int_idpes_usuario: $this->pessoa_logada) > 7) {
            $this->simpleRedirect(url: 'index.php');
        }

        return $retorno;
    }

    public function Gerar()
    {
        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);

        if ($_POST) {
            foreach ($_POST as $key => $value) {
                $this->$key = $value;
            }
        }

        $this->ano = $ano_atual = date(format: 'Y');
        $this->mes = $mes_atual = date(format: 'n');

        $this->campoLista(nome: 'mes', campo: 'Mês', valor: $this->meses_do_ano, default: $this->mes);

        $this->campoNumero(nome: 'ano', campo: 'Ano', valor: $this->ano, tamanhovisivel: 4, tamanhomaximo: 4, obrigatorio: true);

        $get_escola = true;
        $obrigatorio = true;
        $exibe_nm_escola = true;

        $this->ref_cod_escola = $obj_permissoes->getEscola(int_idpes_usuario: $this->pessoa_logada);
        $this->ref_cod_instituicao = $obj_permissoes->getInstituicao(int_idpes_usuario: $this->pessoa_logada);
        include 'include/pmieducar/educar_campo_lista.php';
        $this->campoRotulo(nome: 'cursos_', campo: 'Cursos', valor: '<div id=\'cursos\'>Selecione uma escola</div>');

        if ($nivel_usuario <= 3) {
            echo '<script>
                    window.onload = function(){document.getElementById(\'ref_cod_escola\').onchange = changeCurso};
                  </script>';
        } else {
            echo '<script>
                    window.onload = function(){ changeCurso() };
                  </script>';
        }

        $this->url_cancelar = 'educar_index.php';
        $this->nome_url_cancelar = 'Cancelar';

        $this->acao_enviar = 'acao2()';
        $this->acao_executa_submit = false;
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-alunos-defasados.js');
    }

    public function Formular()
    {
        $this->title = 'Movimentação Mensal de Alunos';
        $this->processoAp = '944';
    }
};
