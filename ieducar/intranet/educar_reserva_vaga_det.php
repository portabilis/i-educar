<?php

return new class extends clsDetalhe {
    /**
     * Referência a usuário da sessão
     *
     * @var int
     */
    public $pessoa_logada = null;

    /**
     * Título no topo da página
     *
     * @var string
     */
    public $titulo = '';

    // Atributos de mapeamento da tabela pmieducar.reserva_vaga
    public $ref_cod_escola;
    public $ref_cod_serie;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Reserva Vaga - Detalhe';

        $this->ref_cod_serie  = $_GET['ref_cod_serie'];
        $this->ref_cod_escola = $_GET['ref_cod_escola'];

        $tmp_obj = new clsPmieducarEscolaSerie();
        $lst_obj = $tmp_obj->lista($this->ref_cod_escola, $this->ref_cod_serie);
        $registro = array_shift($lst_obj);

        if (! $registro) {
            $this->simpleRedirect('educar_reserva_vaga_lst.php');
        }

        // Instituição
        $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

        // Escola
        $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $nm_escola = $det_ref_cod_escola['nome'];

        // Série
        $obj_ref_cod_serie = new clsPmieducarSerie($registro['ref_cod_serie']);
        $det_ref_cod_serie = $obj_ref_cod_serie->detalhe();
        $nm_serie = $det_ref_cod_serie['nm_serie'];

        // Curso
        $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
        $det_curso = $obj_curso->detalhe();
        $registro['ref_cod_curso'] = $det_curso['nm_curso'];

        // Matrícula
        $obj_matricula = new clsPmieducarMatricula();
        $lst_matricula = $obj_matricula->lista(
            null,
            null,
            $registro['ref_cod_escola'],
            $registro['ref_cod_serie'],
            null,
            null,
            null,
            3,
            null,
            null,
            null,
            null,
            1
        );

        if (is_array($lst_matricula)) {
            $matriculados = count($lst_matricula);
        }

        // Detalhes da reserva
        $obj_reserva_vaga = new clsPmieducarReservaVaga();
        $lst_reserva_vaga = $obj_reserva_vaga->lista(
            null,
            $registro['ref_cod_escola'],
            $registro['ref_cod_serie'],
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1
        );

        if (is_array($lst_reserva_vaga)) {
            $reservados = count($lst_reserva_vaga);
        }

        // Permissões
        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe(['Institui&ccedil;&atilde;o', $registro['ref_cod_instituicao']]);
            }
        }

        if ($nivel_usuario == 1 || $nivel_usuario == 2) {
            if ($nm_escola) {
                $this->addDetalhe(['Escola', $nm_escola]);
            }
        }

        if ($registro['ref_cod_curso']) {
            $this->addDetalhe(['Curso', $registro['ref_cod_curso']]);
        }

        if ($nm_serie) {
            $this->addDetalhe(['S&eacute;rie', $nm_serie]);
        }

        $obj_turmas = new clsPmieducarTurma();
        $lst_turmas = $obj_turmas->lista(
            null,
            null,
            null,
            $this->ref_cod_serie,
            $this->ref_cod_escola,
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

        if (is_array($lst_turmas)) {
            $cont = 0;
            $total_vagas = 0;
            $html = '
        <table width=\'50%\' cellspacing=\'0\' cellpadding=\'0\' border=\'0\'>
          <tr>
            <td bgcolor=#ccdce6>Nome</td>
            <td bgcolor=#ccdce6>N&uacute;mero Vagas</td>
          </tr>';

            foreach ($lst_turmas as $turmas) {
                $total_vagas += $turmas['max_aluno'];
                if (($cont % 2) == 0) {
                    $class = ' formmdtd ';
                } else {
                    $class = ' formlttd ';
                }
                $cont++;

                $html .="
          <tr>
            <td class=$class width='35%'>{$turmas['nm_turma']}</td>
            <td class=$class width='15%'>{$turmas['max_aluno']}</td>
          </tr>";
            }

            $html .= '</tr></table>';
            $this->addDetalhe(['Turma', $html]);

            if ($total_vagas) {
                $this->addDetalhe(['Total Vagas', $total_vagas]);
            }

            if ($matriculados) {
                $this->addDetalhe(['Matriculados', $matriculados]);
            }

            if ($reservados) {
                $this->addDetalhe(['Reservados', $reservados]);
            }

            $vagas_restantes = $total_vagas - ($matriculados + $reservados);
            $this->addDetalhe(['Vagas Restantes', $vagas_restantes]);
        }

        if ($obj_permissao->permissao_cadastra(639, $this->pessoa_logada, 7)) {
            $this->array_botao = ['Reservar Vaga', 'Vagas Reservadas'];
            $this->array_botao_url = ["educar_reserva_vaga_cad.php?ref_cod_escola={$registro['ref_cod_escola']}&ref_cod_serie={$registro['ref_cod_serie']}",
        'educar_reservada_vaga_lst.php?ref_cod_escola=' . $registro['ref_cod_escola'] .
        '&ref_cod_serie=' . $registro['ref_cod_serie']];
        }

        $this->url_cancelar = 'educar_reserva_vaga_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da reserva de vaga', [
        url('intranet/educar_index.php') => 'Escola',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Reserva Vaga';
        $this->processoAp = '639';
    }
};
