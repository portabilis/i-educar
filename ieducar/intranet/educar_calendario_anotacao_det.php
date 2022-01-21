<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_calendario_anotacao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_anotacao;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public $dia;
    public $mes;
    public $ano;
    public $ref_cod_calendario_ano_letivo;

    public function Gerar()
    {
        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->titulo = 'Calendario Anotacao - Detalhe';

        $this->cod_calendario_anotacao=$_GET['cod_calendario_anotacao'];

        $tmp_obj = new clsPmieducarCalendarioAnotacao($this->cod_calendario_anotacao);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            throw new HttpResponseException(
                new RedirectResponse('educar_calendario_ano_letivo_lst.php')
            );
        }

        if ($registro['cod_calendario_anotacao']) {
            $this->addDetalhe([ 'Calendario Anotacão', "{$registro['cod_calendario_anotacao']}"]);
        }
        if ($registro['nm_anotacao']) {
            $this->addDetalhe([ 'Nome Anotacão', "{$registro['nm_anotacao']}"]);
        }
        if ($registro['descricao']) {
            $this->addDetalhe([ 'Descricão', "{$registro['descricao']}"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(620, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_calendario_anotacao_cad.php';
            $this->url_editar = "educar_calendario_anotacao_cad.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}&cod_calendario_anotacao={$registro['cod_calendario_anotacao']}";
        }

        $this->url_cancelar = "educar_calendario_anotacao_lst.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}";
        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Calendario Anotacao';
        $this->processoAp = '620';
    }
};
