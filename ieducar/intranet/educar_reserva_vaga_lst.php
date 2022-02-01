<?php

return new class extends clsListagem {
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

    /**
     * Limite de registros por página
     *
     * @var int
     */
    public $limite = 0;

    /**
     * Início dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset = 0;

    public $ref_cod_escola;
    public $ref_cod_serie;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_ref_cod_serie;
    public $ref_cod_curso;
    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Reserva Vaga - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
      'Série',
      'Curso'
    ];

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Escola';
            $lista_busca[] = 'Instituição';
        } elseif ($nivel_usuario == 2) {
            $lista_busca[] = 'Escola';
        }
        $this->addCabecalhos($lista_busca);

        $get_escola = true;
        $get_curso  = true;
        $get_escola_curso_serie = true;
        include 'include/pmieducar/educar_campo_lista.php';

        // Paginador
        $this->limite = 20;
        $this->offset = $_GET['pagina_' . $this->nome] ?
      $_GET['pagina_' . $this->nome] * $this->limite - $this->limite :
      0;

        $obj_escola_serie = new clsPmieducarEscolaSerie();
        $obj_escola_serie->setLimite($this->limite, $this->offset);

        $lista = $obj_escola_serie->lista(
            $this->ref_cod_escola,
            $this->ref_ref_cod_serie,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            null,
            null,
            null,
            null,
            $this->ref_cod_instituicao,
            $this->ref_cod_curso
        );

        $total = $obj_escola_serie->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_ref_cod_serie = new clsPmieducarSerie($registro['ref_cod_serie']);
                $det_ref_cod_serie = $obj_ref_cod_serie->detalhe();
                $nm_serie = $det_ref_cod_serie['nm_serie'];

                $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
                $det_curso = $obj_curso->detalhe();
                $registro['ref_cod_curso'] = $det_curso['nm_curso'];

                $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
                $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                $nm_escola = $det_ref_cod_escola['nome'];

                $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

                $lista_busca = [
          "<a href=\"educar_reserva_vaga_det.php?ref_cod_escola={$registro['ref_cod_escola']}&ref_cod_serie={$registro['ref_cod_serie']}\">{$nm_serie}</a>",
          "<a href=\"educar_reserva_vaga_det.php?ref_cod_escola={$registro['ref_cod_escola']}&ref_cod_serie={$registro['ref_cod_serie']}\">{$registro['ref_cod_curso']}</a>"
        ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_reserva_vaga_det.php?ref_cod_escola={$registro['ref_cod_escola']}&ref_cod_serie={$registro['ref_cod_serie']}\">{$nm_escola}</a>";
                    $lista_busca[] = "<a href=\"educar_reserva_vaga_det.php?ref_cod_escola={$registro['ref_cod_escola']}&ref_cod_serie={$registro['ref_cod_serie']}\">{$registro['ref_cod_instituicao']}</a>";
                } elseif ($nivel_usuario == 2) {
                    $lista_busca[] = "<a href=\"educar_reserva_vaga_det.php?ref_cod_escola={$registro['ref_cod_escola']}&ref_cod_serie={$registro['ref_cod_serie']}\">{$nm_escola}</a>";
                }
                $this->addLinhas($lista_busca);
            }
        }

        $this->addPaginador2('educar_reserva_vaga_lst.php', $total, $_GET, $this->nome, $this->limite);
        $this->largura = '100%';

        $this->breadcrumb('Listagem de reservas de vaga', [
        url('intranet/educar_index.php') => 'Escola',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Reserva Vaga';
        $this->processoAp = '639';
    }
};

?>

<script type='text/javascript'>
document.getElementById('ref_cod_escola').onchange = function() {
  getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function() {
  getEscolaCursoSerie();
}
</script>
