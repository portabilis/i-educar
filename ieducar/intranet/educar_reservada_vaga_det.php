<?php

return new class extends clsDetalhe
{
    /**
     * Referência a usuário da sessão
     *
     * @var int
     */
    public $pessoa_logada;

    /**
     * Título no topo da página
     *
     * @var string
     */
    public $titulo = '';

    // Atributos de mapeamento da tabela pmieducar.reserva_vaga
    public $cod_reserva_vaga;

    public $ref_ref_cod_escola;

    public $ref_ref_cod_serie;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_cod_aluno;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    /**
     * Identificação para pmieducar.escola.
     *
     * @var int
     */
    public $ref_cod_escola;

    /**
     * Identificação para pmieducar.curso.
     *
     * @var int
     */
    public $ref_cod_curso;

    /**
     * Identificação para pmieducar.serie.
     *
     * @var int
     */
    public $ref_cod_serie;

    /**
     * Identificação para pmieducar.instituicao.
     *
     * @var int
     */
    public $ref_cod_instituicao;

    /**
     * Sobrescreve clsDetalhe::Gerar().
     *
     * @see clsDetalhe::Gerar()
     */
    public function Gerar()
    {
        $this->titulo = 'Vagas Reservadas - Detalhe';

        $this->cod_reserva_vaga = $_GET['cod_reserva_vaga'];

        $obj_reserva_vaga = new clsPmieducarReservaVaga();
        $lst_reserva_vaga = $obj_reserva_vaga->lista(int_cod_reserva_vaga: $this->cod_reserva_vaga);

        if (is_array(value: $lst_reserva_vaga)) {
            $registro = array_shift(array: $lst_reserva_vaga);
        }

        if (!$registro) {
            $this->simpleRedirect(url: 'educar_reservada_vaga_lst.php');
        }

        // Atribui códigos a variáveis de instância
        $this->ref_cod_escola = $registro['ref_ref_cod_escola'];
        $this->ref_cod_curso = $registro['ref_cod_curso'];
        $this->ref_cod_serie = $registro['ref_ref_cod_serie'];
        $this->ref_cod_instituicao = $registro['ref_cod_instituicao'];

        // Desativa o pedido de reserva de vaga
        if ($_GET['desativa'] == true) {
            $this->_desativar();
        }

        // Instituição
        $obj_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_cod_instituicao']);
        $det_instituicao = $obj_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $det_instituicao['nm_instituicao'];

        // Escola
        $obj_escola = new clsPmieducarEscola(cod_escola: $registro['ref_ref_cod_escola']);
        $det_escola = $obj_escola->detalhe();
        $registro['ref_ref_cod_escola'] = $det_escola['nome'];

        // Série
        $obj_serie = new clsPmieducarSerie(cod_serie: $registro['ref_ref_cod_serie']);
        $det_serie = $obj_serie->detalhe();
        $registro['ref_ref_cod_serie'] = $det_serie['nm_serie'];

        // Curso
        $obj_curso = new clsPmieducarCurso(cod_curso: $registro['ref_cod_curso']);
        $det_curso = $obj_curso->detalhe();
        $registro['ref_cod_curso'] = $det_curso['nm_curso'];

        if ($registro['nm_aluno']) {
            $nm_aluno = $registro['nm_aluno'] . ' (aluno externo)';
        }

        if ($registro['ref_cod_aluno']) {
            $obj_aluno = new clsPmieducarAluno();
            $lst_aluno = $obj_aluno->lista(
                int_cod_aluno: $registro['ref_cod_aluno'],
                int_ativo: 1
            );

            if (is_array(value: $lst_aluno)) {
                $det_aluno = array_shift(array: $lst_aluno);
                $nm_aluno = $det_aluno['nome_aluno'];
            }
        }

        if ($nm_aluno) {
            $this->addDetalhe(detalhe: ['Aluno', $nm_aluno]);
        }

        if ($this->cod_reserva_vaga) {
            $this->addDetalhe(detalhe: ['N&uacute;mero Reserva Vaga', $this->cod_reserva_vaga]);
        }

        $this->addDetalhe(detalhe: ['-', 'Reserva Pretendida']);

        if ($registro['ref_cod_instituicao']) {
            $this->addDetalhe(detalhe: ['Instituição', $registro['ref_cod_instituicao']]);
        }

        if ($registro['ref_ref_cod_escola']) {
            $this->addDetalhe(detalhe: ['Escola', $registro['ref_ref_cod_escola']]);
        }

        if ($registro['ref_cod_curso']) {
            $this->addDetalhe(detalhe: ['Curso', $registro['ref_cod_curso']]);
        }

        if ($registro['ref_ref_cod_serie']) {
            $this->addDetalhe(detalhe: ['Série', $registro['ref_ref_cod_serie']]);
        }

        $obj_permissao = new clsPermissoes();
        if ($obj_permissao->permissao_cadastra(int_processo_ap: 639, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->array_botao = ['Emissão de Documento de Reserva de Vaga', 'Desativar Reserva'];
            $this->array_botao_url_script = ["showExpansivelImprimir(400, 200,  \"educar_relatorio_solicitacao_transferencia.php?cod_reserva_vaga={$this->cod_reserva_vaga}\",[], \"Relatório de Solicitação de transferência\")", "go(\"educar_reservada_vaga_det.php?cod_reserva_vaga={$this->cod_reserva_vaga}&desativa=true\")"];
        }

        $this->url_cancelar = 'educar_reservada_vaga_lst.php?ref_cod_escola=' .
      $this->ref_cod_escola . '&ref_cod_serie=' . $this->ref_cod_serie;
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe da vaga reservada', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    /**
     * Desativa o pedido de reserva de vaga.
     *
     * @return bool Retorna FALSE em caso de erro
     */
    private function _desativar()
    {
        $obj = new clsPmieducarReservaVaga(
            cod_reserva_vaga: $this->cod_reserva_vaga,
            ref_usuario_exc: $this->pessoa_logada,
            ativo: 0
        );
        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect(url: 'educar_reservada_vaga_lst.php?ref_cod_escola=' .
          $this->ref_cod_escola . '&ref_cod_serie=' . $this->ref_cod_serie);
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Vagas Reservadas';
        $this->processoAp = '639';
    }
};
