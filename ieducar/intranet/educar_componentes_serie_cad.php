<?php

return new class extends clsCadastro {
    public $pessoa_logada;

    public $instituicao_id;
    public $curso_id;
    public $serie_id;
    public $componente_id;
    public $carga_horaria;
    public $retorno;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->serie_id=$_GET['serie_id'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            9998859,
            $this->pessoa_logada,
            3,
            'educar_componentes_serie_lst.php'
        );

        if (is_numeric($this->serie_id)) {
            $retorno = 'Editar';
            $obj = new clsPmieducarSerie($this->serie_id);
            $registro  = $obj->detalhe();

            if ($registro) {
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->curso_id = $registro['ref_cod_curso'];

                $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
                $obj_curso_det = $obj_curso->detalhe();
                $this->instituicao_id = $obj_curso_det['ref_cod_instituicao'];
                $this->fexcluir = $obj_permissoes->permissao_excluir(
                    9998859,
                    $this->pessoa_logada,
                    3
                );
            }
        }

        $this->url_cancelar = 'educar_componentes_serie_lst.php';

        $this->breadcrumb('Componentes da série', [
        url('intranet/educar_index.php') => 'Escola',
    ]);

        $this->nome_url_cancelar = 'Cancelar';

        $this->alerta_faixa_etaria  = dbBool($this->alerta_faixa_etaria);
        $this->bloquear_matricula_faixa_etaria  = dbBool($this->bloquear_matricula_faixa_etaria);
        $this->exigir_inep  = dbBool($this->exigir_inep);

        $this->retorno = $retorno;

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }

        $opcoesCurso = ['' => 'Selecione um curso'];
        $opcoesSerie = ['' => 'Selecione uma série'];

        $this->campoOculto('curso_id', $this->curso_id);
        $this->campoOculto('serie_id', $this->serie_id);
        $this->campoOculto('serie_id', $this->serie_id);
        $this->campoOculto('retorno', $this->retorno);
        $this->campoOculto('sugestao_anos_letivos', json_encode(array_values($this->anosLetivosExistentes())));

        $this->inputsHelper()->dynamic('instituicao', ['value' => $this->instituicao_id]);

        $this->campoLista('ref_cod_curso', 'Curso', $opcoesCurso, $this->curso_id);
        $this->campoLista('ref_cod_serie', 'Série', $opcoesSerie, $this->serie_id);

        $helperOptions = ['objectName'  => 'ref_cod_area_conhecimento'];
        $options       = ['label' => 'Áreas de conhecimento',
                           'size' => 50,
                           'required' => false];

        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

        $this->campoRotulo('componentes_', 'Componentes da série', '<table id=\'componentes\'></table>');

        $scripts = ['/modules/Cadastro/Assets/Javascripts/ComponentesSerie.js',
                     '/modules/Cadastro/Assets/Javascripts/ComponentesSerieAcao.js'];
        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    public function Novo()
    {
        // Todas as ações estão sendo realizadas em ComponentesSerieAcao.js
        $this->simpleRedirect('educar_componentes_serie_lst.php');
    }

    public function Editar()
    {
        // Todas as ações estão sendo realizadas em ComponentesSerieAcao.js
        $this->simpleRedirect('educar_componentes_serie_lst.php');
    }

    public function Excluir()
    {
        // Todas as ações estão sendo realizadas em ComponentesSerieAcao.js
        $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
        $this->simpleRedirect('educar_componentes_serie_lst.php');
    }

    public function Formular()
    {
        $this->title = 'S&eacute;rie';
        $this->processoAp = '9998859';
    }
};
