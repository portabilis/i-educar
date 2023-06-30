<?php

return new class extends clsCadastro
{
    public $pessoa_logada;

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
        $this->ref_cod_serie = $_GET['ref_cod_serie'];
        $this->ref_cod_escola = $_GET['ref_cod_escola'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 639,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_reserva_vaga_lst.php'
        );

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' reserva de vaga', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        if ($this->ref_cod_aluno) {
            $obj_reserva_vaga = new clsPmieducarReservaVaga();
            $lst_reserva_vaga = $obj_reserva_vaga->lista(
                int_ref_cod_aluno: $this->ref_cod_aluno,
                int_ativo: 1
            );

            // Verifica se o aluno já possui reserva alguma reserva ativa no sistema
            if (is_array(value: $lst_reserva_vaga)) {
                echo '
          <script type=\'text/javascript\'>
            alert(\'Aluno já possui reserva de vaga!\\nNão é possivel realizar a reserva.\');
            window.location = \'educar_reserva_vaga_lst.php\';
          </script>';
                exit();
            }

            echo '
        <script type=\'text/javascript\'>
          alert(\'A reserva do aluno permanecerá ativa por apenas 2 dias!\');
        </script>';
        }

        $this->campoOculto(nome: 'ref_cod_serie', valor: $this->ref_cod_serie);
        $this->campoOculto(nome: 'ref_cod_escola', valor: $this->ref_cod_escola);

        $this->nm_aluno = $this->nm_aluno_;

        $this->campoTexto(
            nome: 'nm_aluno',
            campo: 'Aluno',
            valor: $this->nm_aluno,
            tamanhovisivel: 30,
            tamanhomaximo: 255,
            descricao2: "<img border=\"0\" onclick=\"pesquisa_aluno();\" id=\"ref_cod_aluno_lupa\" name=\"ref_cod_aluno_lupa\" src=\"imagens/lupa.png\"\/><span style='padding-left:20px;'><input type='button' value='Aluno externo' onclick='showAlunoExt(true);' class='botaolistagem'></span>",
            evento: '',
            disabled: true
        );

        $this->campoOculto(nome: 'nm_aluno_', valor: $this->nm_aluno_);
        $this->campoOculto(nome: 'ref_cod_aluno', valor: $this->ref_cod_aluno);

        $this->campoOculto(nome: 'tipo_aluno', valor: 'i');

        $this->campoTexto(nome: 'nm_aluno_ext', campo: 'Nome aluno', valor: $this->nm_aluno_ext, tamanhovisivel: 50, tamanhomaximo: 255);
        $this->campoCpf(
            nome: 'cpf_responsavel',
            campo: 'CPF responsável',
            valor: $this->cpf_responsavel,
            descricao: '<span style=\'padding-left:20px;\'><input type=\'button\' value=\'Aluno interno\' onclick=\'showAlunoExt(false);\' class=\'botaolistagem\'></span>'
        );

        $this->campoOculto(nome: 'passo', valor: 1);

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
            ref_ref_cod_escola: $this->ref_cod_escola,
            ref_ref_cod_serie: $this->ref_cod_serie,
            ref_usuario_cad: $this->pessoa_logada,
            ref_cod_aluno: $this->ref_cod_aluno,
            ativo: 1,
            nm_aluno: $this->nm_aluno_ext,
            cpf_responsavel: idFederal2int(str: $this->cpf_responsavel)
        );

        $cadastrou = $obj_reserva_vaga->cadastra();

        if ($cadastrou) {
            $this->mensagem .= 'Reserva de Vaga efetuada com sucesso.<br>';
            $this->simpleRedirect(url: 'educar_reservada_vaga_det.php?cod_reserva_vaga=' . $cadastrou);
        }

        $this->mensagem = 'Reserva de Vaga não realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-reserva-vaga-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Reserva Vaga';
        $this->processoAp = '639';
    }
};
