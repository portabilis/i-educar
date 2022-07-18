<?php

return new class extends clsDetalhe {
    public $titulo;
    public $id;
    public $ano;
    public $servidor_id;
    public $funcao_exercida;
    public $tipo_vinculo;
    public $ref_cod_instituicao;
    public $ref_cod_escola;
    public $ref_cod_curso;
    public $ref_cod_serie;
    public $ref_cod_turma;

    public function Gerar()
    {
        $this->titulo = 'Servidor Vínculo Turma - Detalhe';

        $this->id = $_GET['id'];

        $tmp_obj = new clsModulesProfessorTurma($this->id);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_servidor_professor_vinculo_lst.php');
        }

        $resources_funcao = [  null => 'Selecione',
                                1    => 'Docente',
                                2    => 'Auxiliar/Assistente educacional',
                                3    => 'Profissional/Monitor de atividade complementar',
                                4    => 'Tradutor Intérprete de LIBRAS',
                                5    => 'Docente titular - Coordenador de tutoria (de módulo ou disciplina) - EAD',
                                6    => 'Docente tutor - Auxiliar (de módulo ou disciplina) - EAD',
                                7    => 'Guia-Intérprete',
                                8    => 'Profissional de apoio escolar para aluno(a)s com deficiência (Lei 13.146/2015)',
                                9    => 'Instrutor da Educação Profissional'];

        $resources_tipo = [  null => 'Selecione',
                              1    => 'Concursado/efetivo/estável',
                              2    => 'Contrato temporário',
                              3    => 'Contrato terceirizado',
                              4    => 'Contrato CLT'];

        if ($registro['nm_escola']) {
            $this->addDetalhe(['Escola', $registro['nm_escola']]);
        }

        if ($registro['nm_curso']) {
            $this->addDetalhe(['Curso', $registro['nm_curso']]);
        }

        if ($registro['nm_serie']) {
            $this->addDetalhe(['Série', $registro['nm_serie']]);
        }

        if ($registro['nm_turma']) {
            $this->addDetalhe(['Turma', $registro['nm_turma']]);
        }

        if ($registro['funcao_exercida']) {
            $this->addDetalhe(['Função exercida', $resources_funcao[$registro['funcao_exercida']]]);
        }

        if ($registro['tipo_vinculo']) {
            $this->addDetalhe(['Tipo de vínculo', $resources_tipo[$registro['tipo_vinculo']]]);
        }

        $sql = 'SELECT nome
            FROM modules.professor_turma_disciplina
            INNER JOIN modules.componente_curricular cc ON (cc.id = componente_curricular_id)
            WHERE professor_turma_id = $1
            ORDER BY nome';

        $disciplinas = '';

        $resources = Portabilis_Utils_Database::fetchPreparedQuery($sql, [ 'params' => [$this->id] ]);

        foreach ($resources as $reg) {
            $disciplinas .= '<span style="background-color: #ccdce6; padding: 2px; border-radius: 3px;"><b>'.$reg['nome'].'</b></span> ';
        }

        if ($disciplinas != '') {
            $this->addDetalhe(['Disciplinas', $disciplinas]);
        }

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
            $this->url_novo = sprintf(
                'educar_servidor_vinculo_turma_cad.php?ref_cod_instituicao=%d&ref_cod_servidor=%d',
                $registro['instituicao_id'],
                $registro['servidor_id']
            );

            $this->url_editar = sprintf(
                'educar_servidor_vinculo_turma_cad.php?id=%d&ref_cod_instituicao=%d&ref_cod_servidor=%d',
                $registro['id'],
                $registro['instituicao_id'],
                $registro['servidor_id']
            );

            $this->array_botao[] = 'Copiar vínculo';
            $this->array_botao_url_script[] = sprintf(
                'go("educar_servidor_vinculo_turma_cad.php?id=%d&ref_cod_instituicao=%d&ref_cod_servidor=%d&copia");',
                $registro['id'],
                $registro['instituicao_id'],
                $registro['servidor_id']
            );
        }

        $this->url_cancelar = sprintf(
            'educar_servidor_vinculo_turma_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $registro['servidor_id'],
            $registro['instituicao_id']
        );

        $this->largura = '100%';

        $this->breadcrumb('Detalhe do vínculo', [
        url('intranet/educar_servidores_index.php') => 'Servidores',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores - Servidor Formação';
        $this->processoAp = 635;
    }
};
