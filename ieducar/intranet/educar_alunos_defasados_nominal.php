<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
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

    public $meses_do_ano = [
                             '1' => 'JANEIRO'
                            ,'2' => 'FEVEREIRO'
                            ,'3' => 'MAR&Ccedil;O'
                            ,'4' => 'ABRIL'
                            ,'5' => 'MAIO'
                            ,'6' => 'JUNHO'
                            ,'7' => 'JULHO'
                            ,'8' => 'AGOSTO'
                            ,'9' => 'SETEMBRO'
                            ,'10' => 'OUTUBRO'
                            ,'11' => 'NOVEMBRO'
                            ,'12' => 'DEZEMBRO'
                        ];

    public $total_dias_uteis;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->nivel_acesso($this->pessoa_logada) > 7) {
            $this->simpleRedirect('index.php');
        }

        return $retorno;
    }

    public function Gerar()
    {
        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if ($_POST) {
            foreach ($_POST as $key => $value) {
                $this->$key = $value;
            }
        }

        $this->ano = $ano_atual = date('Y');
        $this->mes = $mes_atual = date('n');

        $this->campoLista('mes', 'M&ecirc;s', $this->meses_do_ano, $this->mes, '', false);
//      $this->campoLista( "ano", "Ano",$anos, $this->ano,"",false );

        $this->campoNumero('ano', 'Ano', $this->ano, 4, 4, true);

        $get_escola = true;
        $obrigatorio = true;
        $exibe_nm_escola = true;

        $this->ref_cod_escola = $obj_permissoes->getEscola($this->pessoa_logada);
        $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
        include('include/pmieducar/educar_campo_lista.php');
        $this->campoRotulo('cursos_', 'Cursos', '<div id=\'cursos\'>Selecione uma escola</div>');

        if ($nivel_usuario <=3) {
            echo '<script>
                    window.onload = function(){document.getElementById(\'ref_cod_escola\').onchange = changeCurso};
                  </script>';
        } else {
            echo '<script>
                    window.onload = function(){ changeCurso() };
                  </script>';
        }

        //  if($this->get_link)
        //  $this->campoRotulo("rotulo11", "-", "<a href='$this->get_link' target='_blank'>Baixar Relatório</a>");

        $this->url_cancelar = 'educar_index.php';
        $this->nome_url_cancelar = 'Cancelar';

        $this->acao_enviar = 'acao2()';
        $this->acao_executa_submit = false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-alunos-defasados.js');
    }

    public function Formular()
    {
        $this->title = 'Movimentação Mensal de Alunos';
        $this->processoAp = '944';
    }
};
