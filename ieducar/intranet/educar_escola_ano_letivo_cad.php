<?php

return new class extends clsCadastro {
    public $pessoa_logada;

    public $ref_cod_escola;
    public $ano;
    public $ref_usuario_cad;
    public $ref_usuario_exc;
    public $andamento;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ano            = $_GET['ano'];
        $this->ref_cod_escola = $_GET['cod_escola'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            561,
            $this->pessoa_logada,
            7,
            'educar_escola_lst.php'
        );

        $this->nome_url_sucesso  = 'Continuar';
        $this->url_cancelar      = 'educar_escola_det.php?cod_escola=' . $this->ref_cod_escola;

        $this->breadcrumb('Definição do ano letivo', [
        url('intranet/educar_index.php') => 'Escola',
    ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // Primary keys
        $this->campoOculto('ref_cod_escola', $this->ref_cod_escola);
        $this->campoOculto('ano', $this->ano);

        $obj_anos = new clsPmieducarEscolaAnoLetivo();
        $lista_ano = $obj_anos->lista(
            $this->ref_cod_escola,
            null,
            null,
            null,
            2,
            null,
            null,
            null,
            null,
            1
        );

        $ano_array = [];

        if ($lista_ano) {
            foreach ($lista_ano as $ano) {
                $ano_array[$ano['ano']] = $ano['ano'];
            }
        }

        $ano_atual = date('Y') - 5;

        // Foreign keys
        $opcoes = ['' => 'Selecione'];
        $lim = 10;

        for ($i = 0; $i < $lim; $i++) {
            $ano = $ano_atual + $i;

            if (! key_exists($ano, $ano_array)) {
                $opcoes[$ano] = $ano;
            } else {
                $lim++;
            }
        }

        $this->campoLista('ano', 'Ano', $opcoes, $this->ano);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            561,
            $this->pessoa_logada,
            7,
            'educar_escola_lst.php'
        );

        $url = sprintf(
            'educar_ano_letivo_modulo_cad.php?ref_cod_escola=%s&ano=%s',
            $this->ref_cod_escola,
            $this->ano
        );

        $this->simpleRedirect($url);
    }

    public function Formular()
    {
        $this->title = 'Escola Ano Letivo';
        $this->processoAp = 561;
    }
};
