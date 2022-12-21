<?php

use Illuminate\Support\Facades\Session;

return new class extends clsCadastro {
    public $pessoa_logada;
    public $cod_servidor;
    public $ref_cod_instituicao;
    public $ref_idesco;
    public $ref_cod_funcao;
    public $carga_horaria;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_curso;
    public $ref_cod_disciplina;
    public $cursos_disciplina;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_servidor = $this->getQueryString('ref_cod_servidor');
        $this->ref_cod_instituicao = $this->getQueryString('ref_cod_instituicao');
        $this->ref_cod_funcao = $this->getQueryString('cod_funcao');

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 635,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_servidor_lst.php'
        );

        if (is_numeric($this->cod_servidor) && is_numeric($this->ref_cod_instituicao)) {
            $obj = new clsPmieducarServidor(
                cod_servidor: $this->cod_servidor,
                ref_cod_deficiencia: null,
                ref_idesco: null,
                carga_horaria: null,
                data_cadastro: null,
                data_exclusao: null,
                ativo: null,
                ref_cod_instituicao: $this->ref_cod_instituicao
            );

            $registro = $obj->detalhe();
            if ($registro) {
                $retorno = 'Editar';
            }
        }

        $funcoes = Session::get(key: "servant:{$this->cod_servidor}", default: []);
        $funcoes = $funcoes[$this->ref_cod_funcao] ?? [];

        foreach ($funcoes as $curso => $disciplinas) {
            foreach ($disciplinas as $disciplina) {
                $this->cursos_disciplina[$curso][$disciplina] = $this->ref_cod_funcao;
            }
        }

        if ($this->cursos_disciplina) {
            foreach ($this->cursos_disciplina as $curso => $disciplinas) {
                if ($disciplinas) {
                    foreach ($disciplinas as $disciplina => $funcao) {
                        $this->ref_cod_curso[] = $curso;
                        $this->ref_cod_disciplina[] = $disciplina;
                    }
                }
            }
        }

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto(nome: 'ref_cod_instituicao', valor: $this->ref_cod_instituicao);
        $opcoes = $opcoes_curso = ['' => 'Selecione'];

        $obj_cursos = new clsPmieducarCurso();
        $obj_cursos->setOrderby('nm_curso');
        $lst_cursos = $obj_cursos->lista(
            int_cod_curso: null,
            int_ref_usuario_cad: null,
            int_ref_cod_tipo_regime: null,
            int_ref_cod_nivel_ensino: null,
            int_ref_cod_tipo_ensino: null,
            int_ref_cod_tipo_avaliacao: null,
            str_nm_curso: null,
            str_sgl_curso: null,
            int_qtd_etapas: null,
            int_frequencia_minima: null,
            int_media: null,
            int_media_exame: null,
            int_falta_ch_globalizada: null,
            int_carga_horaria: null,
            str_ato_poder_publico: null,
            int_edicao_final: null,
            str_objetivo_curso: null,
            str_publico_alvo: null,
            date_data_cadastro_ini: null,
            date_data_cadastro_fim: null,
            date_data_exclusao_ini: null,
            date_data_exclusao_fim: null,
            int_ativo: 1,
            int_ref_usuario_exc: null,
            int_ref_cod_instituicao: $this->ref_cod_instituicao
        );

        if ($lst_cursos) {
            foreach ($lst_cursos as $curso) {
                $opcoes_curso[$curso['cod_curso']] = $curso['nm_curso'];
            }
        }

        $lst_opcoes = [];
        $arr_valores = [];

        if ($this->cursos_disciplina) {
            foreach ($this->cursos_disciplina as $curso => $disciplinas) {
                if ($disciplinas) {
                    foreach ($disciplinas as $disciplina => $funcao) {
                        if ($funcao != $this->ref_cod_funcao) {
                            continue;
                        }
                        $arr_valores[] = [$curso, $disciplina];
                    }
                }
            }
        }

        if ($this->ref_cod_curso) {
            $cursosDifferente = array_unique($this->ref_cod_curso);
            foreach ($cursosDifferente as $curso) {
                $obj_componentes = new clsModulesComponenteCurricular;
                $componentes = $obj_componentes->listaComponentesPorCurso(instituicao_id: $this->ref_cod_instituicao, curso: $curso);
                $opcoes_disc = [];
                $opcoes_disc['todas_disciplinas'] = 'Todas as disciplinas';

                $total_componentes = count($componentes);
                for ($i = 0; $i < $total_componentes; $i++) {
                    $opcoes_disc[$componentes[$i]['id']] = $componentes[$i]['nome'];
                }
                $disciplinasCurso[$curso] = [$opcoes_curso, $opcoes_disc];
            }
            foreach ($this->ref_cod_curso as $curso) {
                $lst_opcoes[] = $disciplinasCurso[$curso];
            }
        }

        $this->campoTabelaInicio(
            nome: 'funcao',
            titulo: 'Componentes Curriculares',
            arr_campos: ['Curso', 'Componente Curricular'],
            arr_valores: $arr_valores,
            largura: '',
            array_valores_lista: $lst_opcoes
        );

        // Cursos
        $this->campoLista(
            nome: 'ref_cod_curso',
            campo: 'Curso',
            valor: $opcoes_curso,
            default: $this->ref_cod_curso,
            acao: 'trocaCurso(this)',
            duplo: '',
            descricao: '',
            complemento: ''
        );

        // Disciplinas
        $this->campoLista(
            nome: 'ref_cod_disciplina',
            campo: 'Componente Curricular',
            valor: $opcoes,
            default: $this->ref_cod_disciplina,
            acao: '',
            duplo: '',
            descricao: '',
            complemento: ''
        );

        $this->campoTabelaFim();
    }

    public function Novo()
    {
        $cod_servidor = $this->getQueryString('ref_cod_servidor');
        $cod_funcao = $this->getQueryString('cod_funcao');

        $funcoes = Session::get(key: "servant:{$cod_servidor}", default: []);

        unset($funcoes[$cod_funcao]);

        if ($this->ref_cod_curso) {
            for ($i = 0, $loop = count($this->ref_cod_curso); $i < $loop; $i++) {
                if ($this->ref_cod_disciplina[$i] === 'todas_disciplinas') {
                    $componenteAnoDataMapper = new ComponenteCurricular_Model_AnoEscolarDataMapper();
                    $componentes = $componenteAnoDataMapper->findComponentePorCurso($this->ref_cod_curso[$i]);

                    foreach ($componentes as $componente) {
                        $funcoes[$cod_funcao][$this->ref_cod_curso[$i]][] = $componente->id;
                    }
                } else {
                    $funcoes[$cod_funcao][$this->ref_cod_curso[$i]][] = $this->ref_cod_disciplina[$i];
                }
            }
        }

        Session::put(key: "servant:{$cod_servidor}", value: $funcoes);
        Session::save();
        Session::start();

        echo "<script>parent.fechaExpansivel('{$_GET['div']}');</script>";
        die;
    }

    public function Editar()
    {
        $this->Novo();
    }

    public function Excluir()
    {
        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-servidor-disciplina-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Servidor Disciplina';
        $this->processoAp         = 0;
        $this->renderMenu         = false;
        $this->renderMenuSuspenso = false;
    }
};
