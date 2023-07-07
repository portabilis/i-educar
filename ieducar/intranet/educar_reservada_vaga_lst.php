<?php

return new class extends clsListagem
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
     * Atributos para apresentação
     *
     * @var mixed
     */
    public $ref_cod_escola;

    public $ref_cod_curso;

    public $ref_cod_instituicao;

    public $nm_aluno;

    /**
     * Sobrescreve clsListagem::Gerar().
     *
     * @see clsListagem::Gerar()
     */
    public function Gerar()
    {
        $this->titulo = 'Vagas Reservadas - Listagem';

        // Passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
            'Aluno',
            'Série',
            'Curso',
        ];

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);

        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Escola';
            $lista_busca[] = 'Instituição';
        } elseif ($nivel_usuario == 2) {
            $lista_busca[] = 'Escola';
        }
        $this->addCabecalhos(coluna: $lista_busca);

        // Lista de opçõees para o formulário de pesquisa rápida
        $get_escola = true;
        $get_curso = true;
        $get_escola_curso_serie = true;
        include 'include/pmieducar/educar_campo_lista.php';

        // Referência de escola
        if ($this->ref_cod_escola) {
            $this->ref_ref_cod_escola = $this->ref_cod_escola;
        } elseif (isset($_GET['ref_cod_escola'])) {
            $this->ref_ref_cod_escola = intval(value: $_GET['ref_cod_escola']);
        }

        // Referência de série
        if ($this->ref_cod_serie) {
            $this->ref_ref_cod_serie = $this->ref_cod_serie;
        } elseif (isset($_GET['ref_cod_serie'])) {
            $this->ref_ref_cod_serie = intval(value: $_GET['ref_cod_serie']);
        }

        // Campos do formulário
        $this->campoTexto(
            nome: 'nm_aluno',
            campo: 'Aluno',
            valor: $this->nm_aluno,
            tamanhovisivel: 30,
            tamanhomaximo: 255,
            descricao2: '<img border="0" onclick="pesquisa_aluno();" id="ref_cod_aluno_lupa" name="ref_cod_aluno_lupa" src="imagens/lupa.png" />'
        );

        // Código do aluno (retornado de pop-up de busca da pesquisa de alunos - lupa)
        $this->campoOculto(nome: 'ref_cod_aluno', valor: $this->ref_cod_aluno);

        // Paginador
        $this->limite = 20;
        $this->offset = $_GET["pagina_{$this->nome}"] ?
      ($_GET["pagina_{$this->nome}"] * $this->limite - $this->limite)
      : 0;

        // Instância objeto de mapeamento relacional com o tabela pmieducar.reserva_vaga
        $obj_reserva_vaga = new clsPmieducarReservaVaga();
        $obj_reserva_vaga->setOrderby(strNomeCampo: 'data_cadastro ASC');
        $obj_reserva_vaga->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        // Lista os registros usando os valores passados pelos filtros
        $lista = $obj_reserva_vaga->lista(
            int_cod_reserva_vaga: $this->cod_reserva_vaga,
            int_ref_ref_cod_escola: $this->ref_ref_cod_escola,
            int_ref_ref_cod_serie: $this->ref_ref_cod_serie,
            int_ref_cod_aluno: $this->ref_cod_aluno,
            int_ativo: 1,
            int_ref_cod_instituicao: $this->ref_cod_instituicao,
            int_ref_cod_curso: $this->ref_cod_curso
        );

        // Pega o total de registros encontrados
        $total = $obj_reserva_vaga->_total;

        // Itera sobre resultados montando a lista de apresentação
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                // Recupera nome da série da reserva de vaga
                $obj_serie = new clsPmieducarSerie(cod_serie: $registro['ref_ref_cod_serie']);
                $det_serie = $obj_serie->detalhe();
                $nm_serie = $det_serie['nm_serie'];

                // Recupera o nome do curso da reserva de vaga
                $obj_curso = new clsPmieducarCurso(cod_curso: $registro['ref_cod_curso']);
                $det_curso = $obj_curso->detalhe();
                $registro['ref_cod_curso'] = $det_curso['nm_curso'];

                // Recupera o nome da escola da reserva de vaga
                $obj_escola = new clsPmieducarEscola(cod_escola: $registro['ref_ref_cod_escola']);
                $det_escola = $obj_escola->detalhe();
                $nm_escola = $det_escola['nome'];

                // Recupera o nome da instituição da reserva de vaga
                $obj_ref_cod_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_cod_instituicao']);
                $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

                /*
                 * Se for um aluno previamente cadastrado, procuramos seu nome, primeiro
                 * buscando a referência de Pessoa e depois pesquisando a tabela para
                 * carregar o nome
                 */
                if ($registro['ref_cod_aluno']) {
                    // Pesquisa por aluno para pegar o identificador de Pessoa
                    $obj_aluno = new clsPmieducarAluno(cod_aluno: $registro['ref_cod_aluno']);
                    $det_aluno = $obj_aluno->detalhe();
                    $ref_idpes = $det_aluno['ref_idpes'];

                    // Pesquisa a tabela de pessoa para recuperar o nome
                    $obj_pessoa = new clsPessoa_(int_idpes: $ref_idpes);
                    $det_pessoa = $obj_pessoa->detalhe();
                    $registro['ref_cod_aluno'] = $det_pessoa['nome'];
                } else {
                    $registro['ref_cod_aluno'] = $registro['nm_aluno'] . ' (aluno externo)';
                }

                // Array de dados formatados para apresentação
                $lista_busca = [
                    "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro['cod_reserva_vaga']}\">{$registro['ref_cod_aluno']}</a>",
                    "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro['cod_reserva_vaga']}\">{$nm_serie}</a>",
                    "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro['cod_reserva_vaga']}\">{$registro['ref_cod_curso']}</a>",
                ];

                // Verifica por permissões
                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro['cod_reserva_vaga']}\">{$nm_escola}</a>";
                    $lista_busca[] = "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro['cod_reserva_vaga']}\">{$registro['ref_cod_instituicao']}</a>";
                } elseif ($nivel_usuario == 2) {
                    $lista_busca[] = "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro['cod_reserva_vaga']}\">{$nm_escola}</a>";
                }

                $this->addLinhas(linha: $lista_busca);
            }
        }

        $this->addPaginador2(
            strUrl: 'educar_reservada_vaga_lst.php',
            intTotalRegistros: $total,
            mixVariaveisMantidas: $_GET,
            nome: $this->nome,
            intResultadosPorPagina: $this->limite
        );

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de vagas reservadas', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-reserva-vaga-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Vagas Reservadas';
        $this->processoAp = '639';
    }
};
