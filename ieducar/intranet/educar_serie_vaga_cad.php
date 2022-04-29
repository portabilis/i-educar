<?php

return new class extends clsCadastro {
    public $pessoa_logada;

    public $cod_serie_vaga;
    public $ano;
    public $ref_cod_instituicao;
    public $ref_cod_escola;
    public $ref_cod_curso;
    public $ref_cod_serie;
    public $vagas;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_serie_vaga = $_GET['cod_serie_vaga'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            21253,
            $this->pessoa_logada,
            7,
            'educar_serie_vaga_lst.php'
        );

        if (is_numeric($this->cod_serie_vaga)) {
            $obj = new clsPmieducarSerieVaga($this->cod_serie_vaga);

            $registro  = $obj->detalhe();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $obj_permissoes = new clsPermissoes();

                if ($obj_permissoes->permissao_excluir(21253, $this->pessoa_logada, 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = $retorno == 'Editar' ?
      sprintf('educar_serie_vaga_det.php?cod_serie_vaga=%d', $this->cod_serie_vaga) : 'educar_serie_vaga_lst.php';

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' vagas por série', [
        url('intranet/educar_index.php') => 'Escola',
    ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_serie_vaga', $this->cod_serie_vaga);

        $this->inputsHelper()->dynamic(['ano', 'instituicao', 'escola', 'curso', 'serie'], ['disabled' => is_numeric($this->cod_serie_vaga)]);

        $options = [
      'value'     => $this->turno,
      'resources' => [
        0 => 'Selecione',
        1 => 'Matutino',
        2 => 'Vespertino',
        3 => 'Noturno',
        4 => 'Integral'
    ],
      'disabled' => is_numeric($this->cod_serie_vaga)
    ];
        $this->inputsHelper()->select('turno', $options);

        $this->campoNumero('vagas', 'Vagas', $this->vagas, 3, 3, true);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(21253, $this->pessoa_logada, 7, 'educar_serie_vaga_lst.php');

        $sql = 'SELECT MAX(cod_serie_vaga) + 1 FROM pmieducar.serie_vaga';
        $db  = new clsBanco();
        $max_cod_serie = $db->CampoUnico($sql);
        $max_cod_serie = $max_cod_serie > 0 ? $max_cod_serie : 1;

        $obj = new clsPmieducarSerieVaga(
            $max_cod_serie,
            $this->ano,
            $this->ref_cod_instituicao,
            $this->ref_cod_escola,
            $this->ref_cod_curso,
            $this->ref_cod_serie,
            $this->turno,
            $this->vagas
        );

        $lista = $obj->lista($this->ano, $this->ref_cod_escola, $this->ref_cod_curso, $this->ref_cod_serie, $this->turno);
        if (is_array($lista[0]) && count($lista[0])) {
            $this->mensagem = 'Já; existe cadastro para está; série/ano!<br />';

            return false;
        }

        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
            $this->simpleRedirect('educar_serie_vaga_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado. Verifique se já não existe cadastro para está série/ano!<br />';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(21253, $this->pessoa_logada, 7, 'educar_serie_vaga_lst.php');

        $obj = new clsPmieducarSerieVaga($this->cod_serie_vaga);
        $obj->vagas = $this->vagas;

        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br />';
            $this->simpleRedirect('educar_serie_vaga_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br />';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(21253, $this->pessoa_logada, 7, 'educar_serie_vaga_lst.php');

        $obj = new clsPmieducarSerieVaga($this->cod_serie_vaga);

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br />';
            $this->simpleRedirect('educar_serie_vaga_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br />';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Vagas por sÃ©rie';
        $this->processoAp = 21253;
    }
};
