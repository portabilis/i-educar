<?php

return new class extends clsDetalhe {
    public $titulo;

    public function Gerar()
    {
        // Verificação de permissão para cadastro.
        $this->obj_permissao = new clsPermissoes();

        $this->nivel_usuario = $this->obj_permissao->nivel_acesso($this->pessoa_logada);

        $this->titulo = 'Rota - Detalhe';

        $cod_rota_transporte_escolar = $_GET['cod_rota'];

        $tmp_obj = new clsModulesRotaTransporteEscolar($cod_rota_transporte_escolar);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('transporte_rota_lst.php');
        }

        $this->addDetalhe(['Ano', $registro['ano']]);
        $this->addDetalhe(['Código da rota', $cod_rota_transporte_escolar]);
        $this->addDetalhe(['Descrição', $registro['descricao']]);
        $this->addDetalhe(['Destino', $registro['nome_destino']]);
        $this->addDetalhe(['Empresa', $registro['nome_empresa']]);
        $this->addDetalhe(['Tipo da rota', ($registro['tipo_rota'] == 'U' ? 'Urbana' : 'Rural')]);
        if (trim($registro['km_pav'])!='') {
            $this->addDetalhe(['Percurso pavimentado', $registro['km_pav'].' km']);
        }
        if (trim($registro['km_npav'])!='') {
            $this->addDetalhe(['Percurso não pavimentado', $registro['km_npav'].' km']);
        }

        $this->addDetalhe(['Terceirizado', ($registro['tercerizado'] == 'S' ? 'Sim' : 'Não')]);

        // Itinerário

        $obj = new clsModulesItinerarioTransporteEscolar();
        $obj->setOrderby('seq ASC');
        $lst = $obj->lista(null, $cod_rota_transporte_escolar);

        if ($lst) {
            $tabela = '
          <table>
          <tr colspan=\'5\'><td><a style=\' text-decoration: underline;\' href=\'/intranet/transporte_itinerario_cad.php?cod_rota='.$cod_rota_transporte_escolar.'\'>Editar itinerário</a></td></tr>
            <tr align="center">
              <td bgcolor="#ccdce6"><b>Sequencial</b></td>
              <td bgcolor="#ccdce6"><b>Ponto</b></td>
              <td bgcolor="#ccdce6"><b>Hora</b></td>
              <td bgcolor="#ccdce6"><b>Tipo</b></td>
              <td bgcolor="#ccdce6"><b>Veículo</b></td>
            </tr>';

            $cont = 0;

            foreach ($lst as $valor) {
                if (($cont % 2) == 0) {
                    $color = ' bgcolor="#f5f9fd" ';
                } else {
                    $color = ' bgcolor="#FFFFFF" ';
                }

                $obj_veiculo = new clsModulesVeiculo($valor['ref_cod_veiculo']);
                $obj_veiculo = $obj_veiculo->detalhe();

                $motorista = new clsModulesMotorista($obj_veiculo['ref_cod_motorista']);
                $motorista = $motorista->detalhe();

                $valor_veiculo = $obj_veiculo['descricao']==''?'':$obj_veiculo['descricao'].' - Placa: '.$obj_veiculo['placa'] . ' - Motorista: ' . $motorista['nome_motorista'];

                $obj_ponto = new clsModulesPontoTransporteEscolar($valor['ref_cod_ponto_transporte_escolar']);
                $obj_ponto = $obj_ponto->detalhe();
                $valor_ponto = $obj_ponto['descricao'];

                $tabela .= sprintf(
                    '
            <tr>
              <td %s align=left>%s</td>
              <td %s align=left>%s</td>
              <td %s align=left>%s</td>
              <td %s align=left>%s</td>
              <td %s align=left>%s</td>
            </tr>',
                    $color,
                    $valor['seq'],
                    $color,
                    $valor_ponto,
                    $color,
                    $valor['hora'],
                    $color,
                    ($valor['tipo'] == 'V' ? 'Volta' : 'Ida'),
                    $color,
                    $valor_veiculo
                );

                $cont++;
            }

            $tabela .= '</table>';
        }
        if ($tabela) {
            $this->addDetalhe(['Itinerário', $tabela]);
        } else {
            $this->addDetalhe(['Itinerário', '<a style=\' text-decoration: underline; font-size: 12px;\' href=\'/intranet/transporte_itinerario_cad.php?cod_rota='.$cod_rota_transporte_escolar.'\'>Editar itinerário</a>']);
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(21238, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = '../module/TransporteEscolar/Rota';
            $this->url_editar = "../module/TransporteEscolar/Rota?id={$cod_rota_transporte_escolar}";
        }

        $this->url_cancelar = 'transporte_rota_lst.php';

        $this->largura = '100%';

        $this->breadcrumb('Detalhe da rota', [
        url('intranet/educar_transporte_escolar_index.php') => 'Transporte escolar',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Rotas';
        $this->processoAp = 21238;
    }
};
