<?php

use App\Models\LegacyRegimeType;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $offset;

    public $cod_tipo_regime;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $nm_tipo;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Tipo Regime - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

        switch ($nivel_usuario) {
            case 1:
                $this->addCabecalhos([
                    'Nome Tipo',
                    'Instituição',
                ]);
                break;

            default:
                $this->addCabecalhos([
                    'Nome Tipo',
                ]);
                break;
        }

        // Filtros de Foreign Keys
        $get_escola = false;
        include 'include/pmieducar/educar_campo_lista.php';

        // outros Filtros
        $this->campoTexto('nm_tipo', 'Nome Tipo', $this->nm_tipo, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $query = LegacyRegimeType::query()
            ->where('ativo', 1)
            ->orderBy('nm_tipo', 'ASC');

        if (is_string($this->nm_tipo)) {
            $query->where('nm_tipo', 'ilike', '%' . $this->nm_tipo . '%');
        }

        $result = $query->paginate($this->limite, pageName: 'pagina_' . $this->nome);

        $lista = $result->items();
        $total = $result->total();

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

                if ($nivel_usuario == 1) {
                    $this->addLinhas([
                        "<a href=\"educar_tipo_regime_det.php?cod_tipo_regime={$registro['cod_tipo_regime']}\">{$registro['nm_tipo']}</a>",
                        "<a href=\"educar_tipo_regime_det.php?cod_tipo_regime={$registro['cod_tipo_regime']}\">{$registro['ref_cod_instituicao']}</a>",
                    ]);
                } else {
                    $this->addLinhas([
                        "<a href=\"educar_tipo_regime_det.php?cod_tipo_regime={$registro['cod_tipo_regime']}\">{$registro['nm_tipo']}</a>",
                    ]);
                }
            }
        }

        $this->addPaginador2('educar_tipo_regime_lst.php', $total, $_GET, $this->nome, $this->limite);

        //** Verificacao de permissao para cadastro

        if ($obj_permissao->permissao_cadastra(568, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_tipo_regime_cad.php")';
            $this->nome_acao = 'Novo';
        }
        //**
        $this->largura = '100%';

        $this->breadcrumb('Listagem de tipos de regime', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Tipo Regime';
        $this->processoAp = '568';
    }
};
