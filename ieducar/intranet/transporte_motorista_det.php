<?php

return new class extends clsDetalhe {
    public $titulo;

    public function Gerar()
    {
        // Verificação de permissão para cadastro.
        $this->obj_permissao = new clsPermissoes();

        $this->nivel_usuario = $this->obj_permissao->nivel_acesso($this->pessoa_logada);

        $this->titulo = 'Motorista - Detalhe';

        $cod_motorista = $_GET['cod_motorista'];

        $tmp_obj = new clsModulesMotorista($cod_motorista);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('transporte_motorista_lst.php');
        }

        $this->addDetalhe(['Código do motorista', $cod_motorista]);
        $this->addDetalhe(['Nome', $registro['nome_motorista'].'<br/> <a target=\'_blank\' style=\' text-decoration: underline;\' href=\'atendidos_det.php?cod_pessoa='.$registro['ref_idpes'].'\'>Visualizar pessoa</a>']);
        $this->addDetalhe(['CNH', $registro['cnh']]);
        $this->addDetalhe(['Categoria', $registro['tipo_cnh']]);
        if (trim($registro['dt_habilitacao'])!='') {
            $this->addDetalhe(['Data da habilitação', Portabilis_Date_Utils::pgSQLToBr($registro['dt_habilitacao']) ]);
        }
        if (trim($registro['vencimento_cnh'])!='') {
            $this->addDetalhe(['Vencimento da habilitação', Portabilis_Date_Utils::pgSQLToBr($registro['vencimento_cnh']) ]);
        }

        $this->addDetalhe(['Observação', $registro['observacao']]);
        $this->url_cancelar = 'transporte_motorista_lst.php';

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(21236, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = '../module/TransporteEscolar/Motorista';
            $this->url_editar = "../module/TransporteEscolar/motorista?id={$cod_motorista}";
        }

        $this->largura = '100%';

        $this->breadcrumb('Detalhe do motorista', [
        url('intranet/educar_transporte_escolar_index.php') => 'Transporte escolar',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Motoristas';
        $this->processoAp = 21236;
    }
};
