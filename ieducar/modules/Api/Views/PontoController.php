<?php

use App\Models\Place;
use iEducar\Modules\Addressing\LegacyAddressingFields;

class PontoController extends ApiCoreController
{
    use LegacyAddressingFields;

    protected $_processoAp = 578; //verificar
    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA; // verificar

    protected function createOrUpdatePonto($id = null)
    {
        $ponto = new clsModulesPontoTransporteEscolar();
        $ponto->cod_ponto_transporte_escolar = $id;

        $detalhe = $ponto->detalhe();

        if ($detalhe) {
            $place = Place::query()->find($detalhe['idlog']);
        }

        // após cadastro não muda mais id pessoa
        $ponto->descricao = $this->getRequest()->desc;

        $ponto->latitude = $this->getRequest()->latitude;
        $ponto->longitude = $this->getRequest()->longitude;
        $cep = idFederal2Int($this->getRequest()->postal_code);

        $this->address = $this->getRequest()->address;
        $this->number = $this->getRequest()->number;
        $this->complement = $this->getRequest()->complement;
        $this->neighborhood = $this->getRequest()->neighborhood;
        $this->postal_code = $this->getRequest()->postal_code;
        $this->city_id = $this->getRequest()->city_id;

        if (empty($place)) {
            $place = new Place();
        }

        $place->fill([
            'address' => $this->address,
            'number' => $this->number ?: null,
            'complement' => $this->complement,
            'neighborhood' => $this->neighborhood,
            'city_id' => $this->city_id,
            'postal_code' => idFederal2int($this->postal_code),
        ]);
        $place->save();

        $ponto->cep = $cep;
        $ponto->idbai = $place->getKey();
        $ponto->idlog = $place->getKey();
        $ponto->numero = $this->number;
        $ponto->complemento = $this->complement;

        return (is_null($id) ? $ponto->cadastra() : $ponto->edita());
    }

    protected function get()
    {
        $id = $this->getRequest()->id;
        $ponto = new clsModulesPontoTransporteEscolar();
        $ponto->cod_ponto_transporte_escolar = $id;
        $ponto = $ponto->detalhe();

        $attrs = [
            'cod_ponto_transporte_escolar' => 'id',
            'descricao' => 'desc',
            'cep' => 'cep',
            'idlog' => 'idlog',
            'idbai' => 'idbai',
            'numero' => 'numero',
            'complemento' => 'complemento',
            'bairro' => 'bairro',
            'distrito' => 'distrito',
            'iddis' => 'iddis',
            'logradouro' => 'logradouro',
            'idtlog' => 'idtlog',
            'zona_localizacao' => 'zona_localizacao',
            'sigla_uf' => 'sigla_uf',
            'municipio' => 'municipio',
            'idmun' => 'idmun',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
        ];

        $pt = Portabilis_Array_Utils::filter($ponto, $attrs);
        $pt['desc'] = Portabilis_String_Utils::toUtf8($pt['desc']);

        $pt['bairro'] = $this->toUtf8($pt['bairro']);
        $pt['distrito'] = $this->toUtf8($pt['distrito']);
        $pt['logradouro'] = $this->toUtf8($pt['logradouro']);

        $pt['municipio'] = $this->toUtf8($pt['municipio']);
        $pt['sigla_uf'] = $this->toUtf8($pt['sigla_uf']);

        $pt['cep'] = int2CEP($pt['cep']);

        return $pt;
    }

    protected function validateIfPontoIsNotInUse()
    {
        $itinerario = new clsModulesItinerarioTransporteEscolar();
        $lista = $itinerario->lista(null, null, null, null, null, $this->getRequest()->id);

        if (is_array($lista) && count($lista)>0) {
            $this->messenger->append(
                'Não é possível excluir um ponto que está vinculado a um itinerário.',
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
        if (!($this->getRequest()->cep_ && is_numeric($this->getRequest()->bairro_id) && is_numeric($this->getRequest()->logradouro_id))) {
            $this->normalizaEndereco();
        }

        $id = $this->createOrUpdatePonto();
        if (is_numeric($id)) {
            $this->messenger->append('Cadastro realizado com sucesso', 'success', false, 'error');
        } else {
            $this->messenger->append('Aparentemente o ponto não pode ser cadastrada, por favor, verifique.');
        }

        return ['id' => $id];
    }

    protected function sqlsForNumericSearch()
    {
        $sqls[] = 'select distinct cod_ponto_transporte_escolar as id, descricao as name from
            modules.ponto_transporte_escolar where cod_ponto_transporte_escolar::varchar like $1||\'%\'';

        return $sqls;
    }

    protected function sqlsForStringSearch()
    {
        $sqls[] = 'select distinct cod_ponto_transporte_escolar as id, descricao as name  from
            modules.ponto_transporte_escolar where lower((descricao)) like \'%\'||lower(($1))||\'%\'';

        return $sqls;
    }

    protected function put()
    {
        $id = $this->getRequest()->id;

        if (!($this->getRequest()->cep_ && is_numeric($this->getRequest()->bairro_id) && is_numeric($this->getRequest()->logradouro_id))) {
            $this->normalizaEndereco();
        }

        $editou = $this->createOrUpdatePonto($id);

        if ($editou) {
            $this->messenger->append('Alteração realizada com sucesso', 'success', false, 'error');
        } else {
            $this->messenger->append('Aparentemente o ponto não pode ser alterado, por favor, verifique.');
        }

        return ['id' => $id];
    }

    protected function delete()
    {
        $id = $this->getRequest()->id;
        $pessoas = new clsModulesPessoaTransporte();
        $lista = $pessoas->lista(null, null, null, $id);

        foreach ($lista as $registro) {
            $editaPessoa = new clsModulesPessoaTransporte(
                $registro['cod_pessoa_transporte'],
                $registro['ref_cod_rota_transporte_escolar'],
                $registro['ref_idpes'],
                null,
                $registro['ref_idpes_destino'],
                $registro['observacao']
            );

            $editaPessoa->edita();
        }

        $ponto = new clsModulesPontoTransporteEscolar();
        $ponto->cod_ponto_transporte_escolar = $id;

        if ($ponto->excluir()) {
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

    protected function normalizaEndereco()
    {
        // TODO Addressing
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'ponto')) {
            $this->appendResponse($this->get());
        } elseif ($this->isRequestFor('get', 'ponto-search')) {
            $this->appendResponse($this->search());
        }

        // create
        elseif ($this->isRequestFor('post', 'ponto')) {
            $this->appendResponse($this->post());
        }

        // update
        elseif ($this->isRequestFor('put', 'ponto')) {
            $this->appendResponse($this->put());
        } elseif ($this->isRequestFor('delete', 'ponto')) {
            if ($this->validateIfPontoIsNotInUse()) {
                $this->appendResponse($this->delete());
                echo '<script language= "JavaScript">
                    location.href="intranet/transporte_ponto_lst.php";
                    </script>';

                die();
            }
        } else {
            $this->notImplementedOperationError();
        }
    }
}
