<?php


return new class extends clsListagem
{
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;

    public $cod_serie;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_curso;
    public $nm_serie;
    public $etapa_curso;
    public $concluinte;
    public $carga_horaria;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $intervalo;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'S&eacute;rie - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
      'S&eacute;rie',
      'Curso'
    ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Institui&ccedil;&atilde;o';
        }

        $this->addCabecalhos($lista_busca);

        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'curso']);

        // outros Filtros
        $this->campoTexto('nm_serie', 'Série', $this->nm_serie, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = $_GET["pagina_{$this->nome}"] ?
      $_GET["pagina_{$this->nome}"] * $this->limite-$this->limite : 0;

        $obj_serie = new clsPmieducarSerie();
        $obj_serie->setOrderby('nm_serie ASC');
        $obj_serie->setLimite($this->limite, $this->offset);

        $lista = $obj_serie->lista(
            null,
            null,
            null,
            $this->ref_cod_curso,
            $this->nm_serie,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_instituicao
        );

        $total = $obj_serie->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {

        // Pega detalhes de foreign_keys
                $obj_ref_cod_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
                $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
                $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];

                $obj_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

                $lista_busca = [
          "<a href=\"educar_serie_det.php?cod_serie={$registro['cod_serie']}\">{$registro['nm_serie']}</a>",
          "<a href=\"educar_serie_det.php?cod_serie={$registro['cod_serie']}\">{$registro['ref_cod_curso']}</a>"
        ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_serie_det.php?cod_serie={$registro['cod_serie']}\">{$registro['ref_cod_instituicao']}</a>";
                }

                $this->addLinhas($lista_busca);
            }
        }

        $this->addPaginador2('educar_serie_lst.php', $total, $_GET, $this->nome, $this->limite);

        if ($obj_permissoes->permissao_cadastra(583, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_serie_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de séries', [
        url('intranet/educar_index.php') => 'Escola',
    ]);

    }

    public function Formular()
    {
        $this->title = "i-Educar - S&eacute;rie";
        $this->processoAp = '583';
    }
};


