<?php

class RotaController extends ApiCoreController
{
    protected $_processoAp = 21238; //verificar
    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA; // verificar

  protected function loadNomePessoaj($id)
  {
      $sql = 'select nome from cadastro.pessoa, modules.rota_transporte_escolar rt where idpes = rt.ref_idpes_destino and cod_rota_transporte_escolar = $1';
      $nome = $this->fetchPreparedQuery($sql, $id, false, 'first-field');

      return $this->toUtf8($nome, ['transform' => true]);
  }

    protected function loadNomeEmpresa($id)
    {
        $sql = 'select nome from cadastro.pessoa, modules.empresa_transporte_escolar emp, modules.rota_transporte_escolar rt where idpes = emp.ref_idpes and emp.cod_empresa_transporte_escolar = rt.ref_cod_empresa_transporte_escolar and rt.cod_rota_transporte_escolar = $1';
        $nome = $this->fetchPreparedQuery($sql, $id, false, 'first-field');

        return $this->toUtf8($nome, ['transform' => true]);
    }

    protected function validatesValueIsNumeric($value)
    {
        return (is_numeric($value) || empty($value));
    }

    protected function createOrUpdateRota($id = null)
    {
        $rota = new clsModulesRotaTransporteEscolar();
        $rota->cod_rota_transporte_escolar = $id;

        // após cadastro não muda mais id pessoa
        $rota->descricao = $this->getRequest()->desc;
        $rota->ref_idpes_destino = $this->getRequest()->pessoaj_id;
        $rota->ano = $this->getRequest()->ano;
        $rota->tipo_rota = $this->getRequest()->tipo_rota;
        $rota->km_pav = $this->getRequest()->km_pav;
        $rota->km_npav = $this->getRequest()->km_npav;
        $rota->ref_cod_empresa_transporte_escolar = $this->getRequest()->empresa_id;
        $rota->tercerizado = ($this->getRequest()->tercerizado == 'on' ? 'S' : 'N');

        return (is_null($id) ? $rota->cadastra() : $rota->edita());
    }

    protected function sqlsForNumericSearch()
    {
        $sqls[] = 'select distinct cod_rota_transporte_escolar as id, descricao as name from
            modules.rota_transporte_escolar where cod_rota_transporte_escolar like $1||\'%\'';

        return $sqls;
    }

    protected function sqlsForStringSearch()
    {
        $sqls[] = 'select distinct cod_rota_transporte_escolar as id, descricao as name  from
            modules.rota_transporte_escolar where lower((descricao)) like \'%\'||lower(($1))||\'%\'';

        return $sqls;
    }

    protected function get()
    {
        $id = $this->getRequest()->id;
        $rota = new clsModulesRotaTransporteEscolar();
        $rota->cod_rota_transporte_escolar = $id;
        $rota = $rota->detalhe();

        $attrs = [
            'cod_rota_transporte_escolar' => 'id',
            'descricao' => 'desc',
            'ref_idpes_destino' => 'ref_idpes_destino',
            'ano' => 'ano',
            'tipo_rota' => 'tipo_rota',
            'km_pav' => 'km_pav',
            'km_npav' => 'km_npav',
            'ref_cod_empresa_transporte_escolar' => 'ref_cod_empresa_transporte_escolar',
            'tercerizado' => 'tercerizado'
        ];

        $rota = Portabilis_Array_Utils::filter($rota, $attrs);

        $rota['nomeEmpresa'] = Portabilis_String_Utils::toUtf8($this->loadNomeEmpresa($id));
        $rota['nomeDestino'] = Portabilis_String_Utils::toUtf8($this->loadNomePessoaj($id));
        $rota['desc'] = Portabilis_String_Utils::toUtf8($rota['desc']);

        return $rota;
    }

    protected function validateIfRotaIsNotInUse()
    {
        $pt = new clsModulesPessoaTransporte();
        $lista = $pt->lista(null, null, $this->getRequest()->id);

        if (is_array($lista) && count($lista)>0) {
            $this->messenger->append(
                'Não é possível excluir uma rota que está vinculada a uma pessoa.',
                'error',
                false,
                'error'
            );

            return false;
        } else {
            return true;
        }
    }

    protected function post()
    {
        if ($this->validatesValueIsNumeric($this->getRequest()->km_pav) && $this->validatesValueIsNumeric($this->getRequest()->km_npav)) {
            $id = $this->createOrUpdateRota();

            if (is_numeric($id)) {
                $this->messenger->append('Cadastro realizado com sucesso', 'success', false, 'error');
            } else {
                $this->messenger->append('Aparentemente a rota não pode ser cadastrada, por favor, verifique.');
            }

            return ['id' => $id];
        } else {
            $this->messenger->append('Os dados para Km devem ser númericos (ex: 23.3 ou 55)');
        }
    }

    protected function put()
    {
        $id = $this->getRequest()->id;
        $editou = $this->createOrUpdateRota($id);

        if ($editou) {
            $this->messenger->append('Alteração realizada com sucesso', 'success', false, 'error');
        } else {
            $this->messenger->append('Aparentemente a rota não pode ser alterado, por favor, verifique.');
        }

        return ['id' => $id];
    }

    protected function delete()
    {
        $id = $this->getRequest()->id;

        $itinerario = new clsModulesItinerarioTransporteEscolar();
        $itinerario->excluirTodos($id);

        $rota = new clsModulesRotaTransporteEscolar();
        $rota->cod_rota_transporte_escolar = $id;

        if ($rota->excluir()) {
            $this->messenger->append('Cadastro removido com sucesso', 'success', false, 'error');
        } else {
            $this->messenger->append(
                'Aparentemente o cadastro não pode ser removido, por favor, verifique.',
                'error',
                false,
                'error'
            );
        }

        return ['id' => $id];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'rota')) {
            $this->appendResponse($this->get());
        } elseif ($this->isRequestFor('get', 'rota-search')) {
            $this->appendResponse($this->search());
        }

        // create
        elseif ($this->isRequestFor('post', 'rota')) {
            $this->appendResponse($this->post());
        }

        // update
        elseif ($this->isRequestFor('put', 'rota')) {
            $this->appendResponse($this->put());
        } elseif ($this->isRequestFor('delete', 'rota')) {
            if ($this->validateIfRotaIsNotInUse()) {
                $this->appendResponse($this->delete());
                echo '<script language= "JavaScript">
                    location.href="intranet/transporte_rota_lst.php";
                    </script>';

                die();
            }
        } else {
            $this->notImplementedOperationError();
        }
    }
}
