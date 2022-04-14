<?php

return new class extends clsCadastro {
    /**
     * Referência a usuário da sessão
     *
     * @var int
     */
    public $pessoa_logada = null;

    public $ref_cod_escola;
    public $ref_cod_serie;
    public $ref_cod_aluno;
    public $nm_aluno;
    public $nm_aluno_;

    public $ref_cod_instituicao;
    public $ref_cod_curso;

    public $passo;

    public $nm_aluno_ext;
    public $cpf_responsavel;
    public $tipo_aluno;

    public function Inicializar()
    {
        $retorno = 'Novo';
        $this->ref_cod_serie  = $_GET['ref_cod_serie'];
        $this->ref_cod_escola = $_GET['ref_cod_escola'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            639,
            $this->pessoa_logada,
            7,
            'educar_reserva_vaga_lst.php'
        );

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' reserva de vaga', [
        url('intranet/educar_index.php') => 'Escola',
    ]);

        return $retorno;
    }

    public function Gerar()
    {
        if ($this->ref_cod_aluno) {
            $obj_reserva_vaga = new clsPmieducarReservaVaga();
            $lst_reserva_vaga = $obj_reserva_vaga->lista(
                null,
                null,
                null,
                null,
                null,
                $this->ref_cod_aluno,
                null,
                null,
                null,
                null,
                1
            );

            // Verifica se o aluno já possui reserva alguma reserva ativa no sistema
            if (is_array($lst_reserva_vaga)) {
                echo '
          <script type=\'text/javascript\'>
            alert(\'Aluno já possui reserva de vaga!\\nNão é possivel realizar a reserva.\');
            window.location = \'educar_reserva_vaga_lst.php\';
          </script>';
                die();
            }

            echo '
        <script type=\'text/javascript\'>
          alert(\'A reserva do aluno permanecerá ativa por apenas 2 dias!\');
        </script>';
        }

        $this->campoOculto('ref_cod_serie', $this->ref_cod_serie);
        $this->campoOculto('ref_cod_escola', $this->ref_cod_escola);

        $this->nm_aluno = $this->nm_aluno_;

        $this->campoTexto(
            'nm_aluno',
            'Aluno',
            $this->nm_aluno,
            30,
            255,
            false,
            false,
            false,
            '',
            "<img border=\"0\" class=\"btn\" onclick=\"pesquisa_aluno();\" id=\"ref_cod_aluno_lupa\" name=\"ref_cod_aluno_lupa\" src=\"imagens/lupaT.png\"\/><span style='padding-left:20px;'><input type='button' value='Aluno externo' onclick='showAlunoExt(true);' class='botaolistagem'></span>",
            '',
            '',
            true
        );

        $this->campoOculto('nm_aluno_', $this->nm_aluno_);
        $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);

        $this->campoOculto('tipo_aluno', 'i');

        $this->campoTexto('nm_aluno_ext', 'Nome aluno', $this->nm_aluno_ext, 50, 255, false);
        $this->campoCpf(
            'cpf_responsavel',
            'CPF respons&aacute;vel',
            $this->cpf_responsavel,
            false,
            '<span style=\'padding-left:20px;\'><input type=\'button\' value=\'Aluno interno\' onclick=\'showAlunoExt(false);\' class=\'botaolistagem\'></span>'
        );

        $this->campoOculto('passo', 1);

        $this->acao_enviar = 'acao2()';

        $this->url_cancelar = 'educar_reserva_vaga_lst.php';
        $this->nome_url_cancelar = 'Cancelar';
    }

    public function Novo()
    {
        if ($this->passo == 2) {
            return true;
        }

        $obj_reserva_vaga = new clsPmieducarReservaVaga(
            null,
            $this->ref_cod_escola,
            $this->ref_cod_serie,
            null,
            $this->pessoa_logada,
            $this->ref_cod_aluno,
            null,
            null,
            1,
            $this->nm_aluno_ext,
            idFederal2int($this->cpf_responsavel)
        );

        $cadastrou = $obj_reserva_vaga->cadastra();

        if ($cadastrou) {
            $this->mensagem .= 'Reserva de Vaga efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_reservada_vaga_det.php?cod_reserva_vaga=' . $cadastrou);
        }

        $this->mensagem = 'Reserva de Vaga n&atilde;o realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-reserva-vaga-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Reserva Vaga';
        $this->processoAp = '639';
    }
};
