<?php

use App\Models\LegacySequenceGrade;

return new class extends clsListagem
{
    public $limite;

    public $offset;

    public $ref_serie_origem;

    public $ref_serie_destino;

    public $ref_curso_origem;

    public $ref_curso_destino;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Sequência Enturmação - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
            'Curso Origem',
            'Série Origem',
            'Curso Destino',
            'Série Destino',
        ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Instituição';
        }
        $this->addCabecalhos($lista_busca);

        // Filtros de Foreign Keys
        if ($nivel_usuario == 1) {
            $objInstituicao = new clsPmieducarInstituicao();
            $opcoes = ['' => 'Selecione'];
            $objInstituicao->setOrderby('nm_instituicao ASC');
            $lista = $objInstituicao->lista();
            if (is_array($lista)) {
                foreach ($lista as $linha) {
                    $opcoes[$linha['cod_instituicao']] = $linha['nm_instituicao'];
                }
            }
            $this->campoLista('ref_cod_instituicao', 'Instituição', $opcoes, $this->ref_cod_instituicao, '', null, null, null, null, false);
        } else {
            $obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
            $obj_usuario_det = $obj_usuario->detalhe();
            $this->ref_cod_instituicao = $obj_usuario_det['ref_cod_instituicao'];
        }

        $opcoes = ['' => 'Selecione'];
        $opcoes_ = ['' => 'Selecione'];

        // EDITAR
        if ($this->ref_cod_instituicao) {
            $objTemp = new clsPmieducarCurso();
            $objTemp->setOrderby('nm_curso');
            $lista = $objTemp->lista(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 1, null, $this->ref_cod_instituicao);
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['cod_curso']] = $registro['nm_curso'] . (!empty($registro['descricao']) ? ' - ' . $registro['descricao'] : '');
                    $opcoes_[$registro['cod_curso']] = $registro['nm_curso'] . (!empty($registro['descricao']) ? ' - ' . $registro['descricao'] : '');
                }
            }
        }

        $this->campoLista('ref_curso_origem', 'Curso Origem', $opcoes, $this->ref_curso_origem, '', true, '', '', false, false);
        $this->campoLista('ref_curso_destino', ' Curso Destino', $opcoes_, $this->ref_curso_destino, '', false, '', '', false, false);

        // primary keys

        $opcoes = ['' => 'Selecione'];
        $opcoes_ = ['' => 'Selecione'];

        if ($this->ref_curso_origem) {
            $objTemp = new clsPmieducarSerie();
            $lista = $objTemp->lista(null, null, null, $this->ref_curso_origem, null, null, null, null, null, null, null, null, 1);
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['cod_serie']] = $registro['nm_serie']  . (!empty($registro['descricao']) ? ' - ' . $registro['descricao'] : '');
                }
            }
        }
        if ($this->ref_curso_destino) {
            $objTemp = new clsPmieducarSerie();
            $lista = $objTemp->lista(null, null, null, $this->ref_curso_destino, null, null, null, null, null, null, null, null, 1);
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes_[$registro['cod_serie']] = $registro['nm_serie']  . (!empty($registro['descricao']) ? ' - ' . $registro['descricao'] : '');
                }
            }
        }

        $this->campoLista('ref_serie_origem', 'Série Origem', $opcoes, $this->ref_serie_origem, null, true, '', '', false, false);
        $this->campoLista('ref_serie_destino', ' Série Destino', $opcoes_, $this->ref_serie_destino, '', false, '', '', false, false);

        // Paginador
        $this->limite = 20;
        $lista = LegacySequenceGrade::query()
            ->filter([
                'institution' => $this->ref_cod_instituicao,
                'grade_origin' => $this->ref_serie_origem,
                'grade_destiny' => $this->ref_serie_destino,
                'course_origin' => $this->ref_curso_origem,
                'course_destiny' => $this->ref_curso_destino,
            ])
            ->with([
                'gradeOrigin:cod_serie,nm_serie,ref_cod_curso',
                'gradeDestiny:cod_serie,nm_serie,ref_cod_curso',
                'gradeOrigin.course:cod_curso,nm_curso,descricao,ref_cod_instituicao',
                'gradeDestiny.course:cod_curso,nm_curso,descricao,ref_cod_instituicao',
                'gradeOrigin.course.institution:cod_instituicao,nm_instituicao',
            ])
            ->active()
            ->orderBy('data_cadastro')
            ->paginate(perPage: $this->limite, pageName: "pagina_{$this->nome}");
        $total = $lista->total();

        // monta a lista
        if ($lista->isNotEmpty()) {
            foreach ($lista as $registro) {
                $url = "educar_sequencia_serie_det.php?id={$registro->id}";
                $lista_busca = [
                    "<a href=\"{$url}\">{$registro->gradeOrigin->course->name}</a>",
                    "<a href=\"{$url}\">{$registro->gradeOrigin->name}</a>",
                    "<a href=\"{$url}\">{$registro->gradeDestiny->course->name}</a>",
                    "<a href=\"{$url}\">{$registro->gradeDestiny->name}</a>",
                ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"{$url}\">{$registro->gradeOrigin->course->institution->name}</a>";
                }
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2('educar_sequencia_serie_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(587, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_sequencia_serie_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de sequências de enturmação', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-sequencia-serie.js');
    }

    public function Formular()
    {
        $this->title = 'Sequência Enturmação';
        $this->processoAp = 587;
    }
};
