<?php

return new class extends clsDetalhe {
    public $titulo;

    public function Gerar()
    {
        // Verificação de permissão para cadastro.
        $this->obj_permissao = new clsPermissoes();

        $this->nivel_usuario = $this->obj_permissao->nivel_acesso($this->pessoa_logada);

        $this->titulo = 'Veiculo - Detalhe';

        $cod_veiculo = $_GET['cod_veiculo'];

        $tmp_obj = new clsModulesVeiculo($cod_veiculo);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('transporte_veiculo_lst.php');
        }

        $this->addDetalhe(['Código do veículo', $cod_veiculo]);
        $this->addDetalhe(['Descrição', $registro['descricao']]);
        $this->addDetalhe(['Placa', $registro['placa']]);
        $this->addDetalhe(['Renavam', $registro['renavam']]);
        $this->addDetalhe(['Chassi', $registro['chassi']]);
        $this->addDetalhe(['Marca', $registro['marca']]);
        $this->addDetalhe(['Ano fabricação', $registro['ano_fabricacao']]);
        $this->addDetalhe(['Ano modelo', $registro['ano_modelo']]);
        $this->addDetalhe(['Limite de passageiros', $registro['passageiros']]);
        $malha ='';
        switch ($registro['malha']) {
      case 'A':
        $malha = 'Aquática/Embarcação';
        break;
      case 'F':
        $malha = 'Ferroviária';
        break;
      case 'R':
        $malha = 'Rodoviária';
        break;
    }
        $this->addDetalhe(['Malha', $malha]);
        $this->addDetalhe(['Categoria', $registro['descricao_tipo']]);
        $this->addDetalhe(['Exclusivo para transporte escolar', ($registro['exclusivo_transporte_escolar'] == 'S' ? 'Sim' : 'Não')]);
        $this->addDetalhe(['Adaptado para pessoas com necessidades especiais', ($registro['adaptado_necessidades_especiais'] == 'S' ? 'Sim' : 'Não')]);
        $this->addDetalhe(['Ativo', ($registro['ativo'] == 'S' ? 'Sim' : 'Não')]);
        if ($registro['ativo']=='N') {
            $this->addDetalhe(['Descrição inativo', $registro['descricao_inativo']]);
        }
        $this->addDetalhe(['Empresa', $registro['nome_empresa']]);
        $this->addDetalhe(['Motorista responsável', $registro['nome_motorista']]);
        $this->addDetalhe(['Observação', $registro['observacao']]);
        $this->url_cancelar = 'transporte_veiculo_lst.php';

        $this->largura = '100%';

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(21237, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = '../module/TransporteEscolar/Veiculo';
            $this->url_editar = "../module/TransporteEscolar/Veiculo?id={$cod_veiculo}";
        }

        $this->breadcrumb('Detalhe do veículo', [
        url('intranet/educar_transporte_escolar_index.php') => 'Transporte escolar',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Veiculos';
        $this->processoAp = 21237;
    }
};
